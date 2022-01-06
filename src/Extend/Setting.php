<?php

namespace Dcat\Admin\Extend;

use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

abstract class Setting extends Form implements LazyRenderable
{
    use LazyWidget;

    /**
     * @var ServiceProvider
     */
    protected $extension;

    public function __construct(ServiceProvider $extension = null)
    {
        parent::__construct();

        $this->initExtension($extension);
    }

    protected function initExtension(?ServiceProvider $extension)
    {
        if ($extension) {
            $this->extension = $extension;

            $this->payload(['_extension_' => $extension->getName()]);
        }
    }

    /**
     * 处理请求.
     *
     * @param  array  $input
     * @return \Dcat\Admin\Http\JsonResponse
     */
    public function handle(array $input)
    {
        $this->extension()->config($this->formatInput($input));

        return $this->response()->success(trans('admin.save_succeeded'))->refresh();
    }

    /**
     * 格式化配置信息.
     *
     * @param  array  $input
     * @return array
     */
    protected function formatInput(array $input)
    {
        return $input;
    }

    /**
     * 表单字段定义.
     *
     * @return void
     */
    abstract public function form();

    /**
     * 弹窗标题.
     *
     * @return string
     */
    public function title()
    {
    }

    /**
     * 翻译.
     *
     * @param  string  $key
     * @param  array  $replace
     * @param  null  $locale
     * @return array|string|null
     */
    protected function trans($key, $replace = [], $locale = null)
    {
        return $this->extension()->trans($key, $replace, $locale);
    }

    /**
     * 填充表单数据.
     *
     * @return array
     */
    public function default()
    {
        return $this->extension()->config() ?: [];
    }

    /**
     * @return ServiceProvider
     */
    public function extension()
    {
        if (! empty($this->payload['_extension_'])) {
            return Admin::extension()->get($this->payload['_extension_']);
        }

        return $this->extension;
    }
}
