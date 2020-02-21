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
    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->title(trans('admin.operation_log'))
            ->description(trans('admin.list'))
            ->body($this->grid());
    }

    /**
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OperationLog());

        $grid->id('ID')->bold()->sortable();
        $grid->user(trans('admin.user'))
            ->get('name')
            ->link(function () {
                return admin_url('auth/users/'.$this->user['id']);
            })
            ->responsive();

        $grid->method(trans('admin.method'))->responsive()->display(function ($method) {
            $color = Arr::get(OperationLogModel::$methodColors, $method, 'default');

            return "<span class=\"label label-$color\">$method</span>";
        })->valueAsFilter();

        $grid->path(trans('admin.uri'))->responsive()->display(function ($v) {
            return "<code>$v</code>";
        })->valueAsFilter();

        $grid->ip('IP')->valueAsFilter()->responsive();

        $grid->input->responsive()->display(function ($input) {
            $input = json_decode($input, true);
            $input = Arr::except($input, ['_pjax', '_token', '_method', '_previous_']);
            if (empty($input)) {
                return '';
            }

            return '<pre class="dump">'.json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).'</pre>';
        });

        $grid->created_at(trans('admin.created_at'))->responsive();

        $grid->model()->with('user')->orderBy('id', 'DESC');

        $grid->disableCreateButton();
        $grid->disableQuickEditButton();
        $grid->disableEditButton();
        $grid->disableViewButton();

        $grid->filter(function (Grid\Filter $filter) {
            $filter->equal('user_id', trans('admin.user'))
                ->selectResource('auth/users')
                ->options(function ($v) {
                    if (! $v) {
                        return $v;
                    }
                    $userModel = config('admin.database.users_model');

                    return $userModel::findOrFail($v)->pluck('name', 'id');
                });

            $filter->equal('method', trans('admin.method'))
                ->select(
                    array_combine(OperationLogModel::$methods, OperationLogModel::$methods)
                );

            $filter->like('path', trans('admin.uri'));

            $filter->equal('ip', 'IP');

            $filter->between('created_at')->datetime();
        });

        return $grid;
    }

    /**
     * @param mixed $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
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
