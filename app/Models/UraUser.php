<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\UraNoUraNumberException;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\Authenticatable;
use RuntimeException;

class UraUser implements Authenticatable
{
    public string $email;
    public string $ura_number;

    /**
     * @param string $ura_number
     */
    public function __construct(string $ura_number)
    {
        $this->ura_number = $ura_number;
        $this->email = $ura_number . '@ura.ura';
    }

    /**
     * @param object{
     *     ura_number: string,
     * } $oidcResponse
     * @throws UraNoUraNumberException
     */
    public static function deserializeFromObject(object $oidcResponse): ?UraUser
    {
        $requiredKeys = ["ura_number"];
        $missingKeys = [];
        foreach ($requiredKeys as $key) {
            if (!property_exists($oidcResponse, $key)) {
                $missingKeys[] = $key;
            }
        }
        if (count($missingKeys) > 0) {
            Log::error("Ura user missing required fields: " . implode(", ", $missingKeys));
            throw new UraNoUraNumberException();
        }

        return new UraUser($oidcResponse->ura_number);
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->ura_number;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return $this->ura_number;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifier(): string
    {
        return $this->ura_number;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        throw new RuntimeException("Ura users can't have a password");
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
        return $this->ura_number;
    }
}
