<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;

class Limit extends AbstractDisplayer
{
    protected function addScript()
    {
        $script = <<<'JS'
$('.limit-more').click(function () {
    $(this).parent('.limit-text').toggleClass('d-none').siblings().toggleClass('d-none');
});
JS;

        Admin::script($script);
    }

    public function display($limit = 100, $end = '...')
    {
        $this->value = Helper::htmlEntityEncode($this->value);

        // 数组
        if ($this->value !== null && ! is_scalar($this->value)) {
            $value = Helper::array($this->value);

            if (count($value) <= $limit) {
                return $value;
            }

            $value = array_slice($value, 0, $limit);

            array_push($value, $end);

            return $value;
        }

        // 字符串
        $this->addScript();

        $value = Helper::strLimit($this->value, $limit, $end);

        if ($value == $this->value) {
            return $value;
        }

        return <<<HTML
<div class="limit-text">
    <span class="text">{$value}</span>
    &nbsp;<a href="javascript:void(0);" class="limit-more">&nbsp;<i class="fa fa-angle-double-down"></i></a>
</div>
<div class="limit-text d-none">
    <span class="text">{$this->value}</span>
    &nbsp;<a href="javascript:void(0);" class="limit-more">&nbsp;<i class="fa fa-angle-double-up"></i></a>
</div>
HTML;
    }
}
