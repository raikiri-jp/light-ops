<?php

namespace App\Http\Controllers\Api;

use App\Models\Site;
use App\Services\SiteService;
use Exception;
use Illuminate\Routing\Controller as BaseController;

class SiteController extends BaseController
{
    // [Ex.1] http://127.0.0.1:8000/api/sites
    public function list()
    {
        $sites = [];
        $error = [];
        try {
            $func = function (array $site): array {
                return [
                    'id' => $site['id'],
                    'slug' => $site['slug'],
                    'name' => $site['name'],
                ];
            };
            $sites = array_map($func, SiteService::list()->toArray());

            if (count($sites) === 0) {
                $error[] = 'No site is registered.';
            }
        } catch (Exception $ex) {
            $httpStatus = 500;
            $error[] = $ex->getMessage();
            // TODO throw or log
        } finally {
            return response()->json([
                'sites' => $sites,
                'error' => $error ?? [],
            ], $httpStatus ?? 200);
        }
    }

    // [Ex.1] http://127.0.0.1:8000/api/site/site-slug/status
    public function status($site)
    {
        $data = null;
        try {
            $data = SiteService::status($site);
        } catch (Exception $ex) {
            $httpStatus = 500;
            $error = [$ex->getMessage()];
            // TODO throw or log
        } finally {

            return response()->json(
                array_merge($data, ['error' => $error ?? []]),
                $httpStatus ?? 200
            );
        }
    }

    public function addExample()
    {
        $result = null;
        try {
            Site::insert([
                'slug' => 'example.com',
                'name' => 'Example site',
                'safe_time' => 3600,
            ]);
            $result = 'saved';
        } catch (Exception $ex) {
            $httpStatus = 500;
            $result = 'failed';
            $error = [$ex->getMessage()];
            // TODO throw or log
        } finally {
            return response()->json([
                'result' => $result,
                'error' => $error ?? [],
            ], $httpStatus ?? 200);
        }
    }
}
