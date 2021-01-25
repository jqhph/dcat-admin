<?php

namespace Dcat\Admin\Traits;

trait LazyWidget
{
    protected $payload = [];

    public function payload(array $payload)
    {
        $this->payload = array_merge($this->payload, $payload);

        return $this;
    }

    public function getUrl()
    {
        $data = array_merge($this->payload, [
            'renderable' => $this->getRenderableName(),
        ]);

        return route(admin_api_route_name('render'), $data);
    }

    protected function getRenderableName()
    {
        return str_replace('\\', '_', static::class);
    }
}
