<?php

namespace Dcat\Admin\Models\Repositories;

use Dcat\Admin\Admin;
use Dcat\Admin\Extension as AbstractExtension;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\Repository;
use Dcat\Admin\Show;
use Dcat\Admin\Support\Composer;

class Extension extends Repository
{
    public function get(Grid\Model $model)
    {
        $data = [];
        foreach (Admin::extensions() as $class) {
            $data[] = $this->each($class::make());
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
        $property = Composer::parse($extension->composer());

        $config = (array) config('admin-extensions.'.$extension->getName());

        return [
            'id'           => $extension::NAME,
            'alias'        => $extension->getName(),
            'name'         => $property->name,
            'version'      => Composer::getVersion($property->name),
            'description'  => $property->description,
            'authors'      => $property->authors,
            'require'      => $property->require,
            'require_dev'  => $property->require_dev,
            'homepage'     => $property->homepage,
            'enable'       => $extension::enabled(),
            'config'       => (array) $extension->config(),
            'imported'     => $config['imported'] ?? false,
            'imported_at'  => $config['imported_at'] ?? null,
        ];
    }

    public function edit(Form $form): array
    {
        return [];
    }

    public function update(Form $form)
    {
        $id = $form->key();

        $extension = Admin::extensions()[$id] ?? null;

        if (! $extension) {
            return false;
        }

        $attributes = $form->updates();

        $enable = (bool) ($attributes['enable'] ?? false);

        Admin::enableExtenstion($extension, $enable);

        return true;
    }

    /**
     * Get data before update.
     *
     * @param Form $form
     *
     * @return array
     */
    public function getDataWhenUpdating(Form $form): array
    {
        return [];
    }

    public function detail(Show $show): array
    {
        return [];
    }

    public function destroy(Form $form, array $deletingData)
    {
    }

    public function store(Form $form)
    {
    }

    public function getDataWhenDeleting(Form $form): array
    {
        return [];
    }
}
