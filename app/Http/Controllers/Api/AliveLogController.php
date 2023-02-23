<?php

namespace App\Http\Controllers\Api;

use App\Models\AliveLog;
use Exception;
use Illuminate\Routing\Controller as BaseController;

class AliveLogController extends BaseController
{
    // [Ex.1] http://127.0.0.1:8000/api/alive-log/site-slug/add/alive
    // [Ex.2] http://127.0.0.1:8000/api/alive-log/site-slug/add/ok
    // [Ex.3] http://127.0.0.1:8000/api/alive-log/site-slug/add/warning
    // [Ex.4] http://127.0.0.1:8000/api/alive-log/site-slug/add/error
    function add($site, $status)
    {
        $parameters = compact('site', 'status');
        $result = null;
        try {
            AliveLog::insert([
                'site_slug' => $site,
                'status' => $status,
            ]);
            $result = 'saved';
        } catch (Exception $ex) {
            $httpStatus = 500;
            $result = 'failed';
            $error = [$ex->getMessage()];
            // TODO throw or log
        } finally {
            return response()->json([
                'parameters' => $parameters,
                'result' => $result,
                'error' => $error ?? [],
            ], $httpStatus ?? 200);
        }
    }

    // [Ex.1] http://127.0.0.1:8000/api/alive-log/site-slug/list
    function list($site)
    {
        $aliveLogs = null;
        try {
            $aliveLogs = AliveLog::where('site_slug', $site)
                ->orderByDesc('created_at')
                ->limit(300)
                ->get();
        } catch (Exception $ex) {
            $httpStatus = 500;
            $error = [$ex->getMessage()];
            // TODO throw or log
        } finally {
            return response()->json([
                'logs' => $aliveLogs,
                'error' => $error ?? [],
            ], $httpStatus ?? 200);
        }
    }

    // [Ex.1] http://127.0.0.1:8000/api/alive-log/site-slug/latest
    function latest($site)
    {
        $record = null;
        try {
            $record = AliveLog::where('site_slug', $site)
                ->orderByDesc('id')
                ->first();
        } catch (Exception $ex) {
            $httpStatus = 500;
            $error = [$ex->getMessage()];
            // TODO throw or log
        } finally {
            return response()->json([
                'record' => $record,
                'error' => $error ?? [],
            ], $httpStatus ?? 200);
        }
    }
}
