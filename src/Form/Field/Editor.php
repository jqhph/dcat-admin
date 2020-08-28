<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\JavaScript;

/**
 * TinyMCE editor.
 *
 * @see https://www.tiny.cloud/docs
 * @see http://tinymce.ax-z.cn/
 */
class Editor extends Field
{
    protected static $js = [
        '@tinymce',
    ];

    protected $options = [
        'plugins' => [
            'advlist',
            'autolink',
            'link',
            'image',
            'media',
            'lists',
            'preview',
            'code',
            'help',
            'fullscreen',
            'table',
            'autoresize',
            'codesample',
        ],
        'toolbar' => [
            'undo redo | preview fullscreen | styleselect | fontsizeselect bold italic underline strikethrough forecolor backcolor | link image media blockquote removeformat codesample',
            'alignleft aligncenter alignright  alignjustify| indent outdent bullist numlist table subscript superscript | code',
        ],
        'min_height' => 400,
        'save_enablewhendirty' => true,
        'convert_urls' => false,
    ];

    protected $disk;

    protected $imageUploadDirectory = 'tinymce/images';

    /**
     * 设置文件上传存储配置.
     *
     * @param string $disk
     *
     * @return $this
     */
    public function disk(string $disk)
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * 设置图片上传文件夹.
     *
     * @param string $dir
     *
     * @return $this
     */
    public function imageDirectory(string $dir)
    {
        $this->imageUploadDirectory = $dir;

        return $this;
    }

    /**
     * 自定义图片上传接口.
     *
     * @param string $url
     *
     * @return $this
     */
    public function imageUrl(string $url)
    {
        return $this->mergeOptions(['images_upload_url' => $this->formatUrl(admin_url($url))]);
    }

    /**
     * 设置语言包url.
     *
     * @param string $url
     *
     * @return $this
     */
    public function languageUrl(string $url)
    {
        return $this->mergeOptions(['language_url' => $url]);
    }

    /**
     * 设置编辑器高度.
     *
     * @param int $height
     *
     * @return $this
     */
    public function height(int $height)
    {
        return $this->mergeOptions(['min_height' => $height]);
    }

    /**
     * @return string
     */
    protected function formatOptions()
    {
        $this->options['selector'] = '#'.$this->id;
        $this->options['language'] = config('app.locale');
        $this->options['readonly'] = ! empty($this->attributes['readonly']) || ! empty($this->attributes['disabled']);

        if (empty($this->options['images_upload_url'])) {
            $this->options['images_upload_url'] = $this->defaultImageUploadUrl();
        }

        // 内容更改后保存到隐藏表单
        $this->options['init_instance_callback'] = JavaScript::make($this->buildSaveContentScript());

        return JavaScript::format($this->options);
    }

    /**
     * @return string
     */
    protected function defaultImageUploadUrl()
    {
        return $this->formatUrl(route(admin_api_route('tinymce.upload')));
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected function formatUrl(string $url)
    {
        return Helper::urlWithQuery(
            $url,
            [
                '_token' => csrf_token(),
                'disk'   => $this->disk,
                'dir'    => $this->imageUploadDirectory,
            ]
        );
    }

    /**
     * @return string
     */
    protected function buildSaveContentScript()
    {
        return <<<JS
function (editor) {
    editor.on('Change', function(e) {
        var content = e.level.content;
        if (! content) {
            content = e.level.fragments;
            content = content.length && content.join('');
        }
        
      $(replaceNestedFormIndex('#{$this->id}')).val(String(content).replace('<p><br data-mce-bogus="1"></p>', '').replace('<p><br></p>', ''));
    });
}
JS;
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->script = <<<JS
(function () {
    var opts = {$this->formatOptions()};

    opts.selector = replaceNestedFormIndex(opts.selector);
    
    tinymce.init(opts)
})();
JS;

        return parent::render();
    }
}
