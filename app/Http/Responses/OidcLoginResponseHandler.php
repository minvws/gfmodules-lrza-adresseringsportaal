<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Exceptions\UraNoUraNumberException;
use App\Models\Ura;
use App\Models\UraUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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
        $user = UraUser::deserializeFromObject($userInfo);
        if ($user === null) {
            return redirect()
                ->route('index')
                ->with('error', __('Something went wrong with logging in, please try again.'));
        }

        Auth::setUser($user);

        // Create URA record in DB if this URA user doesn't exist yet
        $ura = Ura::firstWhere('ura', $user->ura_number);
        if ($ura === null) {
            Ura::create(['ura' => $user->ura_number]);
        }

        return new RedirectResponse(route('portals.index'));
    }
}
