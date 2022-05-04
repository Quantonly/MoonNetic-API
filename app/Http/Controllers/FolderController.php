<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function  __construct() {
        $this->middleware(['auth:api']);
    }

    public function getFolder(Request $request) {
        $path = '';
        $directories = Storage::disk('data')->allDirectories($path);
        $files = Storage::disk('data')->allFiles($path);
        return response()->json(compact('directories', 'files'));
    }
}
