<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Checkbox as WidgetCheckbox;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class Tree extends Field
{
    protected $options = [
        'plugins' => ['checkbox', 'types'],
        'core'    => [
            'check_callback' => true,

            'themes' => [
                'name'       => 'proton',
                'responsive' => true,
            ],
        ],
        'checkbox' => [
            'keep_selected_style' => false,
        ],
        'types' => [
            'default'  => [
                'icon' => false,
            ],
        ],
    ];

    /**
     * @var array
     */
    protected $nodes = [];

    /**
     * @var array
     */
    protected $parents = [];

    /**
     * @var bool
     */
    protected $expand = true;

    /**
     * @var array
     */
    protected $columnNames = [
        'id'     => 'id',
        'text'   => 'name',
        'parent' => 'parent_id',
    ];

    /**
     * @var bool
     */
    protected $filterParents = true;

    /**
     * @var bool
     */
    protected $readOnly = false;

    /**
     * @param array|Arrayable|\Closure $data exp:
     *                                       {
     *                                       "id": "1",
     *                                       "parent": "#",
     *                                       "text": "Dashboard",
     *                                       // "state": {"selected": true}
     *                                       }
     *
     * @return $this
     */
    public function nodes($data)
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $this->nodes = &$data;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableFilterParents()
    {
        $this->filterParents = false;

        return $this;
    }

    /**
     * Set the field as readonly mode.
     *
     * @return $this
     */
    public function readOnly()
    {
        $this->readOnly = true;

        return $this;
    }

    /**
     * @param string $idColumn
     * @param string $textColumn
     * @param string $parentColumn
     *
     * @return $this
     */
    public function columnNames(string $idColumn = 'id', string $textColumn = 'name', string $parentColumn = 'parent_id')
    {
        $this->columnNames['id'] = $idColumn;
        $this->columnNames['text'] = $textColumn;
        $this->columnNames['parent'] = $parentColumn;

        return $this;
    }

    protected function formatNodes()
    {
        $value = Helper::array(
            old($this->column, $this->value())
        );

        $this->value = &$value;

        if ($this->nodes instanceof \Closure) {
            $this->nodes = $this->nodes->call($this->values(), $this->value(), $this);
        }

        if (! $this->nodes) {
            return;
        }

        $idColumn = $this->columnNames['id'];
        $textColumn = $this->columnNames['text'];
        $parentColumn = $this->columnNames['parent'];

        $parentIds = $nodes = [];

        foreach ($this->nodes as &$v) {
            if (empty($v[$idColumn])) {
                continue;
            }

            $parentId = $v[$parentColumn] ?? '#';
            if (empty($parentId)) {
                $parentId = '#';
            } else {
                $parentIds[] = $parentId;
            }

            $v['state'] = [];

            if ($value && in_array($v[$idColumn], $value)) {
                $v['state']['selected'] = true;
            }

            if ($this->readOnly) {
                $v['state']['disabled'] = true;
            }

            $nodes[] = [
                'id'     => $v[$idColumn],
                'text'   => $v[$textColumn] ?? null,
                'parent' => $parentId,
                'state'  => $v['state'],
            ];
        }

        if ($this->filterParents) {
            // 筛选出所有父节点，最终点击树节点时过滤掉父节点
            $this->parents = array_unique($parentIds);
        }

        $this->nodes = &$nodes;
    }

    /**
     * Set type.
     *
     * @param array $value
     *
     * @return $this
     */
    public function type(array $value)
    {
        $this->options['types'] = array_merge($this->options['types'], $value);

        return $this;
    }

    /**
     * Set plugins.
     *
     * @param array $value
     *
     * @return $this
     */
    public function plugins(array $value)
    {
        $this->options['plugins'] = $value;

        return $this;
    }

    /**
     * Disable expand.
     *
     * @return $this
     */
    public function disableExpand()
    {
        $this->expand = false;

        return $this;
    }

    protected function formatFieldData($data)
    {
        $value = Arr::get($data, $this->column);

        return Helper::array($value, true);
    }

    /**
     * Prepare for saving.
     *
     * @param string|array $value
     *
     * @return array
     */
    protected function prepareToSave($value)
    {
        return Helper::array($value, true);
    }

    public function render()
    {
        $this->attribute('type', 'hidden');

        $checkboxes = new WidgetCheckbox();

        $checkboxes->style('primary');
        $checkboxes->inline();
        $checkboxes->circle(false);
        $checkboxes->options([
            1 => trans('admin.selectall'),
            2 => trans('admin.expand'),
        ]);
        if ($this->readOnly) {
            $checkboxes->disabled(1);
        }

        $this->expand && $checkboxes->checked(2);

        $this->formatNodes();

        if ($v = $this->value()) {
            $this->attribute('value', implode(',', $v));
        }

        $this->addVariables([
            'checkboxes' => $checkboxes,
            'options'    => json_encode($this->options),
            'nodes'      => json_encode($this->nodes),
            'expand'     => $this->expand,
            'disabled'   => empty($this->attributes['disabled']) ? '' : 'disabled',
            'parents'    => json_encode($this->parents),
        ]);

        return parent::render(); // TODO: Change the autogenerated stub
    }

    public static function collectAssets()
    {
        Admin::css('vendor/dcat-admin/jstree-theme/themes/proton/style.min.css');
        Admin::collectComponentAssets('jstree');
    }
}
