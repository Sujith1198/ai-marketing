<?php

namespace App\Http\Controllers;

use App\Models\AIProvider;
use App\Models\SocialAccount;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

class SystemHealthController extends Controller
{
    public function index()
    {
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();
        
        try {
            DB::connection()->getPdo();
            $dbStatus = 'Connected (MySQL ' . DB::select('select version() as v')[0]->v . ')';
            $dbOk = true;
        } catch (\Exception $e) {
            $dbStatus = 'Disconnected: ' . $e->getMessage();
            $dbOk = false;
        }

        $storageWritable = is_writable(storage_path());
        $aiProvidersCount = AIProvider::where('is_active', true)->count();
        $socialConnectionsCount = SocialAccount::where('status', 'connected')->count();

        return view('system.health', compact(
            'phpVersion',
            'laravelVersion',
            'dbStatus',
            'dbOk',
            'storageWritable',
            'aiProvidersCount',
            'socialConnectionsCount'
        ));
    }
}
