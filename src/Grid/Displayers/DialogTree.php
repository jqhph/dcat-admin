<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Arrayable;

class DialogTree extends AbstractDisplayer
{
    public static $js = '@jstree';
    public static $css = '@jstree';

    protected $url;

    protected $title;

    protected $area = ['580px', '600px'];

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
            'default' => [
                'icon' => false,
            ],
        ],
    ];

    /**
     * @var array
     */
    protected $columnNames = [
        'id'     => 'id',
        'text'   => 'name',
        'parent' => 'parent_id',
    ];

    protected $nodes = [];

    protected $checkAll;

    /**
     * @param array $data exp:
     *                    {
     *                    "id": "1",
     *                    "parent": "#",
     *                    "text": "Dashboard",
     *                    // "state": {"selected": true}
     *                    }
     * @param array $data
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

    public function url(string $source)
    {
        $this->url = admin_url($source);

        return $this;
    }

    public function checkAll()
    {
        $this->checkAll = true;

        return $this;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function options($options = [])
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $width
     * @param string $height
     *
     * @return $this
     */
    public function area(string $width, string $height)
    {
        $this->area = [$width, $height];

        return $this;
    }

    public function setIdColumn(string $name)
    {
        $this->columnNames['id'] = $name;

        return $this;
    }

    public function setTitleColumn(string $name)
    {
        $this->columnNames['text'] = $name;

        return $this;
    }

    public function setParentColumn(string $name)
    {
        $this->columnNames['parent'] = $name;

        return $this;
    }

    public function display($callbackOrNodes = null)
    {
        if (is_array($callbackOrNodes) || $callbackOrNodes instanceof Arrayable) {
            $this->nodes($callbackOrNodes);
        } elseif ($callbackOrNodes instanceof \Closure) {
            $callbackOrNodes->call($this->row, $this);
        }

        $btn = $this->trans('view');

        $this->setupScript();

        $val = $this->format($this->value);

        return <<<EOF
<a href="javascript:void(0)" class="{$this->getSelectorPrefix()}-open-tree" data-checked="{$this->checkAll}" data-val="{$val}">
    <i class='feather icon-align-right'></i> $btn
</a>
EOF;
    }

    protected function format($val)
    {
        return implode(',', Helper::array($val, true));
    }

    protected function getSelectorPrefix()
    {
        return $this->grid->getName().'_'.$this->column->getName().'_'.$this->getKey();
    }

    protected function setupScript()
    {
        $title = $this->title ?: $this->column->getLabel();

        $area = json_encode($this->area);
        $opts = json_encode($this->options);
        $nodes = json_encode($this->nodes);

        Admin::script(
            <<<JS
$('.{$this->getSelectorPrefix()}-open-tree').off('click').on('click', function () {
    var tpl = '<div class="jstree-wrapper p-1" style="border:0"><div class="da-tree" style="margin-top:10px"></div></div>', 
        url = '{$this->url}',
        t = $(this),
        val = t.data('val'),
        ckall = t.data('checked'),
        idx,
        requesting,
        opts = $opts;

    val = val ? String(val).split(',') : [];
        
    if (url) {
        if (requesting) return;
        requesting = 1;
        
        t.buttonLoading();
        $.ajax(url, {data: {value: val}}).then(function (resp) {
             requesting = 0;
             t.buttonLoading(false);
             
             if (!resp.status) {
                return Dcat.error(resp.message || '系统繁忙，请稍后再试');
             }
             
             build(resp.value);
        });
    } else {
        build(val);
    }    
        
    function build(val) {
        opts.core.data = formatNodes(val, $nodes);    
    
        idx = layer.open({
            type: 1,
            area: {$area},
            content: tpl,
            title: '{$title}',
            success: function (a, idx) {
                var tree = $('#layui-layer'+idx).find('.da-tree');
                
                tree.on("loaded.jstree", function () {
                    tree.jstree('open_all');
                }).jstree(opts);
            }
        });
        
        $(document).one('pjax:complete', function () { 
            layer.close(idx);
        });
    }
    
    function formatNodes(value, all) {
        var idColumn = '{$this->columnNames['id']}', 
           textColumn = '{$this->columnNames['text']}', 
           parentColumn = '{$this->columnNames['parent']}';
        var parentIds = [], nodes = [], i, v, parentId;

        for (i in all) {
            v = all[i];
            if (!v[idColumn]) continue;

            parentId = v[parentColumn] || '#';
            if (!parentId) {
                parentId = '#';
            } else {
                parentIds.push(parentId);
            }

            v['state'] = {'disabled': true};

            if (ckall || (value && Dcat.helpers.inObject(value, v[idColumn]))) {
                v['state']['selected'] = true;
            }

            nodes.push({
                'id'     : v[idColumn],
                'text'   : v[textColumn] || null,
                'parent' : parentId,
                'state'  : v['state'],
            });
        }
       
        return nodes;
    }
});
JS
        );
    }
}
