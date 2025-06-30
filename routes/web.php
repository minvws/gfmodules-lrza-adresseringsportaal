<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\IndexController;
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

Route::middleware(['guest'])->group(function () {
    Route::get('login', IndexController::class)->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.post');
});
Route::middleware(['auth'])
    ->group(function () {
        Route::post('logout', LogoutController::class)->name('logout');
        Route::get('index', [PortalController::class, 'index'])->name('portal.index');
        Route::post('index', [PortalController::class, 'edit'])->name('portal.edit');
        Route::get('edit-organization', [PortalController::class, 'editOrganization'])
        ->name('portal.edit-organization');
        Route::post('edit-organization', [PortalController::class, 'updateOrganization'])
        ->name('portal.update-organization');
        Route::get('edit-endpoint', [PortalController::class, 'editEndpoint'])
        ->name('portal.edit-endpoint');
        Route::post('edit-endpoint', [PortalController::class, 'updateEndpoint'])
        ->name('portal.update-endpoint');
        Route::delete('delete-endpoint', [PortalController::class, 'deleteEndpoint'])
        ->name('portal.delete-endpoint');
        Route::delete('delete-organization', [PortalController::class, 'deleteOrganization'])
        ->name('portal.delete-organization');
    });
