<?php

declare(strict_types=1);

namespace App\Http\Responses;

use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

class OidcLoginResponseHandler implements LoginResponseHandlerInterface
{
    public function handleLoginResponse(object $userInfo): Response
    {
        throw new \Exception('Not implemented');
        // return new RedirectResponse(route('portal.index'));
    }
}
