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
    }

    public function uploadFile(Request $request) {
        $fileName = time().'.'.$request->file->getClientOriginalExtension();
        $request->file->move(public_path('upload'), $fileName);
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

    // editFile API
    public function editfile(string $content, string $path) {
        file_put_contents($path, $content);
    }
    //
}
