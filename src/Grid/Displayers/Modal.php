<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\LazyRenderable;
use Illuminate\Support\Str;

class Modal extends AbstractDisplayer
{
    protected $title;

    protected $renderable;

    public function title(string $title)
    {
        $this->title = $title;
    }

    protected function generateElementId()
    {
        $key = Str::random(8);

        return 'grid-modal-'.$this->grid->getName().$key;
    }

    protected function addRenderableScript(string $modalId, string $url)
    {
        $script = <<<JS
(function () {
    var modal = $('#{$modalId}');
    
    modal.on('show.bs.modal', function (e) {
        modal.find('.modal-body').html('<div style="min-height:150px"></div>');
    
        modal.find('.modal-body').loading();
        
        $.ajax('{$url}').then(function (data) {
            modal.find('.modal-body').html(data);
        });
    })
})();
JS;

        Admin::script($script);
    }

    protected function setUpLazyRenderable(string $modalId, LazyRenderable $renderable)
    {
        $renderable->with('key', $this->getKey());

        $this->addRenderableScript($modalId, $renderable->getUrl());

        $renderable::collectAssets();
    }

    public function display($callback = null)
    {
        $title = $this->value ?: $this->trans('title');
        if (func_num_args() == 2) {
            [$title, $callback] = func_get_args();
        }

        $html = $this->value;
        $id = $this->generateElementId();

        if ($callback instanceof \Closure) {
            $callback = $callback->call($this->row, $this);

            if (! $callback instanceof LazyRenderable) {
                $html = Helper::render($callback);

                $callback = null;
            }
        }

        if (is_string($callback) && is_subclass_of($callback, LazyRenderable::class)) {
            $html = '';

            $this->setUpLazyRenderable($id, $callback::make());
        } elseif ($callback instanceof LazyRenderable) {
            $html = '';

            $this->setUpLazyRenderable($id, $callback);
        }

        $title = $this->title ?: $title;

        return <<<EOT
<span class="grid-expand" data-toggle="modal" data-target="#{$id}">
   <a href="javascript:void(0)"><i class="fa fa-clone"></i>&nbsp;&nbsp;{$this->value}</a>
</span>

<div class="modal fade" id="{$id}" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{$title}</h4>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
