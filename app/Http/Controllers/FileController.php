<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use File;

class FileController extends Controller
{

    public function  __construct() {
        $this->middleware(['auth:api']);
        $user = Auth::user();
        $this->subDomain = WebsiteUser::where('user_id', '=', $user->id)->first()->sub_domain;
        $path = $this->subDomain.'/'.$this->subDomain;
    }

    public function uploadFile(Request $request) {
        $file = $request->file('file');
        $zip = new ZipArchive;
        $zip->open($request->file('file'));
        $zip->extractTo(Storage::disk('data')->path(''));
        $zip->close();
        return response('Success', 200);
    }

    public function downloadFile(Request $request) {
        $zip = new ZipArchive;
        $files = explode(',', $request->input('path'));
        $fileName = 'contents.zip';
        if ($zip->open(Storage::disk('data')->path($fileName), ZipArchive::CREATE) == TRUE) {
            foreach ($files as $file) {
                if (File::isDirectory(Storage::disk('data')->path($file))) {
                    $directoryPath = explode('/', $file);
                    $this->addContent($zip, Storage::disk('data')->path($file), end($directoryPath));
                } else {
                    $relativeName = basename($file);
                    $zip->addFile(Storage::disk('data')->path($file), $relativeName);
                }
            }
            $zip->close();
        }
        return response()->download(Storage::disk('data')->path($fileName))->deleteFileAfterSend(true);
    }

    public function readFile(Request $request) {
        return Storage::disk('data')->download($request->input('path'));
    }

    public function createFolder(Request $request) {
        Storage::disk('data')->makeDirectory($request->input('newFileName'));
    }

    public function createFile(Request $request) {
        Storage::disk('data')->put($request->input('newFileName'), '');
    }

    public function renameFile(Request $request) {
        Storage::disk('data')->move($request->input('oldFileName'), $request->input('newFileName'));
    }

    public function deleteFiles(Request $request) {
        foreach ($request->input('fileNames') as $file) {
            if (File::isDirectory(Storage::disk('data')->path($file))) {
                File::deleteDirectory(Storage::disk('data')->path($file));
            } else {
                Storage::disk('data')->delete($file);
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
        file_put_contents(Storage::disk('data')->path($request->input('path')), $request->input('content'));
    }
}
