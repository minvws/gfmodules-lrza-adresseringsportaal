<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function __construct(
        protected readonly LoginResponseHandlerInterface $loginResponseHandler
    ) {
    }

    public function login(Request $request): Response
    {
        $validated = $request->validate([
            'kvk' => 'required|size:8|regex:/^[0-9]+$/',
        ]);

        return $this->loginResponseHandler->handleLoginResponse(
            (object)[
                "kvk_number" => $validated['kvk'] ?? null,
            ]
        );
    }
}
