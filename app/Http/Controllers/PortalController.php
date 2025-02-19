<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Kvk;
use App\Models\KvkUser;
use App\Models\Ura;
use App\Models\UraUser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PortalController extends Controller
{
    public function uraIndex(): View
    {
        /** @var UraUser $auth */
        $auth = auth()->user();
        $ura_user = Ura::firstWhere('ura', $auth->ura_number);
        if ($ura_user === null) {
            throw new NotFoundHttpException('URA not found');
        }

        return view('portals/ura/index')->with('ura_user', $ura_user);
    }

    public function kvkIndex(): View
    {
        /** @var KvkUser $auth */
        $auth = auth()->user();
        $kvk_user = Kvk::firstWhere('kvk', $auth->kvk_number);
        if ($kvk_user === null) {
            throw new NotFoundHttpException('KVK not found');
        }

        return view('portals/kvk/index')->with('kvk_user', $kvk_user);
    }

    public function uraEdit(Request $request): RedirectResponse
    {
        $validated_data = $this->checkEndpoint($request);

        /** @var UraUser $auth */
        $auth = auth()->user();
        $ura = Ura::firstWhere('ura', $auth->ura_number);
        if ($ura === null) {
            throw new AccessDeniedException('URA not found');
        }

        $this->updateSupplierEndpoint($ura, $validated_data);

        return redirect()
            ->route('portal.ura.index')
            ->with('success', 'Supplied endpoint was successfully updated')
        ;
    }

    public function kvkEdit(Request $request): RedirectResponse
    {
        $validated_data = $this->checkEndpoint($request);

        /** @var KvkUser $auth */
        $auth = auth()->user();
        $kvk = Kvk::firstWhere('kvk', $auth->kvk_number);
        if ($kvk === null) {
            throw new AccessDeniedException('KVK not found');
        }

        $this->updateSupplierEndpoint($kvk, $validated_data);

        return redirect()
            ->route('portal.kvk.index')
            ->with('success', 'Supplied endpoint was successfully updated')
            ;
    }

    # @phpstan-ignore-next-line
    protected function checkEndpoint(Request $request): array
    {
        return $request->validate([
            'endpoint' => [
                'required',
                'url',
                'max:1024',
                function ($attribute, $value, $fail) {
                    $prefixes = ["https://"];
                    if (config('app.allow_insecure_endpoints') === true) {
                        $prefixes[] = "http://";
                    }
                    if (!Str::startsWith(strtolower($value), $prefixes)) {
                        $fail($attribute . ' must start with https://');
                    }
                },
            ]
        ]);
    }

    # @phpstan-ignore-next-line
    protected function updateSupplierEndpoint(Ura|Kvk $user, array $validated_data): void
    {
        if ($user->suppliers()->count() === 0) {
            // Create a new supplier if there are no suppliers available yet
            $user->suppliers()->create($validated_data);
        } else {
            // Update the first supplier
            $supplier = $user->suppliers()->first();
            if ($supplier === null) {
                throw new BadRequestException('No supplier found');
            }
            $supplier->update($validated_data);
        }
    }
}
