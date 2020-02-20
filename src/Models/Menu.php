<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * Class Menu.
 *
 * @property int $id
 *
 * @method where($parent_id, $id)
 */
class Menu extends Model
{
    use MenuCache,
        ModelTree {
            ModelTree::boot as treeBoot;
        }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'order', 'title', 'icon', 'uri', 'permission_id'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.menu_table'));

        parent::__construct($attributes);
    }

    /**
     * A Menu belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_menu_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'menu_id', 'role_id');
    }

    public function permissions(): BelongsToMany
    {
        $pivotTable = config('admin.database.permission_menu_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'menu_id', 'permission_id');
    }

    /**
     * @param bool $force
     *
     * @return array
     */
    public function allNodes(bool $force = false): array
    {
        if ($force || $this->queryCallback instanceof \Closure) {
            return $this->fetchAll();
        }

        return $this->remember(function () {
            return $this->fetchAll();
        });
    }

    /**
     * @return array
     */
    protected function fetchAll(): array
    {
        $connection = config('admin.database.connection') ?: config('database.default');
        $orderColumn = DB::connection($connection)->getQueryGrammar()->wrap($this->getOrderColumn());

        $byOrder = 'ROOT ASC, '.$orderColumn;

        $self = new static();
        if ($this->queryCallback instanceof \Closure) {
            $self = call_user_func($this->queryCallback, $self);
        }

        if (static::withPermission()) {
            $self = $self->with('permissions');
        }

        return $self->with('roles')
            ->selectRaw('*, '.$orderColumn.' ROOT')
            ->orderByRaw($byOrder)
            ->get()
            ->toArray();
    }

    /**
     * determine if enable menu bind permission.
     *
     * @return bool
     */
    public static function withPermission()
    {
        return (bool) config('admin.menu.bind_permission');
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        static::treeBoot();

        static::deleting(function ($model) {
            $model->roles()->detach();
            $model->permissions()->detach();

            $model->flushCache();
        });

        static::saved(function ($model) {
            $model->flushCache();
        });
    }
}
