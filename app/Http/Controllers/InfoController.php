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

    public function setStoragePermissions(Request $request) {
        $user = Auth::user();
        $serverIp = '172.26.5.10';
        $subDomain = WebsiteUser::where('user_id', '=', $user->id)->first()->sub_domain;
        $process = new Process(['/usr/scripts/set_storage_permissions.sh', $serverIp, $subDomain]);
        $process->run();
        if (!$process->isSuccessful()) {
           throw new ProcessFailedException($process);
        }
        return response('Success', 200);
    }

    public function createWebsite(Request $request) {
        $user = Auth::user();
        $serverIp = '172.26.5.10';
        $subDomain = $request->input('subDomain');
        $phpVersion = $request->input('phpVersion');
        if (!WebsiteUser::where('user_id', '=', $user->id)->exists()) {
            if (WebsiteUser::where('sub_domain', '=', $request->input('subDomain'))->exists()) {
                return response(null, 500);
            }
            $sftpUsername = Str::random(10);
            $sftpPassword = Str::random(10);
            $phpDatabase = Str::random(10);
            $phpUsername = $phpDatabase;
            $phpPassword = Str::random(10);
            $website = new WebsiteUser;
            $website->user_id = $user->id;
            $website->sub_domain = $subDomain;
            $website->server_ip = $serverIp;
            $website->sftp_username = $sftpUsername;
            $website->sftp_password = $sftpPassword;
            $website->sftp_host = 'server1.moonnetic.com';
            $website->sftp_port = '22';
            $website->php_host = 'server1.moonnetic.com';
            $website->php_database = $phpDatabase;
            $website->php_username = $phpUsername;
            $website->php_password = $phpPassword;
            $website->php_version = $phpVersion;
            $website->save();
            $process = new Process(['/usr/scripts/create_website.sh', $serverIp, $subDomain, $sftpUsername, $sftpPassword, $phpUsername, $phpPassword, $phpVersion]);
            $process->run();
            if (!$process->isSuccessful()) {
               throw new ProcessFailedException($process);
            }
            return response('Success', 200);
        }
        return response(null, 500);
    }

    public function deleteWebsite(Request $request) {
        $user = Auth::user();
        $website = WebsiteUser::where('user_id', '=', $user->id)->first();
        WebsiteUser::where('user_id', '=', $user->id)->delete();
        $process = new Process(['/usr/scripts/delete_website.sh', $website->serverIp, $website->$subDomain, $website->$sftpUsername, $website->$sftpPassword, $website->$phpUsername, $website->$phpPassword, $website->$phpVersion]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return response('Success', 200);
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
