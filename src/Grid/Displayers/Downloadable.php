<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Facades\Storage;

class Downloadable extends AbstractDisplayer
{
    public function display($server = '', $disk = null)
    {
        return collect(Helper::array($this->value))->filter()->map(function ($value) use ($server, $disk) {
            if (empty($value)) {
                return '';
            }

            if (url()->isValidUrl($value)) {
                $src = $value;
            } elseif ($server) {
                $src = rtrim($server, '/').'/'.ltrim($value, '/');
            } else {
                $src = Storage::disk($disk ?: config('admin.upload.disk'))->url($value);
            }

            $name = Helper::basename($value);

            return <<<HTML
<a href='$src' download='{$name}' target='_blank' class='text-muted'>
    <i class="feather icon-download"></i> {$name}
</a>
HTML;
        })->implode('<br>');
    }
}
