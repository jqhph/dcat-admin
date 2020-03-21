<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Extension\Grid\ImportButton;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Models\Repositories\Extension;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\StringOutput;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Box;
use Dcat\Admin\Widgets\Table;
use Dcat\Admin\Widgets\Terminal;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class ExtensionController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        $this->define();

        return $content
            ->title(admin_trans_label('Extensions'))
            ->description(trans('admin.list'))
            ->body($this->grid());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function import()
    {
        $extension = request('id');

        if (! $extension) {
            return response()->json(['status' => false, 'messages' => 'Invalid extension hash.']);
        }

        $box = Alert::make()
            ->title("<span>php artisan admin:import $extension</span>")
            ->content(Terminal::call('admin:import', ['extension' => $extension, '--force' => '1'])->transparent())
            ->success()
            ->removable();

        return response()->json(['status' => true, 'content' => $box->render()]);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Extension());

        $grid->number();
        $grid->name;
        $grid->version;
        $grid->alias;

        $grid->description
            ->if(function () {
                return mb_strlen($this->description) > 14;
            })
            ->limit(14)
            ->expand(function ($expand) {
                if (! $this->description) {
                    return;
                }

                return "<div style='padding:10px 20px'>{$this->description}</div>";
            });

        $grid->authors;
        $grid->enable->switch();
        $grid->imported;

        $view = ucfirst(trans('admin.view'));
        $grid->config
            ->if(function () {
                return $this->config ? true : false;
            })
            ->display($view)
            ->expand($this->getExpandHandler('config'))
            ->else()
            ->emptyString();

        $grid->require
            ->if(function () {
                return $this->require ? true : false;
            })
            ->display($view)
            ->expand($this->getExpandHandler())
            ->else()
            ->emptyString();

        $grid->require_dev
            ->if(function () {
                return $this->require_dev ? true : false;
            })
            ->display($view)
            ->expand($this->getExpandHandler('require_dev'))
            ->else()
            ->emptyString();

        $grid->disablePagination();
        $grid->disableCreateButton();
        $grid->disableDeleteButton();
        $grid->disableBatchDelete();
        $grid->disableFilterButton();
        $grid->disableFilter();
        $grid->disableQuickEditButton();
        $grid->disableEditButton();
        $grid->disableDeleteButton();
        $grid->disableViewButton();

        $grid->actions(new ImportButton());

        $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
            $create->text('package_name')->required();
            $create->text('namespace')
                ->attribute('style', 'width:240px')
                ->required()
                ->default('Dcat\\Admin\\Extension\\:Name');
        });

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $form = new Form(new Extension());

        $form->text('package_name')->rules(function () {
            return [
                'required',
                function ($attribute, $value, $fail) {
                    if (! Helper::validateExtensionName($value)) {
                        return $fail(
                            "[$value] is not a valid package name, please input a name like \"vendor/name\""
                        );
                    }
                },
            ];
        });
        $form->text('namespace')->required();
        $form->hidden('enable');

        $form->saving(function (Form $form) {
            $package = $form->package_name;
            $namespace = $form->namespace;

            if ($package && $namespace) {
                $results = $this->createExtension($package, $namespace);

                return $form->success($results);
            }
        });

        return $form;
    }

    /**
     * 创建扩展
     *
     * @return string
     */
    public function createExtension($package, $namespace)
    {
        $namespace = trim($namespace, '\\');

        $output = new StringOutput();

        Artisan::call('admin:extend', [
            'extension'   => $package,
            '--namespace' => $namespace,
        ], $output);

        return $output->getContent();
    }

    /**
     * @param string $key
     *
     * @return \Closure
     */
    protected function getExpandHandler($key = 'require')
    {
        return function () use ($key) {
            if (! $this->{$key}) {
                return;
            }

            $rows = [];
            foreach ((array) $this->{$key} as $k => $v) {
                $k = "<b class='text-80'>$k</b>";

                $rows[$k] = is_array($v) ? "<pre>{$v}</pre>" : $v;
            }

            return new Table($rows);
        };
    }

    /**
     * 字段显示定义.
     */
    protected function define()
    {
        $name = function ($v) {
            $url = $this->homepage;

            return "<a href='$url' target='_blank'>$v</a>";
        };

        $version = function ($v) {
            $this->version = $this->version ?: 'unknown';
            $style = in_array($this->version, ['dev-master', 'unknown']) ? 'default' : 'primary';

            return $this->version ? "<span class='label bg-$style'>{$this->version}</span>" : '';
        };

        $authors = function ($v) {
            if (! $v) {
                return;
            }

            foreach ($v as &$item) {
                $item = "<span class='text-80'>{$item['name']}</span> <<code>{$item['email']}</code>>";
            }

            return implode('<br/>', $v);
        };

        $imported = function ($v) {
            if (! $v) {
                $text = trans('admin.is_not_import');

                return "<label class='label label-default'>$text</label>";
            }

            return $this->imported_at;
        };

        Grid\Column::define('name', $name);
        Grid\Column::define('version', $version);
        Grid\Column::define('authors', $authors);
        Grid\Column::define('imported', $imported);
    }
}
