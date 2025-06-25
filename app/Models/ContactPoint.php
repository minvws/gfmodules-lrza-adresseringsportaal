<?php

declare(strict_types=1);

namespace App\Models;

class ContactPoint
{
    protected ?ContactPointSystem $system = null;
    protected ?string $value = null;
    protected ?ContactPointUse $use = null;
    protected ?int $rank = null;
    protected ?Period $period = null;

    public function __construct(
        ?ContactPointSystem $system = null,
        ?string $value = null,
        ?ContactPointUse $use = null,
        ?int $rank = null,
        ?Period $period = null
    ) {
        $this->system = $system;
        $this->value = $value;
        $this->use = $use;
        $this->rank = $rank;
        $this->period = $period;
    }

    public function getSystem(): ?ContactPointSystem
    {
        return $this->system;
    }

    public function setSystem(?ContactPointSystem $system): void
    {
        $this->system = $system;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getUse(): ?ContactPointUse
    {
        return $this->use;
    }

    public function setUse(?ContactPointUse $use): void
    {
        $this->use = $use;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(?int $rank): void
    {
        $this->rank = $rank;
    }

    public function getPeriod(): ?Period
    {
        return $this->period;
    }

    public function setPeriod(?Period $period): void
    {
        $this->period = $period;
    }

    /**
     * Create ContactPoint from FHIR array data
     * @param array<string, mixed> $data
     */
    public static function fromFhir(array $data): self
    {
        $system = isset($data['system']) ? ContactPointSystem::from($data['system']) : null;
        $use = isset($data['use']) ? ContactPointUse::from($data['use']) : null;
        $period = isset($data['period']) ? Period::fromFhir($data['period']) : null;

        return new self(
            $system,
            $data['value'] ?? null,
            $use,
            isset($data['rank']) ? (int) $data['rank'] : null,
            $period
        );
    }

    /**
     * Convert ContactPoint to FHIR array format
     * @return array<string, mixed>
     */
    public function toFhir(): array
    {
        $fhir = [];

        if ($this->system !== null) {
            $fhir['system'] = $this->system->value;
        }

        if ($this->value !== null) {
            $fhir['value'] = $this->value;
        }

        if ($this->use !== null) {
            $fhir['use'] = $this->use->value;
        }

        if ($this->rank !== null) {
            $fhir['rank'] = $this->rank;
        }

        if ($this->period !== null) {
            $fhir['period'] = $this->period->toFhir();
        }

        return $fhir;
    }

    /**
     * Validate ContactPoint according to FHIR rules
     * Rule: A system is required if a value is provided.
     */
    public function isValid(): bool
    {
        // If a value is provided, a system is required
        if ($this->value !== null && $this->system === null) {
            return false;
        }

        // Rank must be positive if provided
        if ($this->rank !== null && $this->rank <= 0) {
            return false;
        }

        return true;
    }

    /**
     * Get validation errors
     * @return array<string>
     */
    public function getValidationErrors(): array
    {
        $errors = [];

        if ($this->value !== null && $this->system === null) {
            $errors[] = 'A system is required if a value is provided.';
        }

        if ($this->rank !== null && $this->rank <= 0) {
            $errors[] = 'Rank must be a positive integer.';
        }

        return $errors;
    }
}
