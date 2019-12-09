<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Support\Helper;

class Modal extends AbstractDisplayer
{
    public function display($callback = null)
    {
        $title = $this->trans('title');
        if (func_num_args() == 2) {
            [$title, $callback] = func_get_args();
        }

        $html = $this->value;
        if ($callback instanceof \Closure) {
            $callback = $callback->bindTo($this->row);

            $html = Helper::render($callback($this));
        }

        $key = $this->grid->getName().$this->key();

        return <<<EOT
<span class="grid-expand" data-toggle="modal" data-target="#grid-modal-{$key}">
   <a href="javascript:void(0)"><i class="fa fa-clone"></i>&nbsp;&nbsp;{$this->value}</a>
</span>

<div class="modal fade" id="grid-modal-{$key}" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{$title}</h4>
      </div>
      <div class="modal-body">
        {$html}
      </div>
    </div>
  </div>
</div>

EOT;
    }
}
