<?php

declare(strict_types=1);

namespace App\Models;

class Endpoint
{
    protected string $address;
    protected string $id;
    protected string $managingOrgId;

    public function __construct(string $id, string $address, string $managingOrgId)
    {
        $this->id = $id;
        $this->address = $address;
        $this->managingOrgId = $managingOrgId;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getManagingOrgId(): string
    {
        return $this->managingOrgId;
    }

    /**
     * @return array<string, mixed>
     */
    public function toFhir(): array
    {
        return [
            'resourceType' => 'Endpoint',
            'id' => $this->getId(),
            'status' => 'active',
            'managingOrganization' => [
                'reference' => 'Organization/' . $this->getManagingOrgId(),
            ],
            'address' => $this->getAddress(),
        ];
    }
}
