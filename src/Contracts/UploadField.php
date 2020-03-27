<?php

namespace Dcat\Admin\Contracts;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

interface UploadField
{
    /**
     * Upload File.
     *
     * @param UploadedFile $file
     *
     * @return Response
     */
    public function upload(UploadedFile $file);

    /**
     * Destroy original files.
     *
     * @return void.
     */
    public function destroy();

    /**
     * Destroy files.
     *
     * @param string|array $path
     */
    public function deleteFile($path);
}
