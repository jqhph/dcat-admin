<?php

namespace Dcat\Admin\Grid\Concerns;

use Closure;
use Dcat\Admin\Grid\Tools;

trait HasTools
{
    /**
     * Header tools.
     *
     * @var Tools
     */
    protected $tools;

    /**
     * Setup grid tools.
     */
    public function setupTools()
    {
        $this->tools = new Tools($this);
    }

    /**
     * @return Tools
     */
    public function getTools()
    {
        return $this->tools;
    }

    /**
     * Setup grid tools.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function tools(Closure $callback)
    {
        call_user_func($callback, $this->tools);

        return $this;
    }

    /**
     * Set grid batch-action callback.
     *
     * @param Closure $closure
     *
     * @return $this
     */
    public function batchActions(Closure $closure)
    {
        $this->tools(function (Tools $tools) use ($closure) {
            $tools->batch($closure);
        });

        return $this;
    }

    /**
     * Render custom tools.
     *
     * @return string
     */
    public function renderTools()
    {
        return $this->tools->render();
    }

    /**
     * @param bool $val
     *
     * @return mixed
     */
    public function disableToolbar(bool $val = true)
    {
        return $this->option('show_toolbar', !$val);
    }

    /**
     * @param bool $val
     *
     * @return mixed
     */
    public function showToolbar(bool $val = true)
    {
        return $this->disableToolbar(!$val);
    }

    /**
     * Disable batch actions.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableBatchActions(bool $disable = true)
    {
        $this->tools->disableBatchActions($disable);

        return $this;
    }

    /**
     * Show batch actions.
     *
     * @param bool $val
     *
     * @return $this
     */
    public function showBatchActions(bool $val = true)
    {
        return $this->disableBatchActions(!$val);
    }

    /**
     * Disable batch delete.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableBatchDelete(bool $disable = true)
    {
        $this->tools->batch(function ($action) use ($disable) {
            $action->disableDelete($disable);
        });

        return $this;
    }

    /**
     * Show batch delete.
     *
     * @param bool $val
     *
     * @return $this
     */
    public function showBatchDelete(bool $val = true)
    {
        return $this->disableBatchDelete(!$val);
    }

    /**
     * Disable refresh button.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableRefreshButton(bool $disable = true)
    {
        $this->tools->disableRefreshButton($disable);

        return $this;
    }

    /**
     * Show refresh button.
     *
     * @param bool $val
     *
     * @return $this
     */
    public function showRefreshButton(bool $val = true)
    {
        return $this->disableRefreshButton(!$val);
    }


    /**
     * If grid show toolbar.
     *
     * @return bool
     */
    public function allowToolbar()
    {
        if (
            $this->option('show_toolbar')
            && (
                $this->getTools()->has()
                || $this->allowExporter()
                || $this->allowCreateBtn()
                || $this->allowQuickCreateBtn()
                || $this->allowResponsive()
                || !empty($this->variables['title'])
            )
        ) {
            return true;
        }

        return false;
    }
}
