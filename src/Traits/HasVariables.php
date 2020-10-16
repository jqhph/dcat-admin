<?php

namespace Dcat\Admin\Traits;

/**
 * @method array defaultVariables()
 */
trait HasVariables
{
    protected $variables = [];

    /**
     * 获取所有变量.
     *
     * @return array
     */
    public function variables()
    {
        if (! method_exists($this, 'defaultVariables')) {
            return $this->variables;
        }

        return array_merge($this->defaultVariables(), $this->variables);
    }

    /**
     * 设置变量.
     *
     * @param array $variables
     *
     * @return $this
     */
    public function addVariables(array $variables = [])
    {
        $this->variables = array_merge($this->variables, $variables);

        return $this;
    }
}
