<?php

namespace Dcat\Admin\Http\Repositories;

use Dcat\Admin\Admin;
use Dcat\Admin\Extend\ServiceProvider as AbstractExtension;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\Repository;
use Dcat\Admin\Show;

class Extension extends Repository
{
    public function get(Grid\Model $model)
    {
        $data = [];
        foreach (Admin::extension()->all() as $extension) {
            $data[] = $this->each($extension);
        }

        return $data;
    }

    /**
     * @param AbstractExtension $extension
     *
     * @return array
     */
    protected function each(AbstractExtension $extension)
    {
        $property = $extension->composerProperty;

        $name = $extension->getName();
        $current = $extension->getVersion();
        $latest = $extension->getLocalLatestVersion();

        return [
            'id'           => $name,
            'alias'        => $extension->getAlias(),
            'logo'         => $extension->getLogoBase64(),
            'name'         => $name,
            'version'      => $current,
            'type'         => $extension->getType(),
            'description'  => $property->description,
            'authors'      => $property->authors,
            'homepage'     => $property->homepage,
            'enabled'      => $extension->enabled(),
            'new_version'  => $latest === $current || ! $current ? '' : $latest,
            'extension'    => $extension,
        ];
    }

    public function edit(Form $form): array
    {
        return [];
    }

    public function update(Form $form)
    {
        return true;
    }

    /**
     * Get data before update.
     *
     * @param Form $form
     *
     * @return array
     */
    public function updating(Form $form): array
    {
        return [];
    }

    public function detail(Show $show): array
    {
        return [];
    }

    public function delete(Form $form, array $deletingData)
    {
    }

    public function store(Form $form)
    {
    }

    public function deleting(Form $form): array
    {
        return [];
    }
}
