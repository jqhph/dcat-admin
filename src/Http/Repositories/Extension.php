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

        return [
            'id'           => $extension->getName(),
            'alias'        => $extension->getName(),
            'name'         => $property->name,
            'version'      => $extension->getVersion(),
            'description'  => $property->description,
            'authors'      => $property->authors,
            'homepage'     => $property->homepage,
            'enable'       => $extension->enabled(),
        ];
    }

    public function edit(Form $form): array
    {
        return [];
    }

    public function update(Form $form)
    {
        $id = $form->getKey();

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
