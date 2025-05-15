<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Endpoint;
use App\Models\Organization;
use GuzzleHttp\Client;

class HapiService
{
    protected Client $client;

    public const SYSTEM_URA = 'http://fhir.nl/fhir/NamingSystem/ura';
    public const SYSTEM_KVK = 'http://www.vzvz.nl/fhir/NamingSystem/kvk';

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
            throw new \Exception('Error fetching data from HAPI: ' . $response->getBody());
        }

        $data = json_decode((string)$response->getBody(), true);
        if (isset($data['total']) && $data['total'] !== 1) {
            throw new \Exception('Organization not found in HAPI');
        }

        $endpoints = [];
        foreach ($data['entry'] as $entry) {
            if ($entry['resource']['resourceType'] == 'Endpoint') {
                $endpoints[] = new Endpoint(
                    $entry['resource']['id'] ?? '',
                    $entry['resource']['address'] ?? '',
                    $entry['resource']['managingOrganization']['reference'] ?? '',
                );
            }
        }

        $orgEntry = null;
        foreach ($data['entry'] as $entry) {
            if ($entry['resource']['resourceType'] == 'Organization') {
                $orgEntry = $entry;
                break;
            }
        }
        if (!$orgEntry) {
            throw new \Exception('Organization not found in HAPI');
        }

        return new Organization(
            $orgEntry['resource']['id'] ?? '',
            $orgEntry['resource']['name'] ?? '',
            $orgEntry['resource']['identifier'][0]['system'] ?? '',
            $orgEntry['resource']['identifier'][0]['value'] ?? '',
            $endpoints
        );
    }

    public function updateOrganization(Organization $organization): void
    {
        $response = $this->client->put("/fhir/Organization/{$organization->getId()}", [
            'json' => $organization->toFhir(),
        ]);
        if ($response->getStatusCode() >= 400) {
            throw new \Exception('Error updating organization in HAPI: ' . $response->getBody());
        }
    }

    public function updateEndpoint(Endpoint $endpoint): void
    {
        $response = $this->client->put("/fhir/Endpoint/{$endpoint->getId()}", [
            'json' => $endpoint->toFhir(),
        ]);
        if ($response->getStatusCode() >= 400) {
            throw new \Exception('Error updating endpoint in HAPI: ' . $response->getBody());
        }
    }
}
