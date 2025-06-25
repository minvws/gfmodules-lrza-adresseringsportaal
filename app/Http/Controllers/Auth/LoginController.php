<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OrganizationUser;
use App\Services\Eherkenning\OrganizationAuthGuard;
use App\Services\HapiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Exceptions\HapiException;
use App\Exceptions\OrganizationNotFoundException;

class LoginController extends Controller
{
    public function login(Request $request, OrganizationAuthGuard $guard, HapiService $hapiService): RedirectResponse
    {
        $uraLogin = false;
        $ura = $request->input('ura');
        $kvk = $request->input('kvk');
        if ($ura != null) {
            $validated = $request->validate([
                'ura' => 'required|size:8|regex:/^[0-9]+$/',
            ]);
            $ura = $validated['ura'];
            $uraLogin = true;
        } elseif ($kvk !== null) {
            $validated = $request->validate([
                'kvk' => 'required|size:8|regex:/^[0-9]+$/',
            ]);

            $kvk = $validated['kvk'];
        } else {
            return redirect()->back()->withErrors(['login' => 'Please provide either a URA or KVK number']);
        }

        $created = false;
        try {
            $org = $hapiService->findOrganizationByIdentifier(
                $uraLogin ? HapiService::SYSTEM_URA : HapiService::SYSTEM_KVK,
                $uraLogin ? $ura : $kvk
            );
        } catch (OrganizationNotFoundException $e) {
            $org = $hapiService->createOrganization(
                $uraLogin ? HapiService::SYSTEM_URA : HapiService::SYSTEM_KVK,
                $uraLogin ? $ura : $kvk
            );
            $created = true;
        } catch (HapiException $e) {
            return redirect()->back()->withErrors(['login' => 'Invalid response from HAPI: ' . $e->getMessage()]);
        }

        $guard->setUser(new OrganizationUser($org));


        return redirect()->route('portal.index')->with([
            'success' => 'Successfully ' . ($created ?
            'created Organization and logged in' : 'logged in to existing Organization')
             . ' with ' . ($uraLogin ? 'URA' : 'KVK') . ' number: ' . ($uraLogin ? $ura : $kvk),
        ]);
    }
}
