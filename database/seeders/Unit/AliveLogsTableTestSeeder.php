<?php

namespace Database\Seeders\Unit;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AliveLogsTableTestSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        self::insertLogsOfUnitSit();
        self::insertLogsOfThe360Site();
        self::insertLogsOfUndefinedSite();
    }

    private static function insertLogsOfUnitSit()
    {
        $siteSlug = 'unit.site';
        DB::table('alive_logs')->insert([
            'site_slug' => $siteSlug,
            'status' => 'alive',
            'created_at' => new Carbon('2022-06-06 13:00:00'),
        ]);
        DB::table('alive_logs')->insert([
            'site_slug' => $siteSlug,
            'status' => 'ok',
            'created_at' => new Carbon('2022-06-06 15:00:00'),
        ]);
        DB::table('alive_logs')->insert([
            'site_slug' => $siteSlug,
            'status' => 'error1',
            'created_at' => new Carbon('2022-06-06 22:21:30'),
        ]);
        DB::table('alive_logs')->insert([
            'site_slug' => $siteSlug,
            'status' => 'error2',
            'created_at' => new Carbon('2022-06-07 06:54:40'),
        ]);
        DB::table('alive_logs')->insert([
            'site_slug' => $siteSlug,
            'status' => 'alive',
            'created_at' => new Carbon('2022-06-07 06:55:30'),
        ]);
    }

    private static function insertLogsOfThe360Site()
    {
        $siteSlug = '360.site';
        DB::table('alive_logs')->insert([
            'site_slug' => $siteSlug,
            'status' => 'ok',
            'created_at' => new Carbon('2022-06-08 00:00:00'),
        ]);
    }

    private static function insertLogsOfUndefinedSite()
    {
        $siteSlug = 'undefined.site';
        DB::table('alive_logs')->insert([
            'site_slug' => $siteSlug,
            'status' => 'test status',
            'created_at' => new Carbon('2022-06-09 00:00:00'),
        ]);
    }
}
