<?php

declare(strict_types=1);

namespace App\Services\Eherkenning;

use App\Models\OrganizationUser;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Session\Session;

class OrganizationAuthGuard implements Guard
{
    protected const SESSION_KEY = 'org_user';

    public function __construct(
        protected Session $session,
        protected Dispatcher $events,
    ) {
    }

    public function check(): bool
    {
        return $this->session->has(self::SESSION_KEY);
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function user(): OrganizationUser | null
    {
        if (!$this->check()) {
            return null;
        }

        return $this->session->get(self::SESSION_KEY);
    }

    public function id(): string | null
    {
        return $this->user()?->getAuthIdentifier();
    }

    /**
     * @param array<mixed> $credentials
     * @return mixed
     */
    public function validate(array $credentials = [])
    {
        throw new \RuntimeException('Not implemented EherkenningAuthGuard::validate() method');
    }

    public function hasUser()
    {
        throw new \RuntimeException('Not implemented EherkenningAuthGuard::hasUser() method');
    }

    public function setUser(Authenticatable $user): static
    {
        if (!$user instanceof OrganizationUser) {
            throw new \InvalidArgumentException('User must be an instance of OrganizationUser');
        }

        $this->session->put(self::SESSION_KEY, $user);
        $this->session->migrate(true);
        return $this;
    }

    /**
     * Logs out the current user.
     *
     * @return void
     */
    public function logout(): void
    {
        $user = $this->user();
        if (!$user) {
            return;
        }

        $this->clearUserDataFromStorage();

        $this->events->dispatch(new Logout('oidc', $user));
    }

    protected function clearUserDataFromStorage(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }
}
