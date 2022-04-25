<?php

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Layout\Content;

trait HasNestedResource
{
    /**
     * The id of the nested resource's child model.
     *
     * @var string|int
     */
    protected $nestedResourceId;

    /**
     * The parameter name for the nested resource's route.
     *
     * @var string
     */
    protected $routeParameterName;

    /**
     * {@inheritdoc}
     */
    public function show($id, Content $content)
    {
        return parent::show($this->getNestedResourceId(), $content);
    }

    /**
     * {@inheritdoc}
     */
    public function edit($id, Content $content)
    {
        return parent::edit($this->getNestedResourceId(), $content);
    }

    /**
     * {@inheritdoc}
     */
    public function update($id)
    {
        return parent::update($this->getNestedResourceId());
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($id)
    {
        return parent::destroy($this->getNestedResourceId());
    }

    /**
     * @return string|null
     */
    public function getNestedResourceId()
    {
        if ($this->nestedResourceId) {
            return $this->nestedResourceId;
        }

        return $this->nestedResourceId = request($this->getRouteParameterName());
    }

    /**
     * @param  string|int  $id
     * @return void
     */
    public function setNestedResourceId($id)
    {
        $this->nestedResourceId = $id;
    }

    /**
     * @return string
     */
    public function getRouteParameterName()
    {
        if ($this->routeParameterName) {
            return $this->routeParameterName;
        }

        return $this->routeParameterName = last(request()->route()->parameterNames());
    }

    /**
     * @param  string  $name
     * @return void
     */
    public function setRouteParameterName($name)
    {
        $this->routeParameterName = (string) $name;
    }
}
