<?php

declare(strict_types=1);

namespace App\Models;

enum ContactPointUse: string
{
    case WORK = 'work';
    case TEMP = 'temp';
    case OLD = 'old';
    case MOBILE = 'mobile';

    /**
     * Get the display name for the contact point use
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::WORK => 'Work',
            self::TEMP => 'Temporary',
            self::OLD => 'Old',
            self::MOBILE => 'Mobile',
        };
    }

    /**
     * Get all contact point uses as an associative array
     * @return array<string, string>
     */
    public static function getAllAsArray(): array
    {
        $uses = [];
        foreach (self::cases() as $use) {
            $uses[$use->value] = $use->getDisplayName();
        }
        return $uses;
    }
}
