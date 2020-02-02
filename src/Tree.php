<?php

namespace Dcat\Admin;

use Closure;
use Dcat\Admin\Traits\HasBuilderEvents;
use Dcat\Admin\Tree\AbstractTool;
use Dcat\Admin\Tree\Tools;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Macroable;

class Tree implements Renderable
{
    use HasBuilderEvents,
        Macroable;

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var string
     */
    protected $elementId = 'tree-';

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var \Closure
     */
    protected $queryCallback;

    /**
     * View of tree to render.
     *
     * @var string
     */
    protected $view = [
        'tree'   => 'admin::tree',
        'branch' => 'admin::tree.branch',
    ];

    /**
     * @var \Closure
     */
    protected $callback;

    /**
     * @var null
     */
    protected $branchCallback = null;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $url;

    /**
     * @var bool
     */
    public $useCreate = true;

    /**
     * @var bool
     */
    public $useQuickCreate = true;

    /**
     * @var array
     */
    public $dialogFormDimensions = ['700px', '620px'];

    /**
     * @var bool
     */
    public $useSave = true;

    /**
     * @var bool
     */
    public $useRefresh = true;

    /**
     * @var bool
     */
    public $useEdit = true;

    /**
     * @var bool
     */
    public $useQuickEdit = true;

    /**
     * @var bool
     */
    public $useDelete = true;

    /**
     * @var array
     */
    protected $nestableOptions = [];

    /**
     * Header tools.
     *
     * @var Tools
     */
    public $tools;

    /**
     * @var Closure
     */
    protected $wrapper;

    /**
     * Menu constructor.
     *
     * @param Model|null $model
     */
    public function __construct(Model $model = null, ?\Closure $callback = null)
    {
        $this->model = $model;
        $this->path = $this->path ?: request()->getPathInfo();
        $this->url = url($this->path);

        $this->elementId .= uniqid();

        $this->setupTools();
        $this->collectAssets();

        if ($callback instanceof \Closure) {
            call_user_func($callback, $this);
        }

        $this->callResolving();
    }

    /**
     * Setup tree tools.
     */
    public function setupTools()
    {
        $this->tools = new Tools($this);
    }

    protected function collectAssets()
    {
        Admin::collectComponentAssets('jquery.nestable');
    }

    /**
     * Initialize branch callback.
     *
     * @return void
     */
    protected function setDefaultBranchCallback()
    {
        if (is_null($this->branchCallback)) {
            $this->branchCallback = function ($branch) {
                $key = $branch[$this->model->getKeyName()];
                $title = $branch[$this->model->getTitleColumn()];

                return "$key - $title";
            };
        }
    }

    /**
     * Set branch callback.
     *
     * @param \Closure $branchCallback
     *
     * @return $this
     */
    public function branch(\Closure $branchCallback)
    {
        $this->branchCallback = $branchCallback;

        return $this;
    }

    /**
     * Set query callback this tree.
     *
     * @return Model
     */
    public function query(\Closure $callback)
    {
        $this->queryCallback = $callback;

        return $this;
    }

    /**
     * Set nestable options.
     *
     * @param array $options
     *
     * @return $this
     */
    public function nestable($options = [])
    {
        $this->nestableOptions = array_merge($this->nestableOptions, $options);

        return $this;
    }

    /**
     * Disable create.
     *
     * @return void
     */
    public function disableCreateButton()
    {
        $this->useCreate = false;
    }

    public function disableQuickCreateButton()
    {
        $this->useQuickCreate = false;
    }

    /**
     * @param string $width
     * @param string $height
     *
     * @return $this
     */
    public function setDialogFormDimensions(string $width, string $height)
    {
        $this->dialogFormDimensions = [$width, $height];

        return $this;
    }

    /**
     * Disable save.
     *
     * @return void
     */
    public function disableSaveButton()
    {
        $this->useSave = false;
    }

    /**
     * Disable refresh.
     *
     * @return void
     */
    public function disableRefreshButton()
    {
        $this->useRefresh = false;
    }

    public function disableQuickEditButton()
    {
        $this->useQuickEdit = false;
    }

    public function disableEditButton()
    {
        $this->useEdit = false;
    }

    public function disableDeleteButton()
    {
        $this->useDelete = false;
    }

