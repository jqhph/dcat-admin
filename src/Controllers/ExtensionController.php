<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Extension\Grid\CreateExtensionButton;
use Dcat\Admin\Extension\Grid\ImportButton;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Models\Repositories\Extension;
use Dcat\Admin\Widgets\Box;
use Dcat\Admin\Widgets\Table;
use Dcat\Admin\Widgets\Terminal;
use Illuminate\Routing\Controller;

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

        $box = Box::make("<span>admin:import <small>$extension</small></span>")
            ->content(Terminal::call('admin:import', ['extension' => $extension, '--force' => '1']))
            ->style('default')
            ->collapsable()
            ->removable();

        return response()->json(['status' => true, 'content' => $box->render()]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        $name = request('name');
        $namespace = trim(request('namespace'), '\\');
        $contents = "<span>admin:extend <small>$name --namespace=$namespace</small></span>";
        $terminal = Terminal::call('admin:extend', [
            'extension' => $name,
            '--namespace' => $namespace,
        ]);

        $box = Box::make($contents)
            ->content($terminal)
            ->style('default')
            ->collapsable()
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
            ->asEmpty();

        $grid->require
            ->display($view)
            ->expand($this->getExpandHandler());

        $grid->require_dev
            ->display($view)
            ->expand($this->getExpandHandler('require_dev'));

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
        $grid->tools(new CreateExtensionButton());

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

        $form->hidden('enable');

        return $form;
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

                $rows[$k] = $v;
            }

            $table = new Table([], $rows);

            return $table;
        };
    }

    /**
     * Make definitions.
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

            return $this->version ? "<span class='label label-$style'>{$this->version}</span>" : '';
        };

        $authors = function ($v) {
            if (! $v) {
                return;
            }

            foreach ($v as &$item) {
                $item = "<span class='text-80 bold'>{$item['name']}</span> <<code>{$item['email']}</code>>";
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
