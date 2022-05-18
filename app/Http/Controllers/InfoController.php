<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Server;
use Illuminate\Support\Facades\Auth;

class InfoController extends Controller
{
    public function  __construct() {
        $this->middleware(['auth:api']);
    }

    public function getSFTP(Request $request) {
        $user = Auth::user();
        $info = Server::select('sftp_username', 'sftp_password', 'sftp_host', 'sftp_port')->where('user_id', '=', $user->id)->first();
        return response()->json(compact('info'));
    }

    public function getPHP(Request $request) {
        $user = Auth::user();
        $info = Server::select('php_username', 'php_password', 'php_host', 'php_database', 'php_version')->where('user_id', '=', $user->id)->first();
        return response()->json(compact('info'));
    }
}
