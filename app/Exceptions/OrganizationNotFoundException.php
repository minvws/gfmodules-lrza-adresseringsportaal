<?php

declare(strict_types=1);

namespace App\Exceptions;

class OrganizationNotFoundException extends HapiException
{
    public static function create(string $system, string $id): self
    {
        return new self("Organization not found with identifier {$system}|{$id}");
    }
}
