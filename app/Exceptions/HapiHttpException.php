<?php

declare(strict_types=1);

namespace App\Exceptions;

class HapiHttpException extends HapiException
{
    public static function create(int $statusCode, string $responseBody = ''): self
    {
        return new self("HTTP request failed with status code {$statusCode}: {$responseBody}");
    }
}
