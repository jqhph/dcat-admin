<?php

namespace Dcat\Admin;

use Closure;
use Dcat\Admin\Contracts\TreeRepository;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Traits\HasBuilderEvents;
use Dcat\Admin\Tree\AbstractTool;
use Dcat\Admin\Tree\Tools;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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
     * @var TreeRepository
     */
    protected $repository;

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
        'tree'   => 'admin::tree.container',
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
     * @param Model|TreeRepository|string|null $model
     */
    public function __construct($repository = null, ?\Closure $callback = null)
    {
        $this->repository = $this->makeRepository($repository);
        $this->path = $this->path ?: request()->getPathInfo();
        $this->url = url($this->path);

        $this->elementId .= Str::random(8);

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

    /**
     * @param $repository
     *
     * @return TreeRepository
     */
    public function makeRepository($repository)
    {
        if (is_string($repository)) {
            $repository = new $repository();
        }

        if ($repository instanceof Model || $repository instanceof Builder) {
            $repository = EloquentRepository::make($repository);
        }

        if (! $repository instanceof TreeRepository) {
            $class = get_class($repository);

            throw new \InvalidArgumentException("The class [{$class}] must be a type of [".TreeRepository::class.'].');
        }

        return $repository;
    }

    /**
     * Collect assets.
     */
    protected function collectAssets()
    {
        Admin::collectAssets('jquery.nestable');
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
                $key = $branch[$this->repository->getPrimaryKeyColumn()];
                $title = $branch[$this->repository->getTitleColumn()];

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
     * @return $this
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

        $this->repository->saveOrder($tree);

        return true;
    }

    /**
     * Build tree grid scripts.
     *
     * @return string
     */
    protected function script()
    {
        $saveSucceeded = trans('admin.save_succeeded');
        $nestableOptions = json_encode($this->nestableOptions);

        return <<<JS
(function () {
    var tree = $('#{$this->elementId}');
    
    tree.nestable($nestableOptions);

    $('.{$this->elementId}-save').on('click', function () {
        var serialize = tree.nestable('serialize'), _this = $(this);
        _this.buttonLoading();
        $.post('{$this->url}', {
            _order: JSON.stringify(serialize)
        },
        function (data) {
            _this.buttonLoading(false);
            Dcat.success('{$saveSucceeded}');
            
            if (typeof data.location !== "undefined") {
                return setTimeout(function () {
                    if (data.location) {
                        location.href = data.location;
                    } else {
                        location.reload();
                    }
                }, 1500)
            }
            
            Dcat.reload();
        });
    });
    
    $('.{$this->elementId}-tree-tools').on('click', function(e){
        var action = $(this).data('action');
        if (action === 'expand') {
            tree.nestable('expandAll');
        }
        if (action === 'collapse') {
            tree.nestable('collapseAll');
        }
    });
})()
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
        return $this->repository->withQuery($this->queryCallback)->toTree();
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
            $btn = "<a href='{$url}' class='btn btn-sm btn-primary'><i class='feather icon-plus'></i><span class='d-none d-sm-inline'>&nbsp;{$new}</span></a>";
        }

        if ($this->useQuickCreate) {
            $text = $this->useCreate ? '<i class=\' fa fa-clone\'></i>' : "<i class='feather icon-plus'></i><span class='d-none d-sm-inline'>&nbsp; $new</span>";
            $quickBtn = "<button data-url='$url' class='btn btn-sm btn-primary tree-quick-create'>$text</button>";
        }

        return "&nbsp;<div class='btn-group pull-right' style='margin-right:3px'>{$btn}{$quickBtn}</div>";
    }

    /**
     * @return void
     */
    protected function renderQuickEditButton()
    {
        if ($this->useQuickEdit) {
            [$width, $height] = $this->dialogFormDimensions;

            Form::dialog(trans('admin.edit'))
                ->click('.tree-quick-edit')
                ->success('Dcat.reload()')
                ->dimensions($width, $height);
        }
    }

    /**
     * @return void
     */
    protected function renderQuickCreateButton()
    {
        if ($this->useQuickCreate) {
            [$width, $height] = $this->dialogFormDimensions;

            Form::dialog(trans('admin.new'))
                ->click('.tree-quick-create')
                ->success('Dcat.reload()')
                ->dimensions($width, $height);
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
                'currentUrl'     => $this->url,
                'keyName'        => $this->repository->getKeyName(),
                'branchView'     => $this->view['branch'],
                'branchCallback' => $this->branchCallback,
            ]);

            return $this->doWrap();
        } catch (\Throwable $e) {
            return Admin::makeExceptionHandler()->handle($e);
        }
    }

    /**
     * @return string
     */
    protected function doWrap()
    {
        $view = view($this->view['tree'], $this->variables());

        if (! $wrapper = $this->wrapper) {
            return "<div class='card dcat-box'>{$view->render()}</div>";
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
