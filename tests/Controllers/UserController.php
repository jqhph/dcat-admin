<?php

namespace Tests\Controllers;

use App\Http\Controllers\Controller;
use Dcat\Admin\Controllers\HasResourceActions;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Tests\Models\Tag;
use Tests\Repositories\User;

class UserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        $content->header('All users');
        $content->description('description');

        return $content->body($this->grid());
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit(Content $content, $id)
    {
        $content->header('Edit user');
        $content->description('description');

        $content->body($this->form()->edit($id));

        return $content;
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create(Content $content)
    {
        $content->header('Create user');

        return $content->body($this->form());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('User')
            ->description('Detail')
            ->body($this->detail($id));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->model()->with('tags');

        $grid->id('ID')->sortable();

        $grid->username();
        $grid->email();
        $grid->mobile();
        $grid->full_name();
        $grid->avatar()->display(function ($avatar) {
            return "<img src='{$avatar}' />";
        });
        $grid->column('profile.postcode', 'Post code');
//        $grid->profile()->address();
        $grid->column('profile.address');
//        $grid->position('Position');
        $grid->column('profile.color');
//        $grid->profile()->start_at('开始时间');
        $grid->column('profile.start_at', '开始时间');
//        $grid->profile()->end_at('结束时间');
        $grid->column('profile.end_at', '结束时间');

        $grid->column('column1_not_in_table')->display(function () {
            return 'full name:'.$this->full_name;
        });

        $grid->column('column2_not_in_table')->display(function () {
            return $this->email.'#'.$this->profile['color'];
        });

        $grid->tags()->display(function ($tags) {
            $tags = collect($tags)->map(function ($tag) {
                return "<code>{$tag['name']}</code>";
            })->toArray();

            return implode('', $tags);
        });

        $grid->created_at();
        $grid->updated_at();

        $grid->showExporter();

        $grid->filter(function (Grid\Filter $filter) {
            $filter->equal('id');
            $filter->like('username');
            $filter->like('email');
            $filter->like('profile.postcode');
            $filter->between('profile.start_at')->datetime();
            $filter->between('profile.end_at')->datetime();
        });

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->getKey() % 2 == 0) {
                $actions->append('<a href="/" class="btn btn-xs btn-danger">detail</a>');
            }
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(new User());

        $show->setId($id);

        $show->id('ID');
        $show->username();
        $show->email;

        $show->divider();

        $show->full_name();
        $show->field('profile.postcode');

        $show->tags->json();

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        Form::extend('map', Form\Field\Map::class);
        Form::extend('editor', Form\Field\Editor::class);

        $form = new Form(new User());

        $form->disableDeleteButton();

        $form->display('id', 'ID');
        $form->text('username');
        $form->email('email')->rules('required');
        $form->mobile('mobile');
        $form->image('avatar')->help('上传头像', 'fa-image');
        $form->ignore(['password_confirmation']);
        $form->password('password')->rules('confirmed');
        $form->password('password_confirmation');

        $form->divider();

        $form->text('profile.first_name');
        $form->text('profile.last_name');
        $form->text('profile.postcode')->help('Please input your postcode');
        $form->textarea('profile.address')->rows(15);
        $form->map('profile.latitude', 'profile.longitude', 'Position');
        $form->color('profile.color');
        $form->datetime('profile.start_at');
        $form->datetime('profile.end_at');

//        $tags = Tag::all()->pluck('name', 'id');
//        print_r($tags);die;

        $form->multipleSelect('tags', 'Tags')->options(Tag::all()->pluck('name', 'id'))->customFormat(function ($value) {
            if (! $value) {
                return [];
            }

            return array_column($value, 'id');
        }); //->rules('max:10|min:3');

        $form->display('created_at', 'Created At');
        $form->display('updated_at', 'Updated At');

        $form->html('<a html-field>html...</a>');

        return $form;
    }
}
