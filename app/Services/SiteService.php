<?php

namespace App\Services;

use App\Models\AliveLog;
use App\Models\Site;
use Illuminate\Database\Eloquent\Collection;

/**
 * Sites service.
 */
class SiteService
{
    /**
     * Get all sites table records.
     *
     * @return Collection All sites table records
     */
    public static function list(): Collection
    {
        return SIte::orderBy('slug')->get();
    }

    /**
     * Get a site record.
     *
     * @param mixed<integer|string> $id Site ID
     * @return mixed<Site|null> A sites table record
     */
    public static function findById($id): mixed
    {
        return Site::find($id);
    }

    /**
     * Get a site record.
     *
     * @param string $slug Site slug
     * @return mixed<Site|null> A sites table record
     */
    public static function findBySlug($slug): mixed
    {
        return Site::where('slug', $slug)->first();
    }

    /**
     * Get the status of the site.
     *
     * @param string $slug Site slug
     * @return array `site_id`, `site_slug`, `status`, `last_status`, `safe_sec`, `diff_sec`, `messages`
     */
    public static function status($slug): array
    {
        $site = Site::where('slug', $slug)->first();
        $lastLog = AliveLog::where('site_slug', $slug)
            ->orderByDesc('created_at')
            ->first();

        $messages = [];
        $data = [
            'site_id' => null,
            'site_slug' => $slug,
            'status' => 'status unknown',
            'last_status' => null,
            'last_logged_at' => null,
            'safe_sec' => -1,
            'diff_sec' => -1,
            'messages' => $messages,
        ];

        if ($site) {
            $data['site_id'] = $site->id;
            $data['safe_sec'] = $site->safe_time;
            if ($data['safe_sec'] <= 0) {
                $data['safe_sec'] = 0;
            }
        }

        if ($lastLog) {
            /** @var \Illuminate\Support\Carbon */
            $lastLoggedAt = $lastLog->created_at;
            // AliveLog が最後に登録されてから経過した時間を秒単位で求める
            $diffInSeconds = $lastLoggedAt->diffInSeconds(now());

            $data['status'] = $lastLog->status;
            $data['last_status'] = $lastLog->status;
            $data['last_logged_at'] = $lastLog->created_at;
            $data['diff_sec'] = $diffInSeconds;

            if ($data['safe_sec'] > 0) {
                if ($diffInSeconds > $data['safe_sec']) {
                    $data['status'] = 'warning';
                    $diffInMinutes = $lastLoggedAt->diffInMinutes(now()->addSeconds(30));
                    $messages[] = "The status has not been updated for about {$diffInMinutes} minutes.";
                }
            }
        }

        $data['messages'] = $messages;
        return $data;
    }
}
