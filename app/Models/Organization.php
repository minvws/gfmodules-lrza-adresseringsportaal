<?php

declare(strict_types=1);

namespace App\Models;

class Organization
{
    protected string $id;
    protected string $name;
    protected string $system;
    protected string $identifier;
    /** @var Endpoint[] */
    protected array $endpoints = [];

    /**
     * @param string $id
     * @param string $name
     * @param string $system
     * @param string $identifier
     * @param Endpoint[] $endpoints
     */
    public function __construct(string $id, string $name, string $system, string $identifier, array $endpoints)
    {
        $this->id = $id;
        $this->name = $name;
        $this->system = $system;
        $this->identifier = $identifier;
        $this->endpoints = $endpoints;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSystem(): string
    {
        return $this->system;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return Endpoint[]
     */
    public function getEndpoints(): array
    {
        return $this->endpoints;
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
    }

    public function addEndpoint(Endpoint $endpoint): void
    {
        // Update if endpoint already exists, otherwise add
        foreach ($this->endpoints as $key => $existingEndpoint) {
            if ($existingEndpoint->getId() === $endpoint->getId()) {
                $this->endpoints[$key] = $endpoint;
                return;
            }
        }

        $this->endpoints[] = $endpoint;
    }

    public function removeEndpoint(Endpoint $endpoint): void
    {
        $this->endpoints = array_filter($this->endpoints, fn($e) => $e->getId() !== $endpoint->getId());
    }

    /**
     * @return array<string, mixed>
     */
    public function toFhir(): array
    {
        $endpoints = [];
        foreach ($this->getEndpoints() as $endpoint) {
            $endpoints[] = [
                'reference' => 'Endpoint/' . $endpoint->getId(),
                'display' => $endpoint->getAddress(),
            ];
        }

        return [
            'resourceType' => 'Organization',
            'id' => $this->getId(),
            'active' => true,
            'identifier' => [
                [
                    'system' => $this->getSystem(),
                    'value' => $this->getIdentifier(),
                ],
            ],
            'name' => $this->getName(),
            'endpoint' => $endpoints,
        ];
    }
}
