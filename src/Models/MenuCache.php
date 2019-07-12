<?php

namespace Dcat\Admin\Models;

use Illuminate\Support\Facades\Cache;

trait MenuCache
{
    protected $cacheKey = 'dcat-admin-menus';

    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     *
     * @param \Closure $builder
     * @return mixed
     */
    protected function remember(\Closure $builder)
    {
        if (!$this->enableCache()) {
            return $builder();
        }

        return $this->getStore()->remember($this->cacheKey, null, $builder);
    }

    /**
     * Delete all items from the cache.
     *
     * @return bool
     */
    public function destroyCache()
    {
        if (!$this->enableCache()) {
            return null;
        }

        return $this->getStore()->delete($this->cacheKey);
    }

    /**
     * @return bool
     */
    public function enableCache()
    {
        return config('admin.menu.cache.enable');
    }

    /**
     * Get cache store.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function getStore()
    {
        return Cache::store(config('admin.menu.cache.store', 'file'));
    }

}
