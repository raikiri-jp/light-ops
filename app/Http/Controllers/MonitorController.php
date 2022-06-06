<?php

namespace App\Http\Controllers;

use App\Services\SiteService;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function show($slug)
    {
        $site = SiteService::findBySlug($slug);
        $name = is_null($site) ? $slug : $site->name;
        return view('monitor', [
            'name' => $name,
            'slug' => $slug,
        ]);
    }
}
