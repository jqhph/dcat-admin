<?php

namespace Dcat\Admin\Grid\Filter\Presenter;

use Dcat\Admin\Admin;

class MultipleSelect extends Select
{
    /**
     * Load options for other select when change.
     *
     * @param string $target
     * @param string $resourceUrl
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function loadMore($target, $resourceUrl, $idField = 'id', $textField = 'text'): self
    {
        $class = $this->filter->formatColumnClass($target);

        $script = <<<JS
$(document).on('change', ".{$this->getElementClass()}", function () {
    var target = $(this).closest('form').find(".{$class}");
    var ids = $(this).find("option:selected").map(function(index,elem) {
            return $(elem).val();
        }).get().join(',');
     
    $.ajax("$resourceUrl?q="+ids).then(function (data) {
        target.find("option").remove();
        $.each(data, function (i, item) {
            $(target).append($('<option>', {
                value: item.$idField,
                text : item.$textField
            }));
        });
        
        $(target).trigger('change');
    });
});
JS;

        Admin::script($script);

        return $this;
    }
}
