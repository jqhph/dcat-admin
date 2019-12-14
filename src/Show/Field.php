<?php

namespace Dcat\Admin\Show;

use Dcat\Admin\Show;
use Dcat\Admin\Traits\HasBuilderEvents;
use Dcat\Admin\Traits\HasDefinitions;
use Dcat\Admin\Widgets\Dump;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class Field implements Renderable
{
    use HasBuilderEvents,
        HasDefinitions,
        Macroable {
            __call as macroCall;
        }

    /**
     * @var array
     */
    protected static $extendedFields = [];

    /**
     * @var string
     */
    protected $view = 'admin::show.field';

    /**
     * Name of column.
     *
     * @var string
     */
    protected $name;

    /**
     * Label of column.
     *
     * @var string
     */
    protected $label;

    /**
     * Escape field value or not.
     *
     * @var bool
     */
    protected $escape = true;

    /**
     * Field value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * @var Collection
     */
    protected $showAs = [];

    /**
     * Parent show instance.
     *
     * @var Show
     */
    protected $parent;

    /**
     * Relation name.
     *
     * @var string
     */
    protected $relation;

    /**
     * If show contents in box.
     *
     * @var bool
     */
    protected $border = false;

    /**
     * @var int
     */
    protected $width = 3;

    /**
     * @var array
     */
    protected $fileTypes = [
        'image'      => 'png|jpg|jpeg|tmp|gif',
        'word'       => 'doc|docx',
        'excel'      => 'xls|xlsx|csv',
        'powerpoint' => 'ppt|pptx',
        'pdf'        => 'pdf',
        'code'       => 'php|js|java|python|ruby|go|c|cpp|sql|m|h|json|html|aspx',
        'archive'    => 'zip|tar\.gz|rar|rpm',
        'txt'        => 'txt|pac|log|md',
        'audio'      => 'mp3|wav|flac|3pg|aa|aac|ape|au|m4a|mpc|ogg',
        'video'      => 'mkv|rmvb|flv|mp4|avi|wmv|rm|asf|mpeg',
    ];

    /**
     * Field constructor.
     *
     * @param string $name
     * @param string $label
     */
    public function __construct($name = '', $label = '')
    {
        $this->name = $name;
        $this->label = $this->formatLabel($label);
        $this->showAs = new Collection();

        $this->callResolving();
    }

    /**
     * Set parent show instance.
     *
     * @param Show $show
     *
     * @return $this
     */
    public function setParent(Show $show)
    {
        $this->parent = $show;

        return $this;
    }

    /**
     * Get name of this column.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $width
     *
     * @return $this|int
     */
    public function width(int $width = null)
    {
        if ($width === null) {
            return $this->width;
        }

        $this->width = $width;

        return $this;
    }

    /**
     * Format label.
     *
     * @param $label
     *
     * @return mixed
     */
    protected function formatLabel($label)
    {
        $label = $label ?: ucfirst(admin_trans_field($this->name));

        return str_replace(['.', '_'], ' ', $label);
    }

    /**
     * Get label of the column.
     *
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Field display callback.
     *
     * @param mixed $callable
     *
     * @return $this
     */
    public function as($callable, ...$params)
    {
        $this->showAs->push([$callable, $params]);

        return $this;
    }

    /**
     * Display field using array value map.
     *
     * @param array $values
     * @param null  $default
     *
     * @return $this
     */
    public function using(array $values, $default = null)
    {
        return $this->as(function ($value) use ($values, $default) {
            if (is_null($value)) {
                return $default;
            }

            return Arr::get($values, $value, $default);
        });
    }

    /**
     * Show field as a image.
     *
     * @param string $server
     * @param int    $width
     * @param int    $height
     *
     * @return $this
     */
    public function image($server = '', $width = 200, $height = 200)
    {
        return $this->unescape()->as(function ($path) use ($server, $width, $height) {
            if (empty($path)) {
                return '';
            }

            if (url()->isValidUrl($path)) {
                $src = $path;
            } elseif ($server) {
                $src = $server.$path;
            } else {
                $disk = config('admin.upload.disk');

                if (config("filesystems.disks.{$disk}")) {
                    $src = Storage::disk($disk)->url($path);
                } else {
                    return '';
                }
            }

            return "<img data-init='preview' src='$src' style='max-width:{$width}px;max-height:{$height}px' class='img' />";
        });
    }

    /**
     * Show field as a file.
     *
     * @param string $server
     * @param bool   $download
     *
     * @return Field
     */
    public function file($server = '', $download = true)
    {
        $field = $this;

        return $this->unescape()->as(function ($path) use ($server, $download, $field) {
            $name = basename($path);

            $field->wrap(false);

            $size = $url = '';

            if (url()->isValidUrl($path)) {
                $url = $path;
            } elseif ($server) {
                $url = $server.$path;
            } else {
                $storage = Storage::disk(config('admin.upload.disk'));
                if ($storage->exists($path)) {
                    $url = $storage->url($path);
                    $size = ($storage->size($path) / 1000).'KB';
                }
            }

            if (! $url) {
                return '';
            }

            return <<<HTML
<ul class="mailbox-attachments clearfix">
    <li style="margin-bottom: 0;">
      <span class="mailbox-attachment-icon"><i class="fa {$field->getFileIcon($name)}"></i></span>
      <div class="mailbox-attachment-info">
        <div class="mailbox-attachment-name">
            <i class="fa fa-paperclip"></i> {$name}
            </div>
            <span class="mailbox-attachment-size">
              {$size}&nbsp;
              <a href="{$url}" class="btn btn-default btn-xs pull-right" target="_blank"><i class="fa fa-cloud-download"></i></a>
            </span>
      </div>
    </li>
  </ul>
HTML;
        });
    }

    /**
     * Show field as a link.
     *
     * @param string $href
     * @param string $target
     *
     * @return Field
     */
    public function link($href = '', $target = '_blank')
    {
        return $this->unescape()->as(function ($link) use ($href, $target) {
            $href = $href ?: $link;

            return "<a href='$href' target='{$target}'>{$link}</a>";
        });
    }

    /**
     * Show field as labels.
     *
     * @param string $style
     *
     * @return Field
     */
    public function label($style = 'success')
    {
        return $this->unescape()->as(function ($value) use ($style) {
            if ($value instanceof Arrayable) {
                $value = $value->toArray();
            }

            return collect((array) $value)->map(function ($name) use ($style) {
                return "<span class='label label-{$style}'>$name</span>";
            })->implode('&nbsp;');
        });
    }

    /**
     * Show field as badges.
     *
     * @param string $style
     *
     * @return Field
     */
    public function badge($style = 'blue')
    {
        return $this->unescape()->as(function ($value) use ($style) {
            if ($value instanceof Arrayable) {
                $value = $value->toArray();
            }

            return collect((array) $value)->map(function ($name) use ($style) {
                return "<span class='badge bg-{$style}'>$name</span>";
            })->implode('&nbsp;');
        });
    }

    /**
     * Show field as json code.
     *
     * @return Field
     */
    public function json()
    {
        $field = $this;

        return $this->unescape()->as(function ($value) use ($field) {
            $content = is_string($value) ? json_decode($value, true) : $value;

            $field->wrap(false);

            return Dump::make($content);
        });
    }

    /**
     * @param string $val
     *
     * @return $this
     */
    public function prepend($val)
    {
        return $this->as(function ($v) use (&$val) {
            if (is_array($v)) {
                array_unshift($v, $val);

                return $v;
            } elseif ($v instanceof Collection) {
                return $v->prepend($val);
            }

            return $val.$v;
        });
    }

    /**
     * @param string $val
     *
     * @return $this
     */
    public function append($val)
    {
        return $this->as(function ($v) use (&$val) {
            if (is_array($v)) {
                array_push($v, $val);

                return $v;
            } elseif ($v instanceof Collection) {
                return $v->push($val);
            }

            return $v.$val;
        });
    }

    /**
     * Split a string by string.
     *
     * @param string $d
     *
     * @return $this
     */
    public function explode(string $d = ',')
    {
        return $this->as(function ($v) use ($d) {
            if (is_array($v) || $v instanceof Arrayable) {
                return $v;
            }

            return $v ? explode($d, $v) : [];
        });
    }

    /**
     * Render this column with the given view.
     *
     * @param string $view
     *
     * @return $this
     */
    public function view($view)
    {
        $name = $this->name;

        return $this->as(function ($value) use ($view, $name) {
            $model = $this;

            return view($view, compact('model', 'value', 'name'))->render();
        });
    }

    /**
     * Get file icon.
     *
     * @param string $file
     *
     * @return string
     */
    public function getFileIcon($file = '')
    {
        $extension = File::extension($file);

        foreach ($this->fileTypes as $type => $regex) {
            if (preg_match("/^($regex)$/i", $extension) !== 0) {
                return "fa-file-{$type}-o";
            }
        }

        return 'fa-file-o';
    }

    /**
     * Set escape or not for this field.
     *
     * @param bool $escape
     *
     * @return $this
     */
    public function escape($escape = true)
    {
        $this->escape = $escape;

        return $this;
    }

    /**
     * Unescape for this field.
     *
     * @return Field
     */
    public function unescape()
    {
        return $this->escape(false);
    }

    /**
     * @param Fluent $model
     *
     * @return void
     */
    public function fill(Fluent $model)
    {
        $this->value($model->get($this->name));
    }

    /**
     * Get or set value for this field.
     *
     * @param mixed $value
     *
     * @return $this|mixed
     */
    public function value($value = null)
    {
        if ($value === null) {
            return $this->value;
        }

        $this->value = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function wrap(bool $wrap = true)
    {
        $this->border = $wrap;

        return $this;
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call($method, $arguments = [])
    {
        if ($class = Arr::get(static::$extendedFields, $method)) {
            return $this->callExtendedField($class, $arguments);
        }

        if (static::hasMacro($method)) {
            return $this->macroCall($method, $arguments);
        }

        return $this->callSupportDisplayer($method, $arguments);
    }

    /**
     * Call extended field.
     *
     * @param string|AbstractField|\Closure $abstract
     * @param array                         $arguments
     *
     * @return Field
     */
    protected function callExtendedField($abstract, $arguments = [])
    {
        if ($abstract instanceof \Closure) {
            return $this->as($abstract, ...$arguments);
        }

        if (is_string($abstract) && class_exists($abstract)) {
            /** @var AbstractField $extend */
            $extend = new $abstract();
        }

        if ($abstract instanceof AbstractField) {
            /** @var AbstractField $extend */
            $extend = $abstract;
        }

        if (! isset($extend)) {
            admin_warning("[$abstract] is not a valid Show field.");

            return $this;
        }

        if (! $extend->escape) {
            $this->unescape();
        }

        $field = $this;

        return $this->as(function ($value) use ($extend, $field, $arguments) {
            if (! $extend->border) {
                $field->wrap(false);
            }

            $extend->setValue($value)->setModel($this);

            return $extend->render(...$arguments);
        });
    }

    /**
     * Call Illuminate/Support.
     *
     * @param string $abstract
     * @param array  $arguments
     *
     * @return $this
     */
    protected function callSupportDisplayer($abstract, $arguments)
    {
        return $this->as(function ($value) use ($abstract, $arguments) {
            if (is_array($value) || $value instanceof Arrayable) {
                return call_user_func_array([collect($value), $abstract], $arguments);
            }

            if (is_string($value)) {
                return call_user_func_array([Str::class, $abstract], array_merge([$value], $arguments));
            }

            return $value;
        });
    }

    /**
     * Get all variables passed to field view.
     *
     * @return array
     */
    protected function variables()
    {
        return [
            'content' => $this->value,
            'escape'  => $this->escape,
            'label'   => $this->getLabel(),
            'wrapped' => $this->border,
            'width'   => $this->width,
        ];
    }

    /**
     * Render this field.
     *
     * @return string
     */
    public function render()
    {
        if (static::hasDefinition($this->name)) {
            $this->useDefinedColumn();
        }

        if ($this->showAs->isNotEmpty()) {
            $this->showAs->each(function ($callable) {
                [$callable, $params] = $callable;

                if (! $callable instanceof \Closure) {
                    $this->value = $callable;

                    return;
                }

                $this->value = $callable->call(
                    $this->parent->model(),
                    $this->value,
                    ...$params
                );
            });
        }

        return view($this->view, $this->variables());
    }

    /**
     * Use a defined column.
     *
     * @throws \Exception
     */
    protected function useDefinedColumn()
    {
        $class = static::$definitions[$this->name];

        if (! $class instanceof \Closure) {
            throw new \Exception("Invalid column definition [$class]");
        }

        $this->as($class);
    }

    /**
     * Register custom field.
     *
     * @param string $abstract
     * @param string $class
     *
     * @return void
     */
    public static function extend($abstract, $class)
    {
        static::$extendedFields[$abstract] = $class;
    }

    /**
     * @return array
     */
    public static function extensions()
    {
        return static::$extendedFields;
    }
}
