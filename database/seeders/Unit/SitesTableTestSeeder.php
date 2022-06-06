<?php

namespace Database\Seeders\Unit;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SitesTableTestSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sites')->insert([
            'id' => 360,
            'slug' => '360.site',
            'name' => Str::random(20),
            'safe_time' => 360,
        ]);
        DB::table('sites')->insert([
            'id' => 300,
            'slug' => 'unit.site',
            'name' => Str::random(20),
            'safe_time' => 0,
        ]);
        DB::table('sites')->insert([
            'id' => 100,
            'slug' => 'minus.site',
            'name' => Str::random(20),
            'safe_time' => -1,
        ]);
        DB::table('sites')->insert([
            'id' => 400,
            'slug' => 'deleted.site',
            'name' => Str::random(20),
            'safe_time' => 100,
            'deleted_at' => Carbon::now(),
        ]);
    }
}
