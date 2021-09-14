<?php

namespace Dcat\Admin;

use Closure;
use Dcat\Admin\Contracts\TreeRepository;
use Dcat\Admin\Exception\InvalidArgumentException;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasBuilderEvents;
use Dcat\Admin\Traits\HasVariables;
use Dcat\Admin\Tree\AbstractTool;
use Dcat\Admin\Tree\Actions;
use Dcat\Admin\Tree\Tools;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

/**
 * Class Tree.
 *
 * @see https://github.com/dbushell/Nestable
 */
class Tree implements Renderable
{
    use HasBuilderEvents;
    use HasVariables;
    use Macroable;

    const SAVE_ORDER_NAME = '_order';

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
    protected $view = 'admin::tree.container';

    /**
     * @var string
     */
    protected $branchView = 'admin::tree.branch';

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
    public $expand = true;

    /**
     * @var bool
     */
    public $useQuickCreate = true;

    /**
     * @var array
     */
    public $dialogFormDimensions = ['700px', '670px'];

    /**
     * @var bool
     */
    public $useSave = true;

    /**
     * @var bool
     */
    public $useRefresh = true;

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
     * @var string
     */
    protected $actionsClass;

    /**
     * @var \Closure[]
     */
    protected $actionCallbacks = [];

    /**
     * @var Closure
     */
    protected $wrapper;

    /**
     * Menu constructor.
     *
     * @param  Model|TreeRepository|string|null  $model
     */
    public function __construct($repository = null, ?\Closure $callback = null)
    {
        $this->repository = $this->makeRepository($repository);
        $this->path = $this->path ?: request()->getPathInfo();
        $this->url = url($this->path);

        $this->elementId .= Str::random(8);

        $this->setUpTools();

        if ($callback instanceof \Closure) {
            call_user_func($callback, $this);
        }

        $this->callResolving();
    }

    /**
     * Setup tree tools.
     */
    public function setUpTools()
    {
        $this->tools = new Tools($this);
    }

    /**
     * @param $repository
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

            throw new InvalidArgumentException("The class [{$class}] must be a type of [".TreeRepository::class.'].');
        }

        return $repository;
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
     * @param  \Closure  $branchCallback
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
     * number of levels an item can be nested (default 5).
     *
     * @see https://github.com/dbushell/Nestable
     *
     * @param  int  $max
     * @return $this
     */
    public function maxDepth(int $max)
    {
        return $this->nestable(['maxDepth' => $max]);
    }

    /**
     * Set nestable options.
     *
     * @param  array  $options
     * @return $this
     */
    public function nestable($options = [])
    {
        $this->nestableOptions = array_merge($this->nestableOptions, $options);

        return $this;
    }

    /**
     * @param  bool  $value
     * @return void
     */
    public function expand(bool $value = true)
    {
        $this->expand = $value;
    }

    /**
     * Disable create.
     *
     * @param  bool  $value
     * @return void
     */
    public function disableCreateButton(bool $value = true)
    {
        $this->useCreate = ! $value;
    }

    public function showCreateButton(bool $value = true)
    {
        return $this->disableCreateButton(! $value);
    }

    public function disableQuickCreateButton(bool $value = true)
    {
        $this->useQuickCreate = ! $value;
    }

    public function showQuickCreateButton(bool $value = true)
    {
        return $this->disableQuickCreateButton(! $value);
    }

    /**
     * @param  string  $width
     * @param  string  $height
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
     * @param  bool  $value
     * @return void
     */
    public function disableSaveButton(bool $value = true)
    {
        $this->useSave = ! $value;
    }

    public function showSaveButton(bool $value = true)
    {
        return $this->disableSaveButton(! $value);
    }

    /**
     * Disable refresh.
     *
     * @param  bool  $value
     * @return void
     */
    public function disableRefreshButton(bool $value = true)
    {
        $this->useRefresh = ! $value;
    }

    public function showRefreshButton(bool $value = true)
    {
        return $this->disableRefreshButton(! $value);
    }

    public function disableQuickEditButton(bool $value = true)
    {
        $this->actions(function (Actions $actions) use ($value) {
            $actions->disableQuickEdit($value);
        });
    }

    public function showQuickEditButton(bool $value = true)
    {
        return $this->disableQuickEditButton(! $value);
    }

