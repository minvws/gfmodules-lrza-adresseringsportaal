<?php

declare(strict_types=1);

namespace App\Http\Responses;

use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use App\Services\HapiService;
use Symfony\Component\HttpFoundation\Response;
use App\Models\OrganizationUser;
use App\Exceptions\HapiException;
use App\Exceptions\OrganizationNotFoundException;
use Illuminate\Support\Facades\Auth;

class OidcLoginResponseHandler implements LoginResponseHandlerInterface
{
    public function __construct(
        protected HapiService $hapiService
    ) {
    }

     /**
     * @param object{
     *  "kvk_number": string|null,
     * } $userInfo
     */
    public function handleLoginResponse(object $userInfo): Response
    {
        $kvk = $userInfo->kvk_number ?? null;

        if (!isset($kvk)) {
            return redirect()->route('login')->withErrors(['login' => 'No KVK number provided']);
        }

        $created = false;
        try {
            $org = $this->hapiService->findOrganizationByIdentifier(
                HapiService::SYSTEM_KVK,
                $kvk
            );
        } catch (OrganizationNotFoundException $e) {
            $org = $this->hapiService->createOrganization(
                HapiService::SYSTEM_KVK,
                $kvk
            );
            $created = true;
        } catch (HapiException $e) {
            return redirect()->route('login')->with(
                'error', 'Invalid response from HAPI: ' . $e->getMessage()
            );
        }

        Auth::setUser(new OrganizationUser($org));


        return redirect()->route('portal.index')->with([
            'success' => 'Successfully ' . ($created ?
            'created Organization and logged in' : 'logged in to existing Organization')
            . ' with KVK number: ' . $kvk,
        ]);
    }
}
