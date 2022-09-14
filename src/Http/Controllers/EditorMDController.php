<?php

namespace Dcat\Admin\Http\Controllers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EditorMDController
{
    public function upload(Request $request)
    {
        $file = $request->file('editormd-image-file');
        $dir = trim($request->get('dir'), '/');
        $disk = $this->disk();

        $newName = $this->generateNewName($file);

        $disk->putFileAs($dir, $file, $newName);

        return ['success' => 1, 'url' => $disk->url("{$dir}/$newName")];
    }

    protected function generateNewName(UploadedFile $file)
    {
        return uniqid(md5($file->getClientOriginalName())).'.'.$file->getClientOriginalExtension();
    }

    /**
     * @return \Illuminate\Contracts\Filesystem\Filesystem|FilesystemAdapter
     */
    protected function disk()
    {
        $disk = request()->get('disk') ?: config('admin.upload.disk');

        return Storage::disk($disk);
    }
}