    /**
     * @param Closure $closure
     *
     * @return $this;
     */
    public function wrap(\Closure $closure)
    {
        $this->wrapper = $closure;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasWrapper()
    {
        return $this->wrapper ? true : false;
    }

    /**
     * Save tree order from a input.
     *
     * @param string $serialize
     *
     * @return bool
     */
    public function saveOrder($serialize)
    {
        $tree = json_decode($serialize, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        $this->model->saveOrder($tree);

        return true;
    }

    /**
     * Build tree grid scripts.
     *
     * @return string
     */
    protected function script()
    {
        $deleteConfirm = trans('admin.delete_confirm');
        $saveSucceeded = trans('admin.save_succeeded');
        $deleteSucceeded = trans('admin.delete_succeeded');
        $confirm = trans('admin.confirm');
        $cancel = trans('admin.cancel');

        $nestableOptions = json_encode($this->nestableOptions);

        return <<<JS
$('#{$this->elementId}').nestable($nestableOptions);

$('.tree_branch_delete').click(function() {
    var id = $(this).data('id');
    
     LA.confirm("$deleteConfirm", function () {
        LA.NP.start();
        $.ajax({
            method: 'post',
            url:  '{$this->url}/' + id,
            data: {
                _method:'delete',
                _token:LA.token,
            },
            success: function (data) {
                LA.NP.done();
                if (typeof data === 'object') {
                    if (data.status) {
                        LA.reload();
                        LA.success("$deleteSucceeded");
                    } else {
                        LA.error(data.message || 'Delete failed.');
                    }
                }
            }
        });
    }, "$confirm", "$cancel");
});

$('.{$this->elementId}-save').click(function () {
    var serialize = $('#{$this->elementId}').nestable('serialize');
    LA.NP.start();
    $.post('{$this->url}', {
        _token: LA.token,
        _order: JSON.stringify(serialize)
    },
    function(data){
        LA.NP.done();
        LA.reload();
        LA.success('{$saveSucceeded}');
    });
});

$('.{$this->elementId}-tree-tools').on('click', function(e){
    var action = $(this).data('action');
    if (action === 'expand') {
        $('.dd').nestable('expandAll');
    }
    if (action === 'collapse') {
        $('.dd').nestable('collapseAll');
    }
});

JS;
    }

    /**
     * Set view of tree.
     *
     * @param string $view
     */
    public function view($view)
    {
        $this->view = $view;
    }

    /**
     * Return all items of the tree.
     *
     * @param array $items
     */
    public function getItems()
    {
        return $this->model->withQuery($this->queryCallback)->toTree();
    }

    /**
     * Variables in tree template.
     *
     * @return array
     */
    public function variables()
    {
        return [
            'id'             => $this->elementId,
            'tools'          => $this->tools->render(),
            'items'          => $this->getItems(),
            'useCreate'      => $this->useCreate,
            'useQuickCreate' => $this->useQuickCreate,
            'useSave'        => $this->useSave,
            'useRefresh'     => $this->useRefresh,
            'useEdit'        => $this->useEdit,
            'useQuickEdit'   => $this->useQuickEdit,
            'useDelete'      => $this->useDelete,
            'createButton'   => $this->renderCreateButton(),
        ];
    }

    /**
     * Setup tools.
     *
     * @param Closure|array|AbstractTool|Renderable|Htmlable|string $callback
     *
     * @return $this|Tools
     */
    public function tools($callback = null)
    {
        if ($callback === null) {
            return $this->tools;
        }

        if ($callback instanceof \Closure) {
            call_user_func($callback, $this->tools);

            return $this;
        }

        if (! is_array($callback)) {
            $callback = [$callback];
        }

        foreach ($callback as $tool) {
            $this->tools->add($tool);
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function renderCreateButton()
    {
        if (! $this->useQuickCreate && ! $this->useCreate) {
            return '';
        }

        $url = $this->url.'/create';
        $new = trans('admin.new');

        $quickBtn = $btn = '';
        if ($this->useCreate) {
            $btn = "<a href='{$url}' class='btn btn-sm btn-success'><i class='ti-plus'></i><span class='hidden-xs'>&nbsp;&nbsp;{$new}</span></a>";
        }

        if ($this->useQuickCreate) {
            $text = $this->useCreate ? '<i class=\' fa fa-clone\'></i>' : "<i class='ti-plus'></i><span class='hidden-xs'> &nbsp; $new</span>";
            $quickBtn = "<a data-url='$url' class='btn btn-sm btn-success tree-quick-create'>$text</a>";
        }

        return "<div class='btn-group pull-right' style='margin-right:3px'>{$btn}{$quickBtn}</div>";
    }

    /**
     * @return void
     */
    protected function renderQuickEditButton()
    {
        if ($this->useQuickEdit) {
            [$width, $height] = $this->dialogFormDimensions;

            Form::modal(trans('admin.edit'))
                ->click('.tree-quick-edit')
                ->success('LA.reload()')
                ->dimensions($width, $height)
                ->render();
        }
    }

    /**
     * @return void
     */
    protected function renderQuickCreateButton()
    {
        if ($this->useQuickCreate) {
            [$width, $height] = $this->dialogFormDimensions;

            Form::modal(trans('admin.new'))
                ->click('.tree-quick-create')
                ->success('LA.reload()')
                ->dimensions($width, $height)
                ->render();
        }
    }

    /**
     * Render a tree.
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function render()
    {
        try {
            $this->callResolving();

            $this->setDefaultBranchCallback();

            $this->renderQuickEditButton();
            $this->renderQuickCreateButton();

            Admin::script($this->script());

            view()->share([
                'path'           => $this->url,
                'keyName'        => $this->model->getKeyName(),
                'branchView'     => $this->view['branch'],
                'branchCallback' => $this->branchCallback,
            ]);

            return $this->doWrap();
        } catch (\Throwable $e) {
            return Admin::makeExceptionHandler()->renderException($e);
        }
    }

    /**
     * @return string
     */
    protected function doWrap()
    {
        $view = view($this->view['tree'], $this->variables());

        if (! $wrapper = $this->wrapper) {
            return "<div class='card da-box'>{$view->render()}</div>";
        }

        return $wrapper($view);
    }

    /**
     * Get the string contents of the grid view.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Create a tree instance.
     *
     * @param mixed ...$param
     *
     * @return $this
     */
    public static function make(...$param)
    {
        return new static(...$param);
    }
}
