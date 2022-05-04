<?php

namespace App\Http\Controllers\Api;

use App\Models\AliveLog;
use Illuminate\Routing\Controller as BaseController;

class AliveLogController extends BaseController
{
    // [Ex.1] http://127.0.0.1:8000/api/sites
    function sites()
    {
        $sites = AliveLog::select('site')
            ->orderBy('site')
            ->distinct()
            ->get()
            ->toArray();
        return array_column($sites, 'site');
    }

    // [Ex.1] http://127.0.0.1:8000/api/alive-log/sitename/add/alive
    // [Ex.2] http://127.0.0.1:8000/api/alive-log/sitename/add/ok
    // [Ex.3] http://127.0.0.1:8000/api/alive-log/sitename/add/warning
    // [Ex.4] http://127.0.0.1:8000/api/alive-log/sitename/add/error
    function add($site, $status)
    {
        $parameters = compact('site', 'status');
        AliveLog::insert([
            'site' => $site,
            'status' => $status,
        ]);

        // TODO Try-catch
        return ['result' => 'saved', 'parameters' => $parameters];
    }

    // [Ex.1] http://127.0.0.1:8000/api/alive-log/sitename/list
    function list($site)
    {
        return AliveLog::where('site', $site)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();
    }

    // [Ex.1] http://127.0.0.1:8000/api/alive-log/sitename/latest
    function latest($site)
    {
        return AliveLog::where('site', $site)
            ->orderByDesc('id')
            ->first();
    }

    // [Ex.1] http://127.0.0.1:8000/api/alive-log/sitename/status
    function status($site)
    {
        $log = $this->latest($site);
        $lastStatus = $log->status;
        $status = $lastStatus;
        /** @var \Illuminate\Support\Carbon $last */
        $modifiedAt = $log->created_at;
        $diffInMinutes = $modifiedAt->diffInMinutes(now()->addSeconds(30));
        $messages = [];

        if ($diffInMinutes >= 10) {
            $messages[] = "The status hasn't been updated for about {$diffInMinutes} minutes.";
            if (in_array(strtoupper($status), ['ALIVE', 'OK'])) {
                $status = 'warning';
            }
        }

        return [
            'status' => $status,
            'last_status' => $lastStatus,
            'modified_at' => $modifiedAt,
            'messages' => $messages
        ];
    }
}
