<?php

namespace Dcat\Admin\Extend;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

/**
 * @property \Symfony\Component\Console\Output\OutputInterface $output
 */
trait CanImportMenu
{
    protected $menuValidationRules = [
        'parent' => 'nullable',
        'title'  => 'required',
        'uri'    => 'nullable',
        'icon'   => 'nullable',
    ];

    /**
     * 添加菜单
     *
     * @param array $menu
     *
     * @throws \Exception
     */
    protected function addMenu(array $menu)
    {
        if (! Arr::isAssoc($menu)) {
            foreach ($menu as $v) {
                $this->addMenu($v);
            }

            return;
        }

        if (! $this->validateMenu($menu)) {
            return;
        }

        if ($menuModel = $this->getMenuModel()) {
            $lastOrder = $menuModel::max('order');

            $menuModel::create([
                'parent_id' => $this->getParentMenuId($menu['parent'] ?? 0),
                'order'     => $lastOrder + 1,
                'title'     => $menu['title'],
                'icon'      => (string) ($icon ?? ''),
                'uri'       => (string) ($menu['uri'] ?? ''),
                'extension' => $this->getExtensionName(),
            ]);
        }
    }

    /**
     * 根据名称获取菜单ID.
     *
     * @param int|string $parent
     *
     * @return int
     */
    protected function getParentMenuId($parent)
    {
        if (is_numeric($parent)) {
            return $parent;
        }

        $menuModel = $this->getMenuModel();

        return $menuModel::query()
            ->where('title', $parent)
            ->where('extension', $this->getExtensionName())
            ->value('id') ?: 0;
    }

    /**
     * 删除菜单.
     */
    protected function deleteMenu()
    {
        $menuModel = $this->getMenuModel();

        if (! $menuModel) {
            return;
        }

        $menuModel::query()
            ->where('extension', $this->getExtensionName())
            ->delete();
    }

    /**
     * 验证菜单字段格式是否正确.
     *
     * @param array $menu
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function validateMenu(array $menu)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($menu, $this->menuValidationRules);

        if ($validator->passes()) {
            return true;
        }

        return false;
    }

    protected function getMenuModel()
    {
        return config('admin.database.menu_model');
    }
}
