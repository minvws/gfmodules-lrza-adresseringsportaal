<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Endpoint;
use App\Models\OrganizationUser;
use App\Services\Eherkenning\OrganizationAuthGuard;
use App\Services\HapiService;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\Uid\Uuid;

class PortalController extends Controller
{
    protected HapiService $hapiService;
    protected Guard $guard;

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
            ->with('organization', $user->getOrganization())
        ;
    }

    public function edit(Request $request): RedirectResponse
    {
        $validated_data = $this->checkEndpoint($request);

        /** @var OrganizationUser $user */
        $user = $this->guard->user();

        // If there is no ID (new endpoint), generate a new UUID as identifier
        if (empty($validated_data['id'])) {
            $validated_data['id'] = (string)Uuid::v4();
        }

        $endpoint = new Endpoint(
            $validated_data['id'],
            $validated_data['endpoint'],
            $user->getOrganization()->getId(),
        );

        $organization = $user->getOrganization();
        $organization->addEndpoint($endpoint);
        $organization->updateName($validated_data['org_name']);

        $this->hapiService->updateEndpoint($endpoint);
        $this->hapiService->updateOrganization($organization);

        return redirect()
            ->route('portal.index')
            ->with('success', 'Organization information is updated successfully')
        ;
    }

    # @phpstan-ignore-next-line
    protected function checkEndpoint(Request $request): array
    {
        return $request->validate([
            'org_name' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
            'id' => [
                'string',
                'nullable',
                'min:0',
                'max:255',
                'regex:/^[a-zA-Z0-9\-]+$/',
            ],
            'endpoint' => [
                'required',
                'url',
                'max:1024',
                function ($attribute, $value, $fail) {
                    $prefixes = ["https://"];
                    if (config('app.allow_insecure_endpoints') === true) {
                        $prefixes[] = "http://";
                    }
                    if (!Str::startsWith(strtolower($value), $prefixes)) {
                        $fail($attribute . ' must start with https://');
                    }
                },
            ]
        ]);
    }
}
