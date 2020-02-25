<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Str;

class Modal extends AbstractDisplayer
{
    protected $title;

    public function title(string $title)
    {
        $this->title = $title;
    }

    protected function generateElementId()
    {
        $key = $this->key() ?: Str::random(8);

        return 'grid-modal-'.$this->grid->getName().$key;
    }

    public function display($callback = null)
    {
        $title = $this->trans('title');
        if (func_num_args() == 2) {
            [$title, $callback] = func_get_args();
        }

        $html = $this->value;
        if ($callback instanceof \Closure) {
            $html = Helper::render(
                $callback->call($this->row, $this)
            );
        }

        $title = $this->title ?: $title;
        $id = $this->generateElementId();

        return <<<EOT
<span class="grid-expand" data-toggle="modal" data-target="#{$id}">
   <a href="javascript:void(0)"><i class="fa fa-clone"></i>&nbsp;&nbsp;{$this->value}</a>
</span>

<div class="modal fade" id="{$id}" tabindex="-1" role="dialog">
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
