<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\HapiHttpException;
use App\Exceptions\InvalidResponseException;
use App\Exceptions\OrganizationNotFoundException;
use App\Models\Endpoint;
use App\Models\Organization;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Psr\Http\Message\ResponseInterface;

class HapiService
{
    protected Client $client;
    protected string $endpoint;

    public const string SYSTEM_KVK = 'http://www.vzvz.nl/fhir/NamingSystem/kvk';

    public function __construct(string $endpoint)
    {
        $this->endpoint = $endpoint;
        // Ensure base_uri ends with a trailing slash for proper path appending
        $baseUri = rtrim($endpoint, '/') . '/';
        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => 5.0,
            'connect_timeout' => 2.0,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Make HTTP request and catch connection exceptions
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $endpoint The endpoint path
     * @param array<string, mixed>|null $data Optional JSON data
     * @return ResponseInterface
     * @throws \Exception
     */
    public function makeRequest(string $method, string $endpoint, ?array $data = null): ResponseInterface
    {
        $options = [];
        if ($data !== null) {
            $options['json'] = $data;
        }

        try {
            return match (strtoupper($method)) {
                'GET' => $this->client->get($endpoint, $options),
                'POST' => $this->client->post($endpoint, $options),
                'PUT' => $this->client->put($endpoint, $options),
                'DELETE' => $this->client->delete($endpoint, $options),
                default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}")
            };
        } catch (ConnectException $e) {
            throw new HapiHttpException(
                "Cannot connect to server at '{$this->endpoint}'. " .
                "Error: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function findOrganizationByIdentifier(string $system, string $id): Organization
    {
        $queryParams = [
            'identifier' => $system . '|' . $id,
            '_include' => 'Organization:endpoint',
        ];
        $endpoint = 'Organization?' . http_build_query($queryParams);
        $response = $this->makeRequest('GET', $endpoint);

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
            throw InvalidResponseException::create(
                'Invalid response structure: please wait till the HAPI server is ready'
            );
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
            identifiers: [
                [
                    'system' => $system,
                    'value' => $id,
                ],
            ],
            endpoint: null
        );
        $response = $this->makeRequest('PUT', 'Organization/' . $org->getId(), $org->toFhir());
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
        $response = $this->makeRequest('PUT', "Organization/{$organization->getId()}", $organization->toFhir());
        if ($response->getStatusCode() >= 400) {
            throw HapiHttpException::create(
                $response->getStatusCode(),
                (string)$response->getBody()
            );
        }
    }

    public function updateEndpoint(Endpoint $endpoint): void
    {
        $response = $this->makeRequest('PUT', "Endpoint/{$endpoint->getId()}", $endpoint->toFhir());
        if ($response->getStatusCode() >= 400) {
            throw HapiHttpException::create(
                $response->getStatusCode(),
                (string)$response->getBody()
            );
        }
    }

    public function deleteEndpoint(string $endpointId): void
    {
        $response = $this->makeRequest('DELETE', "Endpoint/{$endpointId}");
        if ($response->getStatusCode() >= 400) {
            throw HapiHttpException::create(
                $response->getStatusCode(),
                (string)$response->getBody()
            );
        }
    }

    public function deleteOrganization(string $organizationId): void
    {
        $response = $this->makeRequest('DELETE', "Organization/{$organizationId}");
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
        $response = $this->makeRequest('GET', "Organization/{$organizationId}");
        if ($response->getStatusCode() !== 200) {
            throw HapiHttpException::create(
                $response->getStatusCode(),
                (string)$response->getBody()
            );
        }
    }

    public function checkIfEndpointRegistered(string $endpointId): void
    {
        $response = $this->makeRequest('GET', "Endpoint/{$endpointId}");
        if ($response->getStatusCode() !== 200) {
             throw HapiHttpException::create(
                 $response->getStatusCode(),
                 (string)$response->getBody()
             );
        }
    }
}
