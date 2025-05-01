<?php

declare(strict_types=1);

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\Auth\DigidMockController;
use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', IndexController::class)->name('index');

Route::post('login/ura', [LoginController::class, 'loginUra'])->name('login.ura');
Route::post('login/kvk', [LoginController::class, 'loginKvk'])->name('login.kvk');
if (config('auth.oidc_mock_enabled')) {
    Route::get('oidc/login', [DigidMockController::class, 'login'])->name('oidc.login');
}

Route::post('logout', LogoutController::class)->name('logout');

Route::middleware(['auth:web_ura'])
    ->prefix('portal/ura')
    ->group(function () {
        Route::get('index', [PortalController::class, 'uraIndex'])->name('portal.ura.index');
        Route::post('index', [PortalController::class, 'uraEdit'])->name('portal.ura.edit');
    });

Route::middleware(['auth:web_kvk'])
    ->prefix('portal/kvk')
    ->group(function () {
        Route::get('index', [PortalController::class, 'kvkIndex'])->name('portal.kvk.index');
        Route::post('index', [PortalController::class, 'kvkEdit'])->name('portal.kvk.edit');
    });

// FHIR-specific routes for Endpoint

Route::prefix('fhir/Endpoint')->group(function () {
    Route::get('', [ApiController::class, 'getAllEndpoints'])->name('fhir.endpoint.get_all');
    Route::get('_history', [ApiController::class, 'getEndpointHistory'])->name('fhir.endpoint.history');
    Route::get('{id}', [ApiController::class, 'getEndpointById'])->name('fhir.endpoint.get_one');
    Route::get('{id}/_history', [ApiController::class, 'getEndpointHistoryById'])->name('fhir.endpoint.history_by_id');
});
