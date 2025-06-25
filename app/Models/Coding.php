<?php

declare(strict_types=1);

namespace App\Models;

class Coding
{
    protected ?string $system;
    protected ?string $code;
    protected ?string $display;

    public function __construct(?string $system = null, ?string $code = null, ?string $display = null)
    {
        if ($system === null && $code === null && $display === null) {
            throw new \InvalidArgumentException('At least one of system, code, or display must be provided');
        }

        $this->system = $system;
        $this->code = $code;
        $this->display = $display;
    }

    public function getSystem(): ?string
    {
        return $this->system;
    }

    public function setSystem(?string $system): void
    {
        $this->system = $system;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getDisplay(): ?string
    {
        return $this->display;
    }

    public function setDisplay(?string $display): void
    {
        $this->display = $display;
    }

    /**
     * Create CodeableConcept from FHIR array data
     * @param array<string, mixed> $data
     */
    public static function fromFhir(array $data): self
    {
        return new self(
            $data['system'] ?? null,
            $data['code'] ?? null,
            $data['display'] ?? null
        );
    }

    /**
     * Convert to FHIR array format
     *
     * @return array<string, mixed>
     */
    public function toFhir(): array
    {
        $result = [];

        if ($this->system !== null) {
            $result['system'] = $this->system;
        }

        if ($this->code !== null) {
            $result['code'] = $this->code;
        }

        if ($this->display !== null) {
            $result['display'] = $this->display;
        }

        return $result;
    }
}
