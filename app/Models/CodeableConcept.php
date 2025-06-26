<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Coding;

class CodeableConcept
{
    /**
     * @var Coding[]
     */
    protected array $coding;

    /**
     * @param Coding[] $coding
     */
    public function __construct(array $coding)
    {
        $this->coding = $coding;
    }

    /**
     * @return Coding[]
     */
    public function getCoding(): array
    {
        return $this->coding;
    }

    /**
     * @param Coding[] $coding
     */
    public function setCoding(array $coding): void
    {
        $this->coding = $coding;
    }

    /**
     * Create CodeableConcept from FHIR array data
     * @param array<string, mixed> $data
     */
    public static function fromFhir(array $data): self
    {
        $coding = isset($data['coding']) ? array_map(fn($c) => Coding::fromFhir($c), $data['coding']) : [];

        return new self($coding);
    }
    /**
     * Convert to FHIR array format
     *
     * @return array<string, mixed>
     */
    public function toFhir(): array
    {
        return [
            'coding' => array_map(fn(Coding $c) => $c->toFhir(), $this->coding),
        ];
    }
}
