<?php

namespace Tests\Unit\Services;

use App\Services\SiteService;
use Carbon\Carbon;
use Database\Seeders\Unit\AliveLogsTableTestSeeder;
use Database\Seeders\Unit\SitesTableTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SiteServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function test_list(): void
    {
        Artisan::call('db:seed', [
            '--class' => SitesTableTestSeeder::class
        ]);
        $response = SiteService::list();
        $results = $response->toArray();

        $this->assertSame(3, count($results));

        $this->assertSame(
            [
                'id',
                'slug',
                'name',
                'safe_time',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            array_keys($results[0])
        );

        $this->assertSame(360, $results[0]['id']);
        $this->assertSame('360.site', $results[0]['slug']);
        $this->assertSame(100, $results[1]['id']);
        $this->assertSame('minus.site', $results[1]['slug']);
        $this->assertSame(300, $results[2]['id']);
        $this->assertSame('unit.site', $results[2]['slug']);
    }

    public function test_findById(): void
    {
        Artisan::call('db:seed', [
            '--class' => SitesTableTestSeeder::class
        ]);
        $id = 300;
        $site = SiteService::findById($id);
        $this->assertSame($id, $site->id);
    }

    public function test_findById__notFound(): void
    {
        Artisan::call('db:seed', [
            '--class' => SitesTableTestSeeder::class
        ]);
        $deletedId = 400;
        $this->assertNull(SiteService::findById($deletedId));
    }

    public function test_findBySlug(): void
    {
        Artisan::call('db:seed', [
            '--class' => SitesTableTestSeeder::class
        ]);
        $slug = 'unit.site';
        $site = SiteService::findBySlug($slug);
        $this->assertSame($slug, $site->slug);
    }

    public function test_status(): void
    {
        Artisan::call('db:seed', [
            '--class' => SitesTableTestSeeder::class
        ]);
        Artisan::call('db:seed', [
            '--class' => AliveLogsTableTestSeeder::class
        ]);
        Carbon::setTestNow(new Carbon('2022-06-08 07:00:00'));

        $slug = 'unit.site';
        $siteId = SiteService::findBySlug($slug)->id;
        $result = SiteService::status($slug);
        $this->assertArrayHasKey('site_id', $result);
        $this->assertArrayHasKey('site_slug', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('last_status', $result);
        $this->assertArrayHasKey('last_logged_at', $result);
        $this->assertArrayHasKey('safe_sec', $result);
        $this->assertArrayHasKey('diff_sec', $result);
        $this->assertArrayHasKey('messages', $result);
        $this->assertSame($siteId, $result['site_id']);
        $this->assertSame($slug, $result['site_slug']);
        $this->assertSame('alive', $result['status']);
        $this->assertSame('alive', $result['last_status']);
        $this->assertTrue((new Carbon('2022-06-07 06:55:30'))->eq($result['last_logged_at']));
        $this->assertSame(0, $result['safe_sec']);
        $this->assertSame((24 * 60 * 60) + (60 * 4) + 30, $result['diff_sec']);
        $this->assertSame([], $result['messages']);
    }

    public function test_status__warning(): void
    {
        Artisan::call('db:seed', [
            '--class' => SitesTableTestSeeder::class
        ]);
        Artisan::call('db:seed', [
            '--class' => AliveLogsTableTestSeeder::class
        ]);

        $slug = '360.site';
        $siteId = SiteService::findBySlug($slug)->id;

        // safe_sec > diff_sec
        Carbon::setTestNow(new Carbon('2022-06-08 00:05:59'));
        $result = SiteService::status($slug);
        $this->assertSame($siteId, $result['site_id']);
        $this->assertSame($slug, $result['site_slug']);
        $this->assertSame(360, $result['safe_sec']);
        $this->assertSame(359, $result['diff_sec']);
        $this->assertSame('ok', $result['last_status']);
        $this->assertSame('ok', $result['status']);
        $this->assertSame([], $result['messages']);

        // safe_sec == diff_sec
        Carbon::setTestNow(new Carbon('2022-06-08 00:06:00'));
        $result = SiteService::status($slug);
        $this->assertSame(360, $result['safe_sec']);
        $this->assertSame(360, $result['diff_sec']);
        $this->assertSame('ok', $result['last_status']);
        $this->assertSame('ok', $result['status']);
        $this->assertSame([], $result['messages']);

        // safe_sec < diff_sec (ok -> warning)
        Carbon::setTestNow(new Carbon('2022-06-08 00:06:01'));
        $result = SiteService::status($slug);
        $this->assertSame(360, $result['safe_sec']);
        $this->assertSame(361, $result['diff_sec']);
        $this->assertSame('ok', $result['last_status']);
        $this->assertSame('warning', $result['status']);
        $this->assertCount(1, $result['messages']);
        // 361 sec ≒ 6 min
        $this->assertMatchesRegularExpression('/6/', $result['messages'][0]);
    }

    public function test_status__undefined_site(): void
    {
        Artisan::call('db:seed', [
            '--class' => SitesTableTestSeeder::class
        ]);
        Artisan::call('db:seed', [
            '--class' => AliveLogsTableTestSeeder::class
        ]);

        Carbon::setTestNow(new Carbon('2022-06-09 00:00:01'));

        $slug = 'undefined.site';
        $this->assertDatabaseMissing('sites', ['slug' => $slug]);

        $result = SiteService::status($slug);
        $this->assertNull($result['site_id']);
        $this->assertSame($slug, $result['site_slug']);
        $this->assertSame('test status', $result['status']);
        $this->assertSame('test status', $result['last_status']);
        $this->assertTrue((new Carbon('2022-06-09 00:00:00'))->eq($result['last_logged_at']));
        $this->assertSame(-1, $result['safe_sec']);
        $this->assertSame(1, $result['diff_sec']);
        $this->assertSame([], $result['messages']);
    }

    public function test_status__no_data(): void
    {
        $slug = 'no-data';
        $this->assertDatabaseMissing('sites', ['slug' => $slug]);
        $this->assertDatabaseMissing('alive_logs', ['site_slug' => $slug]);

        $result = SiteService::status($slug);
        $this->assertNull($result['site_id']);
        $this->assertSame($slug, $result['site_slug']);
        $this->assertSame('status unknown', $result['status']);
        $this->assertNull($result['last_status']);
        $this->assertNull($result['last_logged_at']);
        $this->assertSame(-1, $result['safe_sec']);
        $this->assertSame(-1, $result['diff_sec']);
        $this->assertSame([], $result['messages']);
    }
}
