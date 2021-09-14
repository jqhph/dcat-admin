<?php

namespace Dcat\Admin\Form\Concerns;

use Closure;
use Dcat\Admin\Contracts\UploadField as UploadFieldInterface;
use Dcat\Admin\Form\Events;
use Dcat\Admin\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

trait HasEvents
{
    public $eventResponse;

    /**
     * 监听创建页面访问事件.
     *
     * @param  Closure  $callback
     * @return $this
     */
    public function creating(Closure $callback)
    {
        Event::listen(Events\Creating::class, $this->makeListener($callback));

        return $this;
    }

    /**
     * 监听编辑页面访问时间.
     *
     * @param  Closure  $callback
     * @return $this
     */
    public function editing(Closure $callback)
    {
        Event::listen(Events\Editing::class, $this->makeListener($callback));

        return $this;
    }

    /**
     * 监听提交事件.
     *
     * @param  Closure  $callback
     * @return $this
     */
    public function submitted(Closure $callback)
    {
        Event::listen(Events\Submitted::class, $this->makeListener($callback));

        return $this;
    }

    /**
     * 保存.
     *
     * @param  Closure  $callback
     * @return $this
     */
    public function saving(Closure $callback)
    {
        Event::listen(Events\Saving::class, $this->makeListener($callback));

        return $this;
    }

    /**
     * 保存完成.
     *
     * @param  Closure  $callback
     * @return $this
     */
    public function saved(Closure $callback)
    {
        Event::listen(Events\Saved::class, $this->makeListener($callback));

        return $this;
    }

    /**
     * 删除.
     *
     * @param  Closure  $callback
     * @return $this
     */
    public function deleting(Closure $callback)
    {
        Event::listen(Events\Deleting::class, $this->makeListener($callback));

        return $this;
    }

    /**
     * 删除完成.
     *
     * @param  Closure  $callback
     * @return $this
     */
    public function deleted(Closure $callback)
    {
        Event::listen(Events\Deleted::class, $this->makeListener($callback));

        return $this;
    }

    /**
     * 文件上传.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function uploading(Closure $callback)
    {
        Event::listen(Events\Uploading::class, $this->makeListener($callback));

        return $this;
    }

    /**
     * 上传完成.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function uploaded(Closure $callback)
    {
        Event::listen(Events\Uploaded::class, $this->makeListener($callback));

        return $this;
    }

    /**
     * 删除文件.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function fileDeleting(Closure $callback)
    {
        Event::listen(Events\FileDeleting::class, $this->makeListener($callback));

        return $this;
    }

    /**
     * 删除文件完成.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function fileDeleted(Closure $callback)
    {
        Event::listen(Events\FileDeleted::class, $this->makeListener($callback));

        return $this;
    }

    /**
     * @param  \Closure  $callback
     * @return \Closure
     */
    protected function makeListener(Closure $callback)
    {
        return function (Events\Event $event) use ($callback) {
            if ($event->form !== $this) {
                return;
            }

            if ($model = $event->form->model()) {
                $callback = $callback->bindTo($model);
            }

            $ret = $callback($this, ...$event->payload);

            if ($ret instanceof Response || $ret instanceof JsonResponse) {
                $event->form->eventResponse = $ret;

                return false;
            }
        };
    }

    /**
     * 触发创建页访问事件.
     *
     * @return mixed
     */
    protected function callCreating()
    {
        return $this->fire(Events\Creating::class);
    }

    /**
     * 触发编辑页访问事件.
     *
     * @return mixed
     */
    protected function callEditing()
    {
        return $this->fire(Events\Editing::class);
    }

    /**
     * 触发表单提交事件.
     *
     * @return mixed
     */
    protected function callSubmitted()
    {
        return $this->fire(Events\Submitted::class);
    }

    /**
     * 触发表单保存事件.
     *
     * @return mixed
     */
    protected function callSaving()
    {
        return $this->fire(Events\Saving::class);
    }

    /**
     * 触发表单保存完成事件.
     *
     * @param  mixed  $result
     * @return mixed|null
     */
    protected function callSaved($result)
    {
        return $this->fire(Events\Saved::class, [$result]);
    }

    /**
     * 触发数据删除事件.
     *
     * @return mixed|null
     */
    protected function callDeleting()
    {
        return $this->fire(Events\Deleting::class);
    }

    /**
     * 触发数据删除完成事件.
     *
     * @param  mixed  $result
     * @return mixed|null
     */
    protected function callDeleted($result)
    {
        return $this->fire(Events\Deleted::class, [$result]);
    }

    /**
     * 触发文件上传事件.
     *
     * @param  UploadFieldInterface|\Dcat\Admin\Form\Field  $field
     * @param  UploadedFile  $file
     * @return mixed|null
     */
    protected function callUploading($field, $file)
    {
        return $this->fire(Events\Uploading::class, [$field, $file]);
    }

    /**
     * 触发文件上传完成事件.
     *
     * @param  UploadFieldInterface|\Dcat\Admin\Form\Field  $field
     * @param  UploadedFile  $file
     * @param  Response  $response
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    protected function callUploaded($field, $file, $response)
    {
        return $this->fire(Events\Uploaded::class, [$field, $file, $response]);
    }

    /**
     * 触发文件删除事件.
     *
     * @param  UploadFieldInterface|\Dcat\Admin\Form\Field  $field
     * @param  UploadedFile  $file
     * @param  Response  $response
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    protected function callFileDeleting($field)
    {
        return $this->fire(Events\FileDeleting::class, [$field]);
    }

    /**
     * 触发文件删除完成事件.
     *
     * @param  UploadFieldInterface|\Dcat\Admin\Form\Field  $field
     * @param  UploadedFile  $file
     * @param  Response  $response
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    protected function callFileDeleted($field)
    {
        return $this->fire(Events\FileDeleted::class, [$field]);
    }

    /**
     * @param  string  $name
     * @param  array  $payload
     * @return RedirectResponse|\Illuminate\Http\Response|void
     */
    protected function fire($name, array $payload = [])
    {
        Event::dispatch(new $name($this, $payload));

        return $this->eventResponse;
    }
}
