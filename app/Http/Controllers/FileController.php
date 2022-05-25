<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use File;
use App\Models\WebsiteUser;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{

    public function  __construct() {
        $this->middleware(['auth:api']);
        $user = Auth::user();
        $subDomain = WebsiteUser::where('user_id', '=', $user->id)->first()->sub_domain;
        $this->path = $subDomain.'/'.$subDomain;
    }

    public function uploadFile(Request $request) {
        $file = $request->file('file');
        $zip = new ZipArchive;
        $zip->open($request->file('file'));
        $zip->extractTo(Storage::disk('data')->path($this->path . '/'));
        $zip->close();
        return response('Success', 200);
    }

    public function downloadFile(Request $request) {
        $zip = new ZipArchive;
        $files = explode(',', $request->input('path'));
        $fileName = 'contents.zip';
        if ($zip->open(Storage::disk('data')->path($this->path . '/' . $fileName), ZipArchive::CREATE) == TRUE) {
            foreach ($files as $file) {
                if (File::isDirectory(Storage::disk('data')->path($this->path . '/' . $file))) {
                    $directoryPath = explode('/', $this->path . '/' . $file);
                    $this->addContent($zip, Storage::disk('data')->path($this->path . '/' . $file), end($directoryPath));
                } else {
                    $relativeName = basename($this->path . '/' . $file);
                    $zip->addFile(Storage::disk('data')->path($this->path . '/' . $file), $relativeName);
                }
            }
            $zip->close();
        }
        return response()->download(Storage::disk('data')->path($this->path . '/' . $fileName))->deleteFileAfterSend(true);
    }

    public function readFile(Request $request) {
        return Storage::disk('data')->download($this->path . '/' . $request->input('path'));
    }

    public function createFolder(Request $request) {
        Storage::disk('data')->makeDirectory($this->path . '/' . $request->input('newFileName'));
    }
    
    public function createFile(Request $request) {
        Storage::disk('data')->put($this->path . '/' . $request->input('newFileName'), '');
    }

    public function renameFile(Request $request) {
        Storage::disk('data')->move($this->path . '/' . $request->input('oldFileName'), $this->path . '/' . $request->input('newFileName'));
    }

    public function deleteFiles(Request $request) {
        foreach ($request->input('fileNames') as $file) {
            if (File::isDirectory(Storage::disk('data')->path($this->path . '/' . $file))) {
                File::deleteDirectory(Storage::disk('data')->path($this->path . '/' . $file));
            } else {
                Storage::disk('data')->delete($this->path . '/' . $file);
            }
        }
    }

    private function addContent(\ZipArchive $zip, string $path, string $folder)
    {
        /** @var SplFileInfo[] $files */
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $path,
                \FilesystemIterator::FOLLOW_SYMLINKS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        while ($iterator->valid()) {
            if (!$iterator->isDot()) {
                $filePath = $iterator->getPathName();
                $relativePath = $folder . '\\' . substr($filePath, strlen($path) + 1);

                if (!$iterator->isDir()) {
                    $zip->addFile($filePath, $relativePath);
                } else {
                    if ($relativePath !== false) {
                        $zip->addEmptyDir($relativePath);
                    }
                }
            }
            $iterator->next();
        }
    }

    public function editFile(Request $request) {
        file_put_contents(Storage::disk('data')->path($this->path . '/' . $request->input('path')), $request->input('content'));
    }
}