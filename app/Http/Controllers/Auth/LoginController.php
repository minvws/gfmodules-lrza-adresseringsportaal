<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OrganizationUser;
use App\Services\Eherkenning\OrganizationAuthGuard;
use App\Services\HapiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function loginUra(Request $request, OrganizationAuthGuard $guard, HapiService $hapiService): RedirectResponse
    {
        $validated = $request->validate([
            'ura' => 'required|size:8|regex:/^[0-9]+$/',
        ]);

        $guard->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        try {
            $org = $hapiService->findOrganizationByIdentifier(HapiService::SYSTEM_URA, $validated['ura']);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['ura' => 'URA number not found in HAPI']);
        }

        $guard->setUser(new OrganizationUser($org));

        return redirect()->route('portal.index');
    }

    public function loginKvk(Request $request, OrganizationAuthGuard $guard, HapiService $hapiService): RedirectResponse
    {
        $validated = $request->validate([
            'kvk' => 'required|size:8|regex:/^[0-9]+$/',
        ]);

        $guard->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        try {
            $org = $hapiService->findOrganizationByIdentifier(HapiService::SYSTEM_KVK, $validated['kvk']);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['kvk' => 'KVK number not found in HAPI']);
        }

        $guard->setUser(new OrganizationUser($org));

        return redirect()->route('portal.index');
    }
}
