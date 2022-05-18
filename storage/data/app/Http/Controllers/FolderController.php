<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Folder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function  __construct() {
        $this->middleware(['auth:api']);
    }

    public function getFolder(Request $request) {
        $path = '';
        $allowedDirectories = [];
        $directories = Storage::disk('data')->allDirectories($path);
        $files = Storage::disk('data')->allFiles($path);
        if (!Auth::user()->hasRole('Admin')) {
            foreach (Auth::user()->roles()->get() as $role) {
                $role = $role->name;
                foreach(Folder::all() as $folder) {
                    if ($folder->hasRole($role) && !in_array($folder->path, $allowedDirectories)) array_push($allowedDirectories, $folder->path);
                }
            }
            $newDirectories = [];
            $newFiles = [];
            foreach($directories as $key => $directory) {
                foreach($allowedDirectories as $allowedDirectory) {
                    if (str_starts_with($directory, $allowedDirectory . '/') || $directory === $allowedDirectory) array_push($newDirectories, $directories[$key]);
                }
            }
            foreach($files as $key => $file) {
                foreach($allowedDirectories as $allowedDirectory) {
                    if (str_starts_with($file, $allowedDirectory . '/')) array_push($newFiles, $files[$key]);
                }
            }
            $directories = $newDirectories;
            $files = $newFiles;
        }
        $accessDirectories = [];
        foreach (Folder::all() as $accessDirectory) {
            $newDirectory = [];
            $newDirectory['path'] = $accessDirectory->path;
            $roles = [];
            foreach($accessDirectory->roles()->get() as $role) {
                array_push($roles, $role->name);
            }
            $newDirectory['roles'] = $roles;
            array_push($accessDirectories, $newDirectory);
        }
        return response()->json(compact('directories', 'files', 'accessDirectories'));
    }

    public function setFolderRole(Request $request) {
        if (($folder = Folder::where('path', '=', $request->input('path'))->get())->count() === 0) {
            $folder = Folder::create([
                'path' => $request->input('path'),
            ]);
        } else {
            $folder = Folder::where('path', '=', $request->input('path'))->get()[0];
        }
        $folder->roles()->detach();
        if ($request->input('roles')) {
            foreach ($request->input('roles') as $role) {
                $role = Role::find($role);
                $folder->roles()->attach($role);
            }
        }
        $accessDirectories = [];
        foreach (Folder::all() as $accessDirectory) {
            $newDirectory = [];
            $newDirectory['path'] = $accessDirectory->path;
            $roles = [];
            foreach($accessDirectory->roles()->get() as $role) {
                array_push($roles, $role->name);
            }
            $newDirectory['roles'] = $roles;
            array_push($accessDirectories, $newDirectory);
        }
        return response()->json(compact('accessDirectories'));
    }
}
