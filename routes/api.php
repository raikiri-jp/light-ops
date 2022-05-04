<?php

use App\Http\Controllers\Api\AliveLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$require = (new class
{
    const SITE = '[a-z0-9_.:\-]{4,64}';
    const STATUS = '[a-z0-9]{1,16}';
});

Route::get('/sites', [AliveLogController::class, 'sites']);

Route::get(
    '/alive-log/{site}/add/{status}',
    [AliveLogController::class, 'add']
)->where('site', $require::SITE)->where('status', $require::STATUS);
Route::post(
    '/alive-log/{site}/add/{status}',
    [AliveLogController::class, 'add']
)->where('site', $require::SITE)->where('status', $require::STATUS);

Route::get(
    '/alive-log/{site}/list',
    [AliveLogController::class, 'list']
)->where('site', $require::SITE);

Route::get(
    '/alive-log/{site}/latest',
    [AliveLogController::class, 'latest']
)->where('site', $require::SITE);

Route::get(
    '/alive-log/{site}/status',
    [AliveLogController::class, 'status']
)->where('site', $require::SITE);
