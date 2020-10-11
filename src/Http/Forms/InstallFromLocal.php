<?php

namespace Dcat\Admin\Http\Forms;

use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class InstallFromLocal extends Form implements LazyRenderable
{
    use LazyWidget;

    public function handle(array $input)
    {
        $file = $input['extension'];

        if (! $file) {
            return $this->response()->error('Invalid arguments.');
        }

        $path = $this->getFilePath($file);

        $manager = Admin::extension();

        $allNames = $manager->all()->keys()->toArray();

        $manager->extract($path);

        $manager->load();

        $newAllNames = $manager->all()->keys()->toArray();

        $diff = array_diff($newAllNames, $allNames);

        if (! $diff) {
            return $this->response()->error(trans('admin.invalid_extension_package'));
        }

        $manager
            ->updateManager()
            ->update(current($diff));

        return $this->response()
            ->success(implode('<br>', $manager->updateManager()->notes))
            ->refresh();
    }

    protected function getFilePath($file)
    {
        $root = config("filesystems.disks.{$this->disk()}.root");

        if (! $root) {
            throw new RuntimeException(sprintf('Invalid configurations of disk [%s], missing "root".', $this->disk()));
        }

        return rtrim($root, '/').'/'.$file;
    }

    protected function disk()
    {
        return config('admin.extension.disk') ?: 'local';
    }

    public function form()
    {
        $this->file('extension')
            ->required()
            ->disk($this->disk())
            ->accept('zip,arc,rar,tar.gz', 'application/zip')
            ->autoUpload();
    }
}
