<?php

declare(strict_types=1);

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

Route::middleware(['auth:web'])
    ->prefix('portal')
    ->group(function () {
        Route::get('index', [PortalController::class, 'index'])->name('portal.index');
        Route::post('index', [PortalController::class, 'edit'])->name('portal.edit');
    });
