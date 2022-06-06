<?php

use App\Http\Controllers\MonitorController;
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

$require = (new class
{
    const SLUG = '[a-z0-9_.:\-]{4,64}';
});

Route::get('/', function () {
    return view('sites');
});

Route::get('/monitor/{slug}', [MonitorController::class, 'show'])
    ->where('slug', $require::SLUG);
