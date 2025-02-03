<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Ura;
use App\Models\UraUser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PortalController extends Controller
{
    public function index(): View
    {
        /** @var UraUser $auth */
        $auth = auth()->user();
        $ura = Ura::firstWhere('ura', $auth->ura_number);
        if ($ura === null) {
            throw new NotFoundHttpException('URA not found');
        }

        return view('portals/index')->with('ura', $ura);
    }

    public function edit(Request $request): RedirectResponse
    {
        $validated_data = $request->validate([
            'endpoint' => 'required|url|max:1024|starts_with:https://',
        ]);

        /** @var UraUser $auth */
        $auth = auth()->user();
        $ura = Ura::firstWhere('ura', $auth->ura_number);
        if ($ura === null) {
            throw new NotFoundHttpException('URA not found');
        }

        if ($ura->suppliers()->count() === 0) {
            // Create a new supplier if there are no suppliers available yet
            $ura->suppliers()->create($validated_data);
        } else {
            // Update the first supplier
            $supplier = $ura->suppliers()->first();
            if ($supplier === null) {
                throw new NotFoundHttpException('No supplier found');
            }
            $supplier->update($validated_data);
        }

        return redirect()
            ->route('portals.index')
            ->with('success', 'Supplied endpoint was successfully updated')
        ;
    }
}
