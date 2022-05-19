<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WebsiteUser;
use Illuminate\Support\Facades\Auth;

class InfoController extends Controller
{
    public function  __construct() {
        $this->middleware(['auth:api']);
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
