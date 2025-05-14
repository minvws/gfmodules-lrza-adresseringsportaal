<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Exceptions\UraNoUraNumberException;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

class OidcLoginResponseHandler implements LoginResponseHandlerInterface
{
    /**
     * @param object{
     *      ura_number: string,
     *  } $userInfo
     * @throws UraNoUraNumberException
     */
    public function handleLoginResponse(object $userInfo): Response
    {
        throw new \Exception('Not implemented');
        // return new RedirectResponse(route('portal.index'));
    }
}
