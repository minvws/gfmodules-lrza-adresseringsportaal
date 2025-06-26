<?php

declare(strict_types=1);

namespace App\Models;

enum ContactPointSystem: string
{
    case PHONE = 'phone';
    case FAX = 'fax';
    case EMAIL = 'email';
    case PAGER = 'pager';
    case URL = 'url';
    case SMS = 'sms';
    case OTHER = 'other';

    /**
     * Get the display name for the contact point system
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::PHONE => 'Phone',
            self::FAX => 'Fax',
            self::EMAIL => 'Email',
            self::PAGER => 'Pager',
            self::URL => 'URL',
            self::SMS => 'SMS',
            self::OTHER => 'Other',
        };
    }

    /**
     * Get all contact point systems as an associative array
     * @return array<string, string>
     */
    public static function getAllAsArray(): array
    {
        $systems = [];
        foreach (self::cases() as $system) {
            $systems[$system->value] = $system->getDisplayName();
        }
        return $systems;
    }
}
