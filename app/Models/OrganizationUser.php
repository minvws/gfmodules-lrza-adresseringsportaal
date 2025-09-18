<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use RuntimeException;

class OrganizationUser implements Authenticatable
{
    public Organization $organization;

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->organization->getKvkIdentifier()
        ?? throw new RuntimeException("Organization has no KVK identifier");
    }


    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return $this->organization->getKvkIdentifier()
        ?? throw new RuntimeException("Organization has no KVK identifier");
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifier(): string
    {
        return $this->organization->getKvkIdentifier()
        ?? throw new RuntimeException("Organization has no KVK identifier");
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        throw new RuntimeException("users can't have a password");
    }

    /**
     * Get the name of the password attribute for the user.
     *
     * @return string
     */
    public function getAuthPasswordName()
    {
        throw new RuntimeException("users can't have a password");
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
        return $this->organization->getName();
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }
}
