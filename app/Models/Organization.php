<?php

declare(strict_types=1);

namespace App\Models;

class Organization
{
    protected string $id;
    protected bool $active = true;
    protected string $name;
    protected string $identifierSystem;
    protected string $identifierValue;
    protected ?Endpoint $endpoint = null;
    protected ?ContactPoint $telecom = null;

    /**
     * @param string $id
     * @param string $name
     * @param string $identifierSystem
     * @param string $identifierValue
     * @param ?Endpoint $endpoint
     * @param ?ContactPoint $telecom
     */
    public function __construct(
        string $id,
        string $name,
        string $identifierSystem,
        string $identifierValue,
        ?Endpoint $endpoint = null,
        ?ContactPoint $telecom = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->identifierSystem = $identifierSystem;
        $this->identifierValue = $identifierValue;
        $this->endpoint = $endpoint;
        $this->telecom = $telecom;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentifierSystem(): string
    {
        return $this->identifierSystem;
    }

    public function getidentifierValue(): string
    {
        return $this->identifierValue;
    }

    /**
     * @return Endpoint|null
     */
    public function getEndpoint(): ?Endpoint
    {
        return $this->endpoint;
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
    }

    public function setEndpoint(?Endpoint $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function removeEndpoint(): void
    {
        $this->endpoint = null;
    }

    /**
     * @return ?ContactPoint
     */
    public function getTelecom(): ?ContactPoint
    {
        return $this->telecom;
    }

    /**
     * @param ?ContactPoint $telecom
     */
    public function setTelecom(?ContactPoint $telecom): void
    {
        $this->telecom = $telecom;
    }

    /**
     * Clear telecom contact point
     */
    public function clearTelecom(): void
    {
        $this->telecom = null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toFhir(): array
    {
        $fhirEndpoint = [];
        $endpoint = $this->getEndpoint();
        if ($endpoint !== null) {
            $fhirEndpoint = [
                'reference' => 'Endpoint/' . $endpoint->getId(),
                'display' => $endpoint->getAddress(),
            ];
        }

        return [
            'resourceType' => 'Organization',
            'id' => $this->getId(),
            'active' => $this->active,
            'name' => $this->getName(),
            'identifier' => [
                [
                    'system' => $this->getIdentifierSystem(),
                    'value' => $this->getidentifierValue(),
                ],
            ],
            'telecom' => $this->getTelecom() ? [$this->getTelecom()->toFhir()] : [],
            'endpoint' => $fhirEndpoint,
        ];
    }

    /**
     * Create Organization from FHIR array data
     *
     * @param array<string, mixed> $organizationData
     * @param array<string, mixed> $endpointData
     *
     * @return self
     */
    public static function fromFhir(array $organizationData, ?array $endpointData): self
    {
        $id = $organizationData['id'] ?? '';
        $name = $organizationData['name'] ?? '';

        // Parse type array
        $type = [];
        if (isset($organizationData['type']) && is_array($organizationData['type'])) {
            foreach ($organizationData['type'] as $typeData) {
                $type[] = CodeableConcept::fromFhir($typeData);
            }
        }

        // Parse identifier to get endpoint identifier system and value
        $identifierSystem = '';
        $identifierValue = '';
        if (isset($organizationData['identifier']) && is_array($organizationData['identifier'])) {
            foreach ($organizationData['identifier'] as $identifier) {
                if (isset($identifier['system']) && isset($identifier['value'])) {
                    $identifierSystem = $identifier['system'];
                    $identifierValue = $identifier['value'];
                    break; // Use the first identifier found
                }
            }
        }

        $endpoint = $endpointData ? Endpoint::fromFhir($endpointData) : null;

        // Parse telecom (take first contact point only)
        $telecom = null;
        if (
            isset($organizationData['telecom']) && is_array($organizationData['telecom'])
            && !empty($organizationData['telecom'])
        ) {
            $telecom = ContactPoint::fromFhir($organizationData['telecom'][0]);
        }

        return new self(
            $id,
            $name,
            $identifierSystem,
            $identifierValue,
            $endpoint,
            $telecom
        );
    }
}
