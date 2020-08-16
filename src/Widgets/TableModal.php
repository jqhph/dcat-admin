<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Grid\LazyRenderable;
use Illuminate\Contracts\Support\Renderable;

/**
 * Class TableModal.
 *
 * @method $this title(string $title)
 * @method $this button(string|\Closure $title)
 * @method $this join(bool $value = true)
 * @method $this xl()
 * @method $this on(string $script)
 * @method $this onShown(string $script)
 * @method $this onShow(string $script)
 * @method $this onHidden(string $script)
 * @method $this onHide(string $script)
 * @method $this footer(string|\Closure|Renderable $footer)
 * @method $this getElementSelector()
 */
class TableModal extends Widget
{
    /**
     * @var Modal
     */
    protected $modal;

    /**
     * @var AsyncTable
     */
    protected $table;

    /**
     * @var array
     */
    protected $allowMethods = [
        'id',
        'title',
        'button',
        'join',
        'xl',
        'on',
        'onShown',
        'onShow',
        'onHidden',
        'onHide',
        'getElementSelector',
        'footer',
    ];

    /**
     * @var string
     */
    protected $loadScript;

    /**
     * TableModal constructor.
     *
     * @param null $title
     * @param \Dcat\Admin\Grid\LazyRenderable|null $renderable
     */
    public function __construct($title = null, LazyRenderable $renderable = null)
    {
        $this->modal = Modal::make()
            ->lg()
            ->title($title)
            ->class('grid-modal', true);

        $this->body($renderable);
    }

    /**
     * 设置异步表格实例.
     *
     * @param LazyRenderable|null $renderable
     *
     * @return $this
     */
    public function body(?LazyRenderable $renderable)
    {
        if (! $renderable) {
            return $this;
        }

        $this->table = AsyncTable::make($renderable, false);

        $this->modal->body($this->table);

        return $this;
    }

    /**
     * 设置或获取ID.
     *
     * @param string|null $id
     *
     * @return |string
     */
    public function id(string $id = null)
    {
        $result = $this->modal->id($id);

        if ($id === null) {
            return $result;
        }

        return $this;
    }

    /**
     * 监听弹窗异步渲染完成事件.
     *
     * @param string $script
     *
     * @return $this
     */
    public function onLoad(string $script)
    {
        $this->loadScript .= $script.';';

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        if ($this->runScript) {
            $this->modal->onShow($this->table->getLoadScript());
        }

        if ($this->loadScript) {
            $this->table->onLoad($this->loadScript);
        }

        $this->table->runScript($this->runScript);
        $this->modal->runScript($this->runScript);

        return $this->modal->render();
    }

    /**
     * {@inheritdoc}
     */
    public function getScript()
    {
        return parent::getScript()
            .$this->modal->getScript()
            .$this->table->getScript();
    }

    /**
     * @return Modal
     */
    public function getModal()
    {
        return $this->modal;
    }

    /**
     * @return AsyncTable
     */
    public function getTable()
    {
        return $this->table;
    }

    public static function __callStatic($method, $arguments)
    {
        return static::make()->$method(...$arguments);
    }

    public function __call($method, $parameters)
    {
        if (in_array($method, $this->allowMethods, true)) {
            $result = $this->modal->$method(...$parameters);

            if (in_array($method, ['getElementSelector'], true)) {
                return $result;
            }

            return $this;
        }

        throw new \Exception(
            sprintf('Call to undefined method "%s::%s"', static::class, $method)
        );
    }
}