    public function disableEditButton(bool $value = true)
    {
        $this->actions(function (Actions $actions) use ($value) {
            $actions->disableEdit($value);
        });
    }

    public function showEditButton(bool $value = true)
    {
        return $this->disableEditButton(! $value);
    }

    public function disableDeleteButton(bool $value = true)
    {
        $this->actions(function (Actions $actions) use ($value) {
            $actions->disableDelete($value);
        });
    }

    public function showDeleteButton(bool $value = true)
    {
        return $this->disableDeleteButton(! $value);
    }

    /**
     * @param  Closure  $closure
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
     * @param  string  $serialize
     * @return bool
     */
    public function saveOrder($serialize)
    {
        $tree = json_decode($serialize, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        $this->repository->saveOrder($tree);

        return true;
    }

    /**
     * Set view of tree.
     *
     * @param  string  $view
     * @return $this
     */
    public function view($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @param  string  $view
     * @return $this
     */
    public function branchView($view)
    {
        $this->branchView = $view;

        return $this;
    }

    /**
     * @return \Closure
     */
    public function resolveAction()
    {
        return function ($branch) {
            $class = $this->actionsClass ?: Actions::class;

            $action = new $class();

            $action->setParent($this);
            $action->setRow($branch);

            $this->callActionCallbacks($action);

            return $action->render();
        };
    }

    protected function callActionCallbacks(Actions $actions)
    {
        foreach ($this->actionCallbacks as $callback) {
            $callback->call($actions->row, $actions);
        }
    }

    /**
     * 自定义行操作类.
     *
     * @param  string  $actionClass
     * @return $this
     */
    public function setActionClass(string $actionClass)
    {
        $this->actionsClass = $actionClass;

        return $this;
    }

    /**
     * 设置行操作回调.
     *
     * @param  \Closure|array  $callback
     * @return $this
     */
    public function actions($callback)
    {
        if ($callback instanceof \Closure) {
            $this->actionCallbacks[] = $callback;
        } else {
            $this->actionCallbacks[] = function (Actions $actions) use ($callback) {
                if (! is_array($callback)) {
                    $callback = [$callback];
                }

                foreach ($callback as $value) {
                    $actions->append(clone $value);
                }
            };
        }

        return $this;
    }

    /**
     * Return all items of the tree.
     *
     * @param  array  $items
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
    public function defaultVariables()
    {
        return [
            'id'              => $this->elementId,
            'tools'           => $this->tools->render(),
            'items'           => $this->getItems(),
            'useCreate'       => $this->useCreate,
            'useQuickCreate'  => $this->useQuickCreate,
            'useSave'         => $this->useSave,
            'useRefresh'      => $this->useRefresh,
            'createButton'    => $this->renderCreateButton(),
            'nestableOptions' => $this->nestableOptions,
            'url'             => $this->url,
            'resolveAction'   => $this->resolveAction(),
            'expand'          => $this->expand,
        ];
    }

    /**
     * @return mixed
     */
    public function getKeyName()
    {
        return $this->repository->getKeyName();
    }

    /**
     * @return string
     */
    public function resource()
    {
        return $this->url;
    }

    /**
     * Set resource path.
     *
     * @param  string  $path
     * @return $this
     */
    public function setResource($path)
    {
        $this->url = admin_url($path);

        return $this;
    }

    /**
     * Setup tools.
     *
     * @param  Closure|array|AbstractTool|Renderable|Htmlable|string  $callback
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
        $this->callResolving();

        $this->setDefaultBranchCallback();

        $this->renderQuickCreateButton();

        view()->share([
            'currentUrl'     => $this->url,
            'keyName'        => $this->getKeyName(),
            'branchView'     => $this->branchView,
            'branchCallback' => $this->branchCallback,
        ]);

        return $this->doWrap();
    }

    /**
     * @return string
     */
    protected function doWrap()
    {
        $view = view($this->view, $this->variables());

        if (! $wrapper = $this->wrapper) {
            $html = Admin::resolveHtml($view->render())['html'];

            return "<div class='card'>{$html}</div>";
        }

        return Admin::resolveHtml(Helper::render($wrapper($view)))['html'];
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
     * @param  mixed  ...$param
     * @return $this
     */
    public static function make(...$param)
    {
        return new static(...$param);
    }
}
