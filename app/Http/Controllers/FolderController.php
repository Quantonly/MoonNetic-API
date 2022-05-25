<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\WebsiteUser;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function  __construct() {
        $this->middleware(['auth:api']);
        $user = Auth::user();
        $this->subDomain = WebsiteUser::where('user_id', '=', $user->id)->first()->sub_domain;
    }

    public function getFolder(Request $request) {
        $path = $this->subDomain.'/'.$this->subDomain;
        $domain = $this->subDomain;
        $directories = Storage::disk('data')->allDirectories($path);
        $files = Storage::disk('data')->allFiles($path);
        foreach ($directories as $key => $directory) {
            $directories[$key] = str_replace($path.'/', '', $directory);
        }
        foreach ($files as $key => $file) {
           $files[$key] = str_replace($path.'/', '', $file);
        }
        return response()->json(compact('directories', 'files', 'subDomain'));
    }
}