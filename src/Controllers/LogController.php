<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Models\OperationLog as OperationLogModel;
use Dcat\Admin\Models\Repositories\OperationLog;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

class LogController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title(trans('admin.operation_log'))
            ->description(trans('admin.list'))
            ->body($this->grid());
    }

    protected function grid()
    {
        return new Grid(new OperationLog(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user', trans('admin.user'))
                ->get('name')
                ->link(function () {
                    if ($this->user) {
                        return admin_url('auth/users/'.$this->user['id']);
                    }
                })
                ->responsive();

            $grid->column('method', trans('admin.method'))
                ->responsive()
                ->label(OperationLogModel::$methodColors)
                ->filterByValue();

            $grid->column('path', trans('admin.uri'))->responsive()->display(function ($v) {
                return "<code>$v</code>";
            })->filterByValue();

            $grid->column('ip', 'IP')->filterByValue()->responsive();

            $grid->column('input')->responsive()->display(function ($input) {
                $input = json_decode($input, true);

                if (empty($input)) {
                    return;
                }

                $input = Arr::except($input, ['_pjax', '_token', '_method', '_previous_']);

                if (empty($input)) {
                    return;
                }

                return '<pre class="dump">'.json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).'</pre>';
            });

            $grid->column('created_at', trans('admin.created_at'))->responsive();

            $grid->model()->with('user')->orderBy('id', 'DESC');

            $grid->disableCreateButton();
            $grid->disableQuickEditButton();
            $grid->disableEditButton();
            $grid->disableViewButton();
            $grid->setActionClass(Grid\Displayers\Actions::class);

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id', trans('admin.user'))
                    ->selectResource('auth/users')
                    ->options(function ($v) {
                        if (! $v) {
                            return $v;
                        }
                        $userModel = config('admin.database.users_model');

                        return $userModel::find((array) $v)->pluck('name', 'id');
                    });

                $filter->equal('method', trans('admin.method'))
                    ->select(
                        array_combine(OperationLogModel::$methods, OperationLogModel::$methods)
                    );

                $filter->like('path', trans('admin.uri'));
                $filter->equal('ip', 'IP');
                $filter->between('created_at')->datetime();
            });
        });
    }

    public function destroy($id)
    {
        $ids = explode(',', $id);

        if (OperationLogModel::destroy(array_filter($ids))) {
            $data = [
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ];
        } else {
            $data = [
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ];
        }

        return response()->json($data);
    }
}
