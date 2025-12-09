<?php

declare(strict_types=1);

namespace App\Models;

class Endpoint
{
    protected string $id;
    protected EndpointStatus $status;
    protected Coding $connectionType;
    protected string $managingOrgId;
    protected ?Period $period;
    protected string $address;
    /**
     * @var array<int, array<string, string>> $payloadType
     */
    protected array $payloadType = [];

    /**
     * @param array<int, array<string, mixed>> $payloadType
     */
    public function __construct(
        string $id,
        string $address,
        string $managingOrgId,
        EndpointStatus $status,
        Coding $connectionType,
        ?Period $period,
        array $payloadType = []
    ) {
        $this->id = $id;
        $this->address = $address;
        $this->managingOrgId = $managingOrgId;
        $this->status = $status;
        $this->connectionType = $connectionType;
        $this->period = $period;
        $this->payloadType = $payloadType;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getPayloadType(): array
    {
        return $this->payloadType;
    }

    /**
     * @param array<int, array<string, mixed>> $payloadType
     */
    public function setPayloadType(array $payloadType): void
    {
        $this->payloadType = $payloadType;
    }

    public function getPeriod(): ?Period
    {
        return $this->period;
    }

    public function setPeriod(?Period $period): void
    {
        $this->period = $period;
    }

    public function setConnectionType(Coding $connectionType): void
    {
        $this->connectionType = $connectionType;
    }

    public function getConnectionType(): Coding
    {
        return $this->connectionType;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getManagingOrgId(): string
    {
        return $this->managingOrgId;
    }

    public function getStatus(): EndpointStatus
    {
        return $this->status;
    }

    public function setStatus(EndpointStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Convert to FHIR array format
     *
     * @return array<string, mixed>
     */
    public function toFhir(): array
    {
        return [
            'resourceType' => 'Endpoint',
            'id' => $this->getId(),
            'status' => $this->getStatus()->value,
            'connectionType' => $this->getConnectionType()->toFhir(),
            'managingOrganization' => [
                'reference' => 'Organization/' . $this->getManagingOrgId(),
                'display' => $this->getManagingOrgId(),
            ],
            'payloadType' => $this->getPayloadType(),
            'address' => $this->getAddress(),
            'period' => $this->getPeriod() ? $this->getPeriod()->toFhir() : null,
        ];
    }

    /**
     * Create Endpoint from FHIR array data
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromFhir(array $data): self
    {
        $id = $data['id'] ?? '';
        $address = $data['address'] ?? '';

        // Parse managing organization reference
        $managingOrgId = '';
        if (isset($data['managingOrganization']['reference'])) {
            // Extract ID from reference like "Organization/123"
            $reference = $data['managingOrganization']['reference'];
            if (str_starts_with($reference, 'Organization/')) {
                $managingOrgId = substr($reference, 13); // Remove "Organization/" prefix
            } else {
                $managingOrgId = $reference; // Use as-is if not in expected format
            }
        } elseif (isset($data['managingOrganization']['display'])) {
            $managingOrgId = $data['managingOrganization']['display'];
        }

        // Parse status
        $status = EndpointStatus::ACTIVE; // Default value
        if (isset($data['status'])) {
            $status = EndpointStatus::from($data['status']);
        }

        // Parse connection type
        $connectionType = new Coding(
            system: 'http://hl7.org/fhir/ValueSet/endpoint-connection-type',
            code: 'hl7-fhir-rest',
            display: 'HL7 FHIR'
        ); // Default value
        if (isset($data['connectionType'])) {
            $connectionType = Coding::fromFhir($data['connectionType']);
        }

        // Parse period
        $period = null;
        if (isset($data['period'])) {
            $period = Period::fromFhir($data['period']);
        }

        // Parse payload type
        $payloadType = [];
        if (isset($data['payloadType']) && is_array($data['payloadType'])) {
            $payloadType = $data['payloadType'];
        }

        return new self(
            $id,
            $address,
            $managingOrgId,
            $status,
            $connectionType,
            $period,
            $payloadType
        );
    }
}
