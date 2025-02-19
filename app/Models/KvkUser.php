<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\KvkNoKvkNumberException;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\Authenticatable;
use RuntimeException;

class KvkUser implements Authenticatable
{
    public string $email;
    public string $kvk_number;

    /**
     * @param string $kvk_number
     */
    public function __construct(string $kvk_number)
    {
        $this->kvk_number = $kvk_number;
        $this->email = $kvk_number . '@kvk.kvk';
    }

    /**
     * @param object{
     *     kvk_number: string,
     * } $oidcResponse
     * @throws KvkNoKvkNumberException
     */
    public static function deserializeFromObject(object $oidcResponse): ?KvkUser
    {
        $requiredKeys = ["kvk_number"];
        $missingKeys = [];
        foreach ($requiredKeys as $key) {
            if (!property_exists($oidcResponse, $key)) {
                $missingKeys[] = $key;
            }
        }
        if (count($missingKeys) > 0) {
            Log::error("Kvk user missing required fields: " . implode(", ", $missingKeys));
            throw new KvkNoKvkNumberException();
        }

        return new KvkUser($oidcResponse->kvk_number);
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->kvk_number;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return $this->kvk_number;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifier(): string
    {
        return $this->kvk_number;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        throw new RuntimeException("Kvk users can't have a password");
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken(): string
    {
        throw new RuntimeException("Do not remember cookie's");
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value): void
    {
        throw new RuntimeException("Do not remember cookie's");
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(): string
    {
        throw new RuntimeException("Do not remember cookie's");
    }

    public function getDisplayName(): string
    {
        return $this->kvk_number;
    }
}
