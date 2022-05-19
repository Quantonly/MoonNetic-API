<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WebsiteUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class InfoController extends Controller
{
    public function  __construct() {
        $this->middleware(['auth:api']);
    }

    public function createWebsite(Request $request) {
        $user = Auth::user();
        $serverIp = '172.26.5.10';
        if (!WebsiteUser::where('user_id', '=', $user->id)->exists()) {
            if (WebsiteUser::where('sub_domain', '=', $request->input('subDomain'))->exists()) {
                return response(null, 500);
            }
            $website = new WebsiteUser;
            $website->user_id = $user->id;
            $website->sub_domain = $request->input('subDomain');
            $website->server_ip = $serverIp;
            $website->sftp_username = Str::random(10);
            $website->sftp_password = Str::random(10);
            $website->sftp_host = 'moonnetic.com';
            $website->sftp_port = '22';
            $website->php_host = 'moonnetic.com';
            $website->php_database = 'db_' . Str::random(10);
            $website->php_username = Str::random(10);
            $website->php_password = Str::random(10);
            $website->php_version = $request->input('phpVersion');
            $website->save();
            $process = new Process(['/usr/scripts/create_website.sh']);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            return response('Success', 200);
        }
        return response(null, 500);
    }

    public function getWebsites(Request $request) {
        $user = Auth::user();
        $websites = WebsiteUser::where('user_id', '=', $user->id)->get();
        return response()->json(compact('websites'));
    }

    public function getSFTP(Request $request) {
        $user = Auth::user();
        $info = WebsiteUser::select('sftp_username', 'sftp_password', 'sftp_host', 'sftp_port')->where('user_id', '=', $user->id)->first();
        return response()->json(compact('info'));
    }

    public function getPHP(Request $request) {
        $user = Auth::user();
        $info = WebsiteUser::select('php_username', 'php_password', 'php_host', 'php_database', 'php_version')->where('user_id', '=', $user->id)->first();
        return response()->json(compact('info'));
    }
}
