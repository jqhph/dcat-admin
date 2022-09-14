<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Illuminate\Support\Collection;

class Tab
{
    /**
     * @var Form|WidgetForm
     */
    protected $form;

    /**
     * @var Collection
     */
    protected $tabs;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $columnOffset = 0;

    /**
     * @var bool
     */
    public $hasRows = false;

    /**
     * Tab constructor.
     *
     * @param  Form|WidgetForm  $form
     */
    public function __construct($form)
    {
        $this->form = $form;

        $this->tabs = new Collection();
    }

    /**
     * Append a tab section.
     *
     * @param  string  $title
     * @param  \Closure  $content
     * @param  bool  $active
     * @param  string  $id
     * @return $this
     */
    public function append($title, \Closure $content, bool $active = false, ?string $id = null)
    {
        call_user_func($content, $this->form);

        $fields = $this->collectFields();
        $layout = $this->collectColumnLayout();

        $id = $id ?: ('tab-form-'.($this->tabs->count() + 1).'-'.mt_rand(0, 9999));

        $this->tabs->push(compact('id', 'title', 'fields', 'active', 'layout'));

        return $this;
    }

    /**
     * Collect fields under current tab.
     *
     * @return Collection
     */
    protected function collectFields()
    {
        $fields = clone $this->form->fields();

        $all = $fields->toArray();

        foreach ($this->form->rows() as $row) {
            $rowFields = $row->fields()->map(function ($field) {
                return $field['element'];
            });

            $match = false;

            foreach ($rowFields as $field) {
                if (($index = array_search($field, $all)) !== false) {
                    if (! $match) {
                        $fields->put($index, $row);
                    } else {
                        $fields->pull($index);
                    }

                    $match = true;
                }
            }

            $this->hasRows = true;
        }

        $fields = $fields->slice($this->offset);

        $this->offset += $fields->count();

        return $fields;
    }

    protected function collectColumnLayout()
    {
        $layout = clone $this->form->layout();

        $this->form->layout()->reset();

        return $layout;
    }

    /**
     * Set true for some one tab by title or id.
     *
     * @param  string  $value
     * @param  string  $field
     */
    public function active(string $value, string $field = 'title')
    {
        if ($this->tabs->where($field, $value)->isNotEmpty()) {
            $this->tabs = $this->tabs->map(function ($item) use ($value, $field) {
                $item['active'] = $item[$field] === $value;

                return $item;
            });
        }
    }

    /**
     * Set true for some one tab by key.
     *
     * @param  int  $index
     */
    public function activeByIndex(int $index = 0)
    {
        if ($this->tabs->offsetExists($index)) {
            $this->tabs = $this->tabs->map(function ($item, $itemKey) use ($index) {
                $item['active'] = $itemKey === $index;

                return $item;
            });
        }
    }

    /**
     * Get all tabs.
     *
     * @return Collection
     */
    public function getTabs()
    {
        // If there is no active tab, then active the first.
        if ($this->tabs->filter(function ($tab) {
            return $tab['active'];
        })->isEmpty()) {
            $first = $this->tabs->first();
            $first['active'] = true;

            $this->tabs->offsetSet(0, $first);
        }

        return $this->tabs;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->tabs->isEmpty();
    }

    /**
     * @return void
     */
    public function addScript()
    {
        $elementId = $this->form->getElementId();

        $script = <<<JS
(function () {
    var hash = document.location.hash;
    if (hash) {
        $('#$elementId .nav-tabs a[href="' + hash + '"]').tab('show');
    }

    // Change hash for page-reload
    $('#$elementId .nav-tabs a').on('shown.bs.tab', function (e) {
        history.pushState(null,null, e.target.hash);
    });

    if ($('#$elementId .has-error').length) {
        $('#$elementId .has-error').each(function () {
            var tabId = '#'+$(this).closest('.tab-pane').attr('id');
            $('li a[href="'+tabId+'"] i').removeClass('hide');
        });

        var first = $('#$elementId .has-error:first').closest('.tab-pane').attr('id');
        $('li a[href="#'+first+'"]').tab('show');
    }
})();
JS;
        Admin::script($script);
    }
}
