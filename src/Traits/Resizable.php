<?php

namespace Dcat\Admin\Traits;

use Illuminate\Support\Str;

trait Resizable
{
    /**
     * Method for returning specific thumbnail for model.
     *
     * @param  string  $type
     * @param  string  $attribute
     * @return string|null
     */
    public function thumbnail($type, $attribute = 'image', $disk = null)
    {
        // Return empty string if the field not found
        if (! isset($this->attributes[$attribute])) {
            return '';
        }

        // We take image from posts field
        $image = $this->attributes[$attribute];

        $thumbnail = $this->getThumbnailPath($image, $type);

        return $thumbnail;
    }

    /**
     * Generate thumbnail URL.
     *
     * @param $image
     * @param $type
     * @return string
     */
    public function getThumbnailPath($image, $type)
    {
        // We need to get extension type ( .jpeg , .png ...)
        $ext = pathinfo($image, PATHINFO_EXTENSION);

        // We remove extension from file name so we can append thumbnail type
        $name = Str::replaceLast('.'.$ext, '', $image);

        // We merge original name + type + extension
        return $name.'-'.$type.'.'.$ext;
    }
}
