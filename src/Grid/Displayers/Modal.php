<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\RemoteRenderable;
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

    protected function addRenderableModalScript(string $modalId, string $url)
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

    protected function setUpRemoteRenderable(string $modalId, RemoteRenderable $renderable)
    {
        $renderable->setKey($this->getKey());

        $this->addRenderableModalScript($modalId, $renderable->getUrl());

        $renderable::collectAssets();
    }

    public function display($callback = null)
    {
        $title = $this->trans('title');
        if (func_num_args() == 2) {
            [$title, $callback] = func_get_args();
        }

        $title = $this->title ?: $title;
        $html = $this->value;
        $id = $this->generateElementId();

        if ($callback instanceof \Closure) {
            $html = Helper::render(
                $callback->call($this->row, $this)
            );
        } elseif (is_string($callback) && is_subclass_of($callback, RemoteRenderable::class)) {
            $html = '';

            $this->setUpRemoteRenderable($id, $callback::make());
        } elseif ($callback instanceof RemoteRenderable) {
            $html = '';

            $this->setUpRemoteRenderable($id, $callback);
        }

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
