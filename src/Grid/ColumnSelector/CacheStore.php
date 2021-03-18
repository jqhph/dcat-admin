<?php

namespace Dcat\Admin\Grid\ColumnSelector;

class CacheStore extends SessionStore
{
    protected $driver;
    protected $ttl;

    public function __construct($driver = 'file', $ttl = 25920000)
    {
        $this->driver = cache()->driver($driver);
        $this->ttl = $ttl;
    }

    public function store(array $input)
    {
        $this->driver->put($this->getKey(), $input, $this->ttl);
    }

    public function get()
    {
        return $this->driver->get($this->getKey());
    }

    public function forget()
    {
        $this->driver->forget($this->getKey());
    }
}
