<?php

use App\Http\Controllers\Api\AliveLogController;
use App\Http\Controllers\Api\SiteController;
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
    const SLUG = '[a-z0-9_.:\-]{4,64}';
    const STATUS = '[a-z0-9]{1,16}';
});

Route::get('/sites', [SiteController::class, 'list']);
Route::get('/site/{slug}/status', [SiteController::class, 'status'])
    ->where('slug', $require::SLUG);
Route::get('/site/add-example', [SiteController::class, 'addExample']);

Route::get(
    '/alive-log/{slug}/add/{status}',
    [AliveLogController::class, 'add']
)->where('slug', $require::SLUG)->where('status', $require::STATUS);
Route::post(
    '/alive-log/{slug}/add/{status}',
    [AliveLogController::class, 'add']
)->where('slug', $require::SLUG)->where('status', $require::STATUS);

Route::get(
    '/alive-log/{slug}/list',
    [AliveLogController::class, 'list']
)->where('slug', $require::SLUG);

Route::get(
    '/alive-log/{slug}/latest',
    [AliveLogController::class, 'latest']
)->where('slug', $require::SLUG);

Route::get(
    '/alive-log/{slug}/status',
    [AliveLogController::class, 'status']
)->where('slug', $require::SLUG);
