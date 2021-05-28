<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Admin;

/**
 * Trait LazyWidget.
 *
 *
 * @property string $translation
 */
trait LazyWidget
{
    protected $payload = [];

    public function payload(array $payload)
    {
        $this->payload = array_merge($this->payload, $payload);

        return $this;
    }

    public function translation()
    {
        return empty($this->translation) ? Admin::translator()->getPath() : $this->translation;
    }

    public function getUrl()
    {
        $data = array_merge($this->payload, [
            'renderable' => $this->getRenderableName(),
            '_trans_'    => $this->translation(),
        ]);

        return route(admin_api_route_name('render'), $data);
    }

    protected function getRenderableName()
    {
        return str_replace('\\', '_', static::class);
    }
}
