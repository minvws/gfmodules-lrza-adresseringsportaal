<?php

declare(strict_types=1);

namespace App\Models;

class Organization
{
    public const SYSTEM_KVK = 'http://fhir.nl/fhir/NamingSystem/kvk';
    public const SYSTEM_URA = 'http://fhir.nl/fhir/NamingSystem/ura';

    protected string $id;
    protected bool $active = true;
    protected string $name;
    /**
     * @var array<int, array{system: string, value: string}>
    */
    protected array $identifiers = [];
    protected ?Endpoint $endpoint = null;
    protected ?ContactPoint $telecom = null;

    /**
     * @param string $id
     * @param string $name
     * @param array<int, array{system: string, value: string}> $identifiers
     * @param ?Endpoint $endpoint
     * @param ?ContactPoint $telecom
     */
    public function __construct(
        string $id,
        string $name,
        array $identifiers,
        ?Endpoint $endpoint = null,
        ?ContactPoint $telecom = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->identifiers = $identifiers;
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

    /**
     * Get all identifiers
     * @return array<int, array{system: string, value: string}>
     */
    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    /**
     * Get KVK identifier value
     */
    public function getKvkIdentifier(): ?string
    {
        foreach ($this->identifiers as $identifier) {
            if ($identifier['system'] === self::SYSTEM_KVK) {
                return $identifier['value'];
            }
        }
        return null;
    }

    /**
     * Get URA identifier value
     */
    public function getUraIdentifier(): ?string
    {
        foreach ($this->identifiers as $identifier) {
            if ($identifier['system'] === self::SYSTEM_URA) {
                return $identifier['value'];
            }
        }
        return null;
    }

    /**
     * Set URA identifier (can be updated)
     */
    public function setUraIdentifier(string $value): void
    {
        $found = false;
        foreach ($this->identifiers as &$identifier) {
            if ($identifier['system'] === self::SYSTEM_URA) {
                $identifier['value'] = $value;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $this->identifiers[] = [
                'system' => self::SYSTEM_URA,
                'value' => $value,
            ];
        }
    }

    /**
     * Set KVK identifier (only if not already set)
     */
    public function setKvkIdentifier(string $value): void
    {
        foreach ($this->identifiers as $identifier) {
            if ($identifier['system'] === self::SYSTEM_KVK) {
                return; // Immutable after creation
            }
        }
        $this->identifiers[] = [
            'system' => self::SYSTEM_KVK,
            'value' => $value,
        ];
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

        $identifiers = [];
        foreach ($this->getIdentifiers() as $identifier) {
            $identifiers[] = [
                'system' => $identifier['system'],
                'value' => $identifier['value'],
            ];
        }
        return [
            'resourceType' => 'Organization',
            'id' => $this->getId(),
            'active' => $this->active,
            'name' => $this->getName(),
            'identifier' => $identifiers,
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

        // Parse identifiers
        $identifiers = [];
        if (isset($organizationData['identifier']) && is_array($organizationData['identifier'])) {
            foreach ($organizationData['identifier'] as $identifier) {
                if (isset($identifier['system']) && isset($identifier['value'])) {
                    $identifiers[] = [
                        'system' => $identifier['system'],
                        'value' => $identifier['value'],
                    ];
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
            $identifiers,
            $endpoint,
            $telecom
        );
    }
}
