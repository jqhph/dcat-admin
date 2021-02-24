<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\JavaScript;

/**
 * @see https://pandao.github.io/editor.md/
 */
class Markdown extends Field
{
    /**
     * 编辑器配置.
     *
     * @var array
     */
    protected $options = [
        'height'             => 500,
        'codeFold'           => true,
        'saveHTMLToTextarea' => true, // 保存 HTML 到 Textarea
        'searchReplace'      => true,
        'emoji'              => true,
        'taskList'           => true,
        'tocm'               => true,         // Using [TOCM]
        'tex'                => true,         // 开启科学公式TeX语言支持，默认关闭
        'flowChart'          => false,        // 流程图支持，默认关闭
        'sequenceDiagram'    => false,        // 时序/序列图支持，默认关闭,
        'imageUpload'        => true,
        'autoFocus'          => true,
    ];

    protected $language;

    protected $defaultLangs = [
        'en'    => '@admin/dcat/plugins/editor-md/languages/en.js',
        'zh_TW' => '@admin/dcat/plugins/editor-md/languages/zh-tw.js',
    ];

    protected $disk;

    protected $imageUploadDirectory = 'markdown/images';

    /**
     * 开启 HTML 标签解析.
     * style,script,iframe|on*.
     *
     * @param string $decode
     *
     * @return $this
     */
    public function htmlDecode($decode)
    {
        $this->options['htmlDecode'] = &$decode;

        return $this;
    }

    /**
     * 设置编辑器容器高度.
     *
     * @param int $height
     *
     * @return $this
     */
    public function height($height)
    {
        $this->options['height'] = $height;

        return $this;
    }

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
        return $this->mergeOptions(['imageUploadURL' => $this->formatUrl(admin_url($url))]);
    }

    /**
     * 设置语言包路径.
     *
     * @param string $url
     *
     * @return $this
     */
    public function languageUrl(string $url)
    {
        $this->language = $url;

        return $this;
    }

    /**
     * @return string
     */
    protected function defaultImageUploadUrl()
    {
        return $this->formatUrl(route(admin_api_route_name('editor-md.upload')));
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
    public function render()
    {
        $this->options['path'] = admin_asset('@admin/dcat/plugins/editor-md/lib').'/';
        $this->options['name'] = $this->column;
        $this->options['placeholder'] = $this->placeholder();
        $this->options['readonly'] = ! empty($this->attributes['readonly']) || ! empty($this->attributes['disabled']);

        if (empty($this->options['imageUploadURL'])) {
            $this->options['imageUploadURL'] = $this->defaultImageUploadUrl();
        }

        $this->requireLang();

        $this->addVariables(['options' => JavaScript::format($this->options)]);

        return parent::render();
    }

    protected function requireLang()
    {
        $locale = config('app.locale');

        if (isset($this->defaultLangs[$locale])) {
            Admin::js($this->defaultLangs[$locale]);

            return;
        }

        if ($this->language) {
            Admin::js($this->language);
        }
    }
}
