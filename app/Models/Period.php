<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;
use DateTimeInterface;

class Period
{
    protected ?DateTime $start;
    protected ?DateTime $end;

    public function __construct(?DateTimeInterface $start = null, ?DateTimeInterface $end = null)
    {
        $this->start = $start ? DateTime::createFromInterface($start) : null;
        $this->end = $end ? DateTime::createFromInterface($end) : null;
    }

    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    public function setStart(?DateTimeInterface $start): void
    {
        $this->start = $start ? DateTime::createFromInterface($start) : null;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function setEnd(?DateTimeInterface $end): void
    {
        $this->end = $end ? DateTime::createFromInterface($end) : null;
    }

    /**
     * Create Period from FHIR array data
     * @param array<string, mixed> $data
     */
    public static function fromFhir(array $data): self
    {
        $start = null;
        $end = null;

        if (isset($data['start'])) {
            $start = new DateTime($data['start']);
        }

        if (isset($data['end'])) {
            $end = new DateTime($data['end']);
        }

        return new self($start, $end);
    }

    /**
     * Create Period from date strings
     */
    public static function fromStrings(?string $start = null, ?string $end = null): self
    {
        $startDate = $start ? new DateTime($start) : null;
        $endDate = $end ? new DateTime($end) : null;

        return new self($startDate, $endDate);
    }

    /**
     * Convert to FHIR array format
     *
     * @return array<string, mixed>
     */
    public function toFhir(): array
    {
        $result = [];

        if ($this->start !== null) {
            $result['start'] = $this->start->format('c'); // ISO 8601 format
        }

        if ($this->end !== null) {
            $result['end'] = $this->end->format('c'); // ISO 8601 format
        }

        return $result;
    }

    /**
     * Check if the Period has any data
     */
    public function isEmpty(): bool
    {
        return $this->start === null && $this->end === null;
    }

    /**
     * Check if the Period is valid (has at least start or end)
     */
    public function isValid(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Check if the period is currently active (current time is within the period)
     */
    public function isActive(?DateTime $now = null): bool
    {
        if ($now === null) {
            $now = new DateTime();
        }

        // If start is set and current time is before start, not active
        if ($this->start !== null && $now < $this->start) {
            return false;
        }

        // If end is set and current time is after end, not active
        if ($this->end !== null && $now > $this->end) {
            return false;
        }

        // If we have at least one boundary and we're within it, it's active
        return $this->isValid();
    }

    /**
     * Get the duration of the period in seconds
     */
    public function getDurationInSeconds(): ?int
    {
        if ($this->start === null || $this->end === null) {
            return null;
        }

        return $this->end->getTimestamp() - $this->start->getTimestamp();
    }

    /**
     * Check if this period overlaps with another period
     */
    public function overlapsWith(Period $other): bool
    {
        // If either period is empty, no overlap
        if ($this->isEmpty() || $other->isEmpty()) {
            return false;
        }

        // Get effective start and end for both periods
        $thisStart = $this->start;
        $thisEnd = $this->end;
        $otherStart = $other->getStart();
        $otherEnd = $other->getEnd();

        // If no start is defined, treat as infinite past
        // If no end is defined, treat as infinite future

        // Check for non-overlap conditions
        if ($thisEnd !== null && $otherStart !== null && $thisEnd < $otherStart) {
            return false; // This period ends before other starts
        }

        if ($otherEnd !== null && $thisStart !== null && $otherEnd < $thisStart) {
            return false; // Other period ends before this starts
        }

        return true; // Periods overlap
    }
}
