<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\HapiHttpException;
use App\Exceptions\InvalidResponseException;
use App\Exceptions\OrganizationNotFoundException;
use App\Models\Endpoint;
use App\Models\Organization;
use GuzzleHttp\Client;

class HapiService
{
    protected Client $client;

    public const string SYSTEM_KVK = 'http://www.vzvz.nl/fhir/NamingSystem/kvk';

    public function __construct(string $endpoint)
    {
        $this->client = new Client([
            'base_uri' => $endpoint,
            'timeout'  => 5.0,
            'connect_timeout' => 2.0,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }


    public function findOrganizationByIdentifier(string $system, string $id): Organization
    {
        $response = $this->client->get("/fhir/Organization", [
            'query' => [
                'identifier' => $system . '|' . $id,
                '_include' => 'Organization:endpoint',
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw HapiHttpException::create(
                $response->getStatusCode(),
                (string)$response->getBody()
            );
        }

        $responseBody = (string)$response->getBody();
        $data = json_decode($responseBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw InvalidResponseException::create('Invalid JSON');
        }

        if (!isset($data['total']) || $data['total'] === 0) {
            throw OrganizationNotFoundException::create($system, $id);
        }

        if (!isset($data['entry']) || !is_array($data['entry'])) {
            throw InvalidResponseException::create('Invalid response structure');
        }

        $endpoint = null;
        foreach ($data['entry'] as $entry) {
            if ($entry['resource']['resourceType'] == 'Endpoint') {
                $endpoint = $entry['resource'];
                break;
            }
        }

        $orgEntry = null;
        foreach ($data['entry'] as $entry) {
            if ($entry['resource']['resourceType'] == 'Organization') {
                $orgEntry = $entry['resource'];
                break;
            }
        }
        if ($orgEntry === null) {
            throw OrganizationNotFoundException::create($system, $id);
        }

        return Organization::fromFhir(
            $orgEntry,
            $endpoint
        );
    }

    public function createOrganization(string $system, string $id): Organization
    {
        $org = new Organization(
            id: 'Org-' . $id,
            name: 'Org-' . $id,
            identifierSystem: $system,
            identifierValue: $id,
            endpoint: null
        );
        $response = $this->client->put('/fhir/Organization/' . $org->getId(), [
            'json' => $org->toFhir(),
        ]);
        if ($response->getStatusCode() >= 400) {
            throw HapiHttpException::create(
                $response->getStatusCode(),
                (string)$response->getBody()
            );
        }
        return $org;
    }

    public function updateOrganization(Organization $organization): void
    {
        $response = $this->client->put("/fhir/Organization/{$organization->getId()}", [
            'json' => $organization->toFhir(),
        ]);
        if ($response->getStatusCode() >= 400) {
            throw HapiHttpException::create(
                $response->getStatusCode(),
                (string)$response->getBody()
            );
        }
    }

    public function createEndpoint(string $organizationId): void
    {
        $endpoint = new Endpoint(
            id: \Ramsey\Uuid\Uuid::uuid4()->toString(),
            address: 'http://example.com/fhir/',
            managingOrgId: $organizationId,
            status: \App\Models\EndpointStatus::ACTIVE,
            connectionType: new \App\Models\Coding(
                system: 'http://hl7.org/fhir/ValueSet/endpoint-connection-type',
                code: 'hl7-fhir-rest',
                display: 'HL7 FHIR'
            ),
            period: null,
        );
        $response = $this->client->put('/fhir/Endpoint', [
            'json' => $endpoint->toFhir(),
        ]);
        if ($response->getStatusCode() >= 400) {
            throw HapiHttpException::create(
                $response->getStatusCode(),
                (string)$response->getBody()
            );
        }
    }

    public function updateEndpoint(Endpoint $endpoint): void
    {
        $response = $this->client->put("/fhir/Endpoint/{$endpoint->getId()}", [
            'json' => $endpoint->toFhir(),
        ]);
        if ($response->getStatusCode() >= 400) {
            throw HapiHttpException::create(
                $response->getStatusCode(),
                (string)$response->getBody()
            );
        }
    }

    public function deleteEndpoint(string $endpointId): void
    {
        $response = $this->client->delete("/fhir/Endpoint/{$endpointId}");
        if ($response->getStatusCode() >= 400) {
            throw HapiHttpException::create(
                $response->getStatusCode(),
                (string)$response->getBody()
            );
        }
    }

    public function deleteOrganization(string $organizationId): void
    {
        $response = $this->client->delete("/fhir/Organization/{$organizationId}");
        if ($response->getStatusCode() >= 400) {
            throw HapiHttpException::create(
                $response->getStatusCode(),
                (string)$response->getBody()
            );
        }
    }

    public function deleteOrganizationWithEndpoint(Organization $organization): void
    {
        $this->checkIfOrganizationRegistered($organization->getId());
        if ($organization->getEndpoint() !== null) {
            $this->checkIfEndpointRegistered($organization->getEndpoint()->getId());

            $endpoint = $organization->getEndpoint();

            $organization->setEndpoint(null);
            $this->updateOrganization($organization);

            $this->deleteEndpoint($endpoint->getId());
        }

        $this->deleteOrganization($organization->getId());
    }

    public function deleteEndpointFromOrganization(Organization $organization): void
    {
        $this->checkIfOrganizationRegistered($organization->getId());
        if ($organization->getEndpoint() !== null) {
            $endpoint = $organization->getEndpoint();
            $this->checkIfEndpointRegistered($endpoint->getId());

            $organization->setEndpoint(null);
            $this->updateOrganization($organization);

            $this->deleteEndpoint($endpoint->getId());
        }
    }

    /**
     * Functions to check if the HAPI registered the changes
     * Sometimes the HAPI server is a little slow to act.
     * These functions check if organization or endpoint actually are registered.
     */
    public function checkIfOrganizationRegistered(string $organizationId): void
    {
        $response = $this->client->get("/fhir/Organization/{$organizationId}");
        if ($response->getStatusCode() !== 200) {
            throw HapiHttpException::create(
                $response->getStatusCode(),
                (string)$response->getBody()
            );
        }
    }

    public function checkIfEndpointRegistered(string $endpointId): void
    {
        $response = $this->client->get("/fhir/Endpoint/{$endpointId}");
        if ($response->getStatusCode() !== 200) {
             throw HapiHttpException::create(
                 $response->getStatusCode(),
                 (string)$response->getBody()
             );
        }
    }
}
