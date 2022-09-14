<?php

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Actions\Extensions\InstallFromLocal;
use Dcat\Admin\Http\Actions\Extensions\Marketplace;
use Dcat\Admin\Http\Displayers\Extensions;
use Dcat\Admin\Http\Repositories\Extension;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\StringOutput;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class ExtensionController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->title(admin_trans_label('Extensions'))
            ->description(trans('admin.list'))
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return new Grid(new Extension(), function (Grid $grid) {
            $grid->number();
            $grid->column('name')->displayUsing(Extensions\Name::class);
            $grid->column('description')->displayUsing(Extensions\Description::class)->width('50%');

            $grid->column('authors')->display(function ($v) {
                if (! $v) {
                    return;
                }

                foreach ($v as &$item) {
                    $item = "<span class='text-80'>{$item['name']}</span> <<code>{$item['email']}</code>>";
                }

                return implode('<div style="margin-top: 5px"></div>', $v);
            });

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
            $grid->disableActions();

            $grid->tools([
                new Marketplace(),
                new InstallFromLocal(),
            ]);

            $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
                $create->text('name')
                    ->attribute('style', 'width:240px')
                    ->placeholder('Input Name. Eg: dcat-admin/demo')
                    ->required();
                $create->text('namespace')
                    ->attribute('style', 'width:240px')
                    ->placeholder('Input Namespace. Eg: DcatAdmin\\Demo');
                $create->select('type')
                    ->options([1 => trans('admin.application'), 2 => trans('admin.theme')])
                    ->attribute('style', 'width:140px!important')
                    ->default(1)
                    ->required();
            });
        });
    }

    public function form()
    {
        $form = new Form(new Extension());

        $form->hidden('name')->rules(function () {
            return [
                'required',
                function ($attribute, $value, $fail) {
                    if (! Helper::validateExtensionName($value)) {
                        return $fail(
                            "[$value] is not a valid package name, please type a name like \"vendor/name\""
                        );
                    }
                },
            ];
        });
        $form->hidden('namespace');
        $form->hidden('type');

        $self = $this;

        $form->saving(function (Form $form) use ($self) {
            $package = $form->name;
            $namespace = $form->namespace;
            $type = $form->type;

            if ($package) {
                $results = $self->createExtension($package, $namespace, $type);

                return $form
                    ->response()
                    ->refresh()
                    ->timeout(10)
                    ->success($results);
            }
        });

        return $form;
    }

    public function createExtension($package, $namespace, $type)
    {
        $namespace = trim($namespace, '\\');

        $output = new StringOutput();

        Artisan::call('admin:ext-make', [
            'name'        => $package,
            '--namespace' => $namespace ?: 'default',
            '--theme'     => $type == 2,
        ], $output);

        return sprintf('<pre class="bg-transparent text-white">%s</pre>', (string) $output->getContent());
    }
}
