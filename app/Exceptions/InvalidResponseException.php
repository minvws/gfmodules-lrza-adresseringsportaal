<?php

declare(strict_types=1);

namespace App\Exceptions;

class InvalidResponseException extends HapiException
{
    public static function create(string $reason = ''): self
    {
        return new self("Invalid response from HAPI server" . ($reason ? ": {$reason}" : ''));
    }
}
