<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EndpointRequest;
use App\Http\Requests\OrganizationRequest;
use App\Models\Endpoint;
use App\Models\EndpointConnectionTypes;
use App\Models\EndpointStatus;
use App\Models\OrganizationUser;
use App\Models\Period;
use App\Models\Coding;
use App\Services\Eherkenning\OrganizationAuthGuard;
use App\Services\HapiService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\Uid\Uuid;

class PortalController extends Controller
{
    protected HapiService $hapiService;
    protected OrganizationAuthGuard $guard;

    public function __construct(HapiService $hapiService, OrganizationAuthGuard $guard)
    {
        $this->hapiService = $hapiService;
        $this->guard = $guard;
    }

    public function index(): View
    {
        /** @var OrganizationUser $user */
        $user = $this->guard->user();

        return view('portals/index')
            ->with('organization', $user->getOrganization());
    }

    public function editOrganization(): View
    {
        /** @var OrganizationUser $user */
        $user = $this->guard->user();

        return view('portals/edit-organization')
            ->with('organization', $user->getOrganization());
    }

    public function updateOrganization(OrganizationRequest $request): RedirectResponse
    {
        $validated_data = $request->validated();

        /** @var OrganizationUser $user */
        $user = $this->guard->user();

        $organization = $user->getOrganization();
        $organization->setName($validated_data['org_name']);
        $organization->setUraIdentifier($validated_data['ura_identifier']);

        // Handle telecom contact point
        $telecom = $this->createContactPointFromValidatedData($validated_data);
        $organization->setTelecom($telecom);

        try {
            $this->hapiService->updateOrganization($organization);
        } catch (\Exception $e) {
            return redirect()
                ->route('portal.edit-organization')
                ->withInput()
                ->with('error', 'Failed to update organization: ' . $e->getMessage());
        }

        return redirect()
            ->route('portal.index')
            ->with('success', 'Organization information is updated successfully');
    }

    /**
     * Create ContactPoint from validated form data
     * @param array<string, mixed> $data
     */
    private function createContactPointFromValidatedData(array $data): ?\App\Models\ContactPoint
    {
        $telecomData = $data['telecom'] ?? [];

        if (empty($telecomData['system']) && empty($telecomData['value'])) {
            return null; // No contact point data provided
        }

        $system = !empty($telecomData['system']) ? \App\Models\ContactPointSystem::from($telecomData['system']) : null;
        $use = !empty($telecomData['use']) ? \App\Models\ContactPointUse::from($telecomData['use']) : null;
        $rank = !empty($telecomData['rank']) ? (int) $telecomData['rank'] : null;

        // Handle Period
        $period = null;
        if (!empty($telecomData['period']['start']) || !empty($telecomData['period']['end'])) {
            $start = !empty($telecomData['period']['start']) ? new \DateTime($telecomData['period']['start']) : null;
            $end = !empty($telecomData['period']['end']) ? new \DateTime($telecomData['period']['end']) : null;
            $period = new \App\Models\Period($start, $end);
        }

        return new \App\Models\ContactPoint(
            $system,
            $telecomData['value'] ?? null,
            $use,
            $rank,
            $period
        );
    }


    public function editEndpoint(): View
    {
        /** @var OrganizationUser $user */
        $user = $this->guard->user();

        $endpoint = $user->getOrganization()->getEndpoint();

        return view('portals/edit-endpoint')
            ->with('endpoint', $endpoint)
            ->with('statusOptions', EndpointStatus::cases())
            ->with('connectionTypeOptions', EndpointConnectionTypes::getConnectionTypes());
    }

    public function updateEndpoint(EndpointRequest $request): RedirectResponse
    {
        $validated_data = $request->validated();

        /** @var OrganizationUser $user */
        $user = $this->guard->user();

        // Create Period object from period-start and period-end fields
        $period = null;
        if (!empty($validated_data['period-start']) || !empty($validated_data['period-end'])) {
            $period = Period::fromStrings(
                $validated_data['period-start'] ?? null,
                $validated_data['period-end'] ?? null
            );
        }

        $organization = $user->getOrganization();
        $existingEndpoint = $organization->getEndpoint();
        $endpoint = new Endpoint( // Generate a new UUID if no old endpoint exists
            id: $existingEndpoint ? $existingEndpoint->getId() : (string)Uuid::v4(),
            address: $validated_data['address'],
            status: EndpointStatus::from($validated_data['status']),
            period: $period,
            connectionType: new Coding(
                system: "http://terminology.hl7.org/CodeSystem/endpoint-connection-type",
                code: $validated_data['connectionType'],
                display: EndpointConnectionTypes::getDisplayNameByCode($validated_data['connectionType'])
            ),
            managingOrgId: $organization->getId(),
            payloadType: [[
                'coding' => [[
                    'code' => config('fhir.default_endpoint_payloadtype_code'),
                    'display' => config('fhir.default_endpoint_payloadtype_display')
                ]],
                'text' => config('fhir.default_endpoint_payloadtype_text')
            ]]
        );
        $this->hapiService->updateEndpoint($endpoint);
        $organization->setEndpoint($endpoint);
        $this->hapiService->updateOrganization($organization);

        return redirect()
            ->route('portal.index')
            ->with('success', 'Endpoint information is updated successfully');
    }

    public function deleteEndpoint(): RedirectResponse
    {
        /** @var OrganizationUser $user */
        $user = $this->guard->user();
        $organization = $user->getOrganization();

        try {
            $this->hapiService->deleteEndpointFromOrganization($organization);

            return redirect()
                ->route('portal.index')
                ->with('success', 'Endpoint has been deleted successfully');
        } catch (\Exception $e) {
            return redirect()
                ->route('portal.index')
                ->with('error', 'Failed to delete endpoint: ' . $e->getMessage());
        }
    }

    public function deleteOrganization(): RedirectResponse
    {
        /** @var OrganizationUser $user */
        $user = $this->guard->user();
        $organization = $user->getOrganization();

        try {
            $this->hapiService->deleteOrganizationWithEndpoint($organization);

            // Since the organization is deleted, we should log out the user
            $this->guard->logout();

            return redirect()
                ->route('index')
                ->with('success', 'Organization and all associated data have been deleted successfully');
        } catch (\Exception $e) {
            return redirect()
                ->route('portal.index')
                ->with('error', 'Failed to delete organization: ' . $e->getMessage());
        }
    }
}
