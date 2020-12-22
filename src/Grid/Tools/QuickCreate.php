<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\MultipleSelect;
use Dcat\Admin\Form\Field\Select;
use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Grid;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;

class QuickCreate implements Renderable
{
    /**
     * @var Grid
     */
    protected $parent;

    /**
     * @var Collection
     */
    protected $fields;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $method = 'POST';

    /**
     * QuickCreate constructor.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->parent = $grid;
        $this->fields = Collection::make();
        $this->action = request()->url();
    }

    protected function formatPlaceholder($placeholder)
    {
        return array_filter((array) $placeholder);
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return Text
     */
    public function text($column, $placeholder = '')
    {
        $field = new Text($column, $this->formatPlaceholder($placeholder));

        $this->addField($field->attribute('style', 'width:180px'));

        return $field;
    }

    /**
     * @param string $column
     *
     * @return Text
     */
    public function hidden($column)
    {
        return $this->text($column)
            ->attribute('hidden', 'hidden');
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return Text
     */
    public function email($column, $placeholder = '')
    {
        return $this->text($column, $placeholder)
            ->inputmask(['alias' => 'email']);
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return Text
     */
    public function ip($column, $placeholder = '')
    {
        return $this->text($column, $placeholder)
            ->inputmask(['alias' => 'ip'])
            ->attribute('style', 'width:120px');
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return Text
     */
    public function url($column, $placeholder = '')
    {
        return $this->text($column, $placeholder)
            ->inputmask(['alias' => 'url']);
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return Text
     */
    public function password($column, $placeholder = '')
    {
        return $this->text($column, $placeholder)
            ->attribute('type', 'password')
            ->attribute('style', 'width:120px');
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return Text
     */
    public function mobile($column, $placeholder = '')
    {
        return $this->text($column, $placeholder)
            ->inputmask(['mask' => '99999999999'])
            ->attribute('style', 'width:120px');
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return Text
     */
    public function integer($column, $placeholder = '')
    {
        return $this->text($column, $placeholder)
            ->inputmask(['alias' => 'integer'])
            ->attribute('style', 'width:150px');
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return Field\SelectResource
     */
    public function selectResource($column, $placeholder = '')
    {
        $field = new Field\SelectResource($column, $this->formatPlaceholder($placeholder));

        $this->addField($field->attribute('style', 'width:150px'));

        return $field;
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return Select
     */
    public function select($column, $placeholder = '')
    {
        $field = new Select($column, $this->formatPlaceholder($placeholder));

        $this->addField($field);

        return $field;
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return Field\Tags
     */
    public function tags($column, $placeholder = '')
    {
        $field = new Field\Tags($column, $this->formatPlaceholder($placeholder));

        $this->addField($field);

        return $field;
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return MultipleSelect
     */
    public function multipleSelect($column, $placeholder = '')
    {
        $field = new MultipleSelect($column, $this->formatPlaceholder($placeholder));

        $this->addField($field);

        return $field;
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return Field\Date
     */
    public function datetime($column, $placeholder = '')
    {
        return $this->date($column, $placeholder)->format('YYYY-MM-DD HH:mm:ss');
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return Field\Date
     */
    public function time($column, $placeholder = '')
    {
        return $this->date($column, $placeholder)->format('HH:mm:ss');
    }

    /**
     * @param string $column
     * @param string $placeholder
     *
     * @return Field\Date
     */
    public function date($column, $placeholder = '')
    {
        $field = new Field\Date($column, $this->formatPlaceholder($placeholder));

        $this->addField($field);

        return $field;
    }

    /**
     * @param Field $field
     *
     * @return Field
     */
    protected function addField(Field $field)
    {
        $elementClass = array_merge([$this->getElementClass()], $field->getElementClass());

        $field->addElementClass($elementClass);

        $field->setView($this->resolveView(get_class($field)));

        $field::collectAssets();

        $this->fields->push($field);

        return $field;
    }

    /**
     * @param string $class
     *
     * @return string
     */
    protected function resolveView($class)
    {
        $path = explode('\\', $class);

        $name = strtolower(array_pop($path));

        return "admin::grid.quick-create.{$name}";
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function action(?string $action)
    {
        $this->action = admin_url($action);

        return $this;
    }

    /**
     * @param string $method
     *
     * @return $this
     */
    public function method(?string $method = 'POST')
    {
        $this->method = $method;

        return $this;
    }

    protected function script()
    {
        $url = $this->action;
        $method = $this->method;

        $uniqueName = $this->parent->getName();

        $script = <<<JS
(function () {
    var ctr = $('.{$this->getElementClass()}'),
        btn = $('.quick-create-button-{$uniqueName}');
    
    btn.on('click', function () {
        ctr.toggle().click();
    });
    
    ctr.on('click', function () {
        ctr.find('.create-form').show();
        ctr.find('.create').hide();
    });
    
    ctr.find('.cancel').on('click', function () {
        if (btn.length) {
            ctr.hide();
            return;
        }
        
        ctr.find('.create-form').hide();
        ctr.find('.create').show();
        return false;
    });

    ctr.find('.create-form').submit(function (e) {
        e.preventDefault();
        
        if (ctr.attr('submitting')) {
            return;
        }
        
        var btn = $(this).find(':submit').buttonLoading();
        
        ctr.attr('submitting', 1);
    
        $.ajax({
            url: '{$url}',
            type: '{$method}',
            data: $(this).serialize(),
            success: function(data) {
                ctr.attr('submitting', '');
                btn.buttonLoading(false);
                console.info(data);
                
                if (data.status == true) {
                    Dcat.success(data.message);
                    Dcat.reload();
                    return;
                }
                
                if (typeof data.validation !== 'undefined') {
                    Dcat.warning(data.message)
                }
            },
            error:function(xhq){
                btn.buttonLoading(false);
                ctr.attr('submitting', '');
                var json = xhq.responseJSON;
                if (typeof json === 'object') {
                    if (json.message) {
                        Dcat.error(json.message);
                    } else if (json.errors) {
                        var i, errors = [];
                        for (i in json.errors) {
                            errors.push(json.errors[i].join("<br>"));
                        } 
                        
                        Dcat.error(errors.join("<br>"));
                    }
                }
            }
        });
        
        return false;
    });
})();
JS;

        Admin::script($script);
    }

    public function getElementClass()
    {
        $name = $this->parent->getName();

        return 'quick-create'.($name ? "-{$name}" : '');
    }

    /**
     * @param int $columnCount
     *
     * @return array|string
     */
    public function render($columnCount = 0)
    {
        if ($this->fields->isEmpty()) {
            return '';
        }

        $this->script();

        $vars = [
            'columnCount'  => $columnCount,
            'fields'       => $this->fields,
            'elementClass' => $this->getElementClass(),
        ];

        return view('admin::grid.quick-create.form', $vars)->render();
    }
}
