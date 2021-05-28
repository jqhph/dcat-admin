<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\EloquentSortable\Sortable;

class Permission extends Model implements Sortable
{
    use HasDateTimeFormatter,
        ModelTree {
            ModelTree::boot as treeBoot;
        }

    /**
     * @var array
     */
    protected $fillable = ['parent_id', 'name', 'slug', 'http_method', 'http_path'];

    /**
     * @var array
     */
    public static $httpMethods = [
        'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD',
    ];

    protected $titleColumn = 'name';

    /**
     * {@inheritDoc}
     */
    public function __construct(array $attributes = [])
    {
        $this->init();

        parent::__construct($attributes);
    }

    protected function init()
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.permissions_table'));
    }

    /**
     * Permission belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_permissions_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'role_id');
    }

    /**
     * @return BelongsToMany
     */
    public function menus(): BelongsToMany
    {
        $pivotTable = config('admin.database.permission_menu_table');

        $relatedModel = config('admin.database.menu_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'permission_id', 'menu_id')->withTimestamps();
    }

    /**
     * If request should pass through the current permission.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function shouldPassThrough(Request $request): bool
    {
        if (! $this->http_path) {
            return false;
        }

        $method = $this->http_method;

        $matches = array_map(function ($path) use ($method) {
            if (Str::contains($path, ':')) {
                [$method, $path] = explode(':', $path);
                $method = explode(',', $method);
            }

            $path = Str::contains($path, '.') ? $path : ltrim(admin_base_path($path), '/');

            return compact('method', 'path');
        }, $this->http_path);

        foreach ($matches as $match) {
            if ($this->matchRequest($match, $request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get options for Select field in form.
     *
     * @param \Closure|null $closure
     *
     * @return array
     */
    public static function selectOptions(\Closure $closure = null)
    {
        $options = (new static())->withQuery($closure)->buildSelectOptions();

        return collect($options)->all();
    }

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function getHttpPathAttribute($path)
    {
        return explode(',', $path);
    }

    /**
     * @param $path
     */
    public function setHttpPathAttribute($path)
    {
        if (is_array($path)) {
            $path = implode(',', $path);
        }

        return $this->attributes['http_path'] = $path;
    }

    /**
     * If a request match the specific HTTP method and path.
     *
     * @param array   $match
     * @param Request $request
     *
     * @return bool
     */
    protected function matchRequest(array $match, Request $request): bool
    {
        if (! $path = trim($match['path'], '/')) {
            return false;
        }

        if (! Helper::matchRequestPath($path, $request->decodedPath())) {
            return false;
        }

        $method = collect($match['method'])->filter()->map(function ($method) {
            return strtoupper($method);
        });

        return $method->isEmpty() || $method->contains($request->method());
    }

    /**
     * @param $method
     */
    public function setHttpMethodAttribute($method)
    {
        if (is_array($method)) {
            $this->attributes['http_method'] = implode(',', $method);
        }
    }

    /**
     * @param $method
     *
     * @return array
     */
    public function getHttpMethodAttribute($method)
    {
        if (is_string($method)) {
            return array_filter(explode(',', $method));
        }

        return $method;
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        static::treeBoot();

        parent::boot();

        static::deleting(function ($model) {
            $model->roles()->detach();
        });
    }
}
