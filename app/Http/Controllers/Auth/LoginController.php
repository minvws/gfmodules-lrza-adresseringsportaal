<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Kvk;
use App\Models\KvkUser;
use App\Models\Ura;
use App\Models\UraUser;
use App\Services\Eherkenning\UraAuthGuard;
use App\Services\Eherkenning\KvkAuthGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function loginUra(Request $request, UraAuthGuard $guard): RedirectResponse
    {
        $validated = $request->validate([
            'ura' => 'required|size:8|regex:/^[0-9]+$/',
        ]);

        $guard->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $user = Ura::firstOrCreate(['ura' => $validated['ura']]);

        $user = new UraUser($user->ura);
        $guard->setUser($user);

        return redirect()->route('portal.ura.index');
    }

    public function loginKvk(Request $request, KvkAuthGuard $guard): RedirectResponse
    {
        $validated = $request->validate([
            'kvk' => 'required|size:8|regex:/^[0-9]+$/',
        ]);

        $guard->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $user = Kvk::firstOrCreate(['kvk' => $validated['kvk']]);

        $user = new KvkUser($user->kvk);
        $guard->setUser($user);

        return redirect()->route('portal.kvk.index');
    }
}
