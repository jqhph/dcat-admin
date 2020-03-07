<?php

namespace Dcat\Admin\Actions;

use Dcat\Admin\Admin;
use Dcat\Admin\Models\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

trait HasActionHandler
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @return Response
     */
    public function response()
    {
        if (is_null($this->response)) {
            $this->response = new Response();
        }

        return $this->response;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    protected function parameters()
    {
        return [];
    }

    /**
     * Confirm message of action.
     *
     * @return string|void
     */
    public function confirm()
    {
    }

    /**
     * @return mixed
     */
    public function getCalledClass()
    {
        return str_replace('\\', '_', get_called_class());
    }

    /**
     * @return string
     */
    public function getHandleRoute()
    {
        return admin_url('_handle_action_');
    }

    /**
     * @return void
     */
    protected function addHandlerScript()
    {
        $script = <<<JS
$('{$this->selector()}').off('{$this->event}').on('{$this->event}', function() {
    var data = $(this).data(),
        target = $(this);
    if (target.attr('working') > 0) {
        return;
    }
    {$this->actionScript()}
    {$this->buildRequestScript()}
});
JS;

        Admin::script($script);
    }

    /**
     * @return string
     */
    protected function buildRequestScript()
    {
        $parameters = json_encode($this->parameters());

        return <<<JS
function request() {
    target.attr('working', 1);
    Object.assign(data, {$parameters});
    {$this->buildActionPromise()}
    {$this->handleActionPromise()}
}

if (data['confirm']) {
    LA.confirm(data['confirm'], request);
} else {
    request()
}
JS;
    }

    /**
     * @return string
     */
    protected function actionScript()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function buildActionPromise()
    {
        return <<<JS
var process = new Promise(function (resolve,reject) {
    Object.assign(data, {
        _token: LA.token,
        _action: '{$this->getCalledClass()}',
        _key: '{$this->key()}',
    });
    LA.NP.start();
    $.ajax({
        method: '{$this->getMethod()}',
        url: '{$this->getHandleRoute()}',
        data: data,
        success: function (data) {
            target.attr('working', 0);
            LA.NP.done();
            resolve([data, target]);
        },
        error:function(request){
            target.attr('working', 0);
            LA.NP.done();
            reject([request, target]);
        }
    });
});
JS;
    }

    /**
     * @return string
     */
    protected function resolverScript()
    {
        return <<<JS
function (data) {
    var response = data[0],
        target   = data[1];
        
    if (typeof response !== 'object') {
        return LA.error({type: 'error', title: 'Oops!'});
    }
    
    var then = function (then) {
        switch (then.action) {
            case 'refresh':
                LA.reload();
                break;
            case 'download':
                window.open(then.value, '_blank');
                break;
            case 'redirect':
                LA.reload(then.value);
                break;
            case 'location':
                window.location = then.value;
                break;
            case 'script':
                (function () {
                    eval(then.value);
                })();
                break;
        }
    };

    if (typeof response.html === 'string' && response.html) {
        {$this->handleHtmlResponse()};
    }

    if (typeof response.data.message === 'string' && response.data.type) {
        LA[response.data.type](response.data.message);
    }
    
    if (response.data.then) {
      then(response.data.then);
    }
}
JS;
    }

    /**
     * @return string
     */
    protected function handleHtmlResponse()
    {
        return <<<'JS'
target.html(response.html);
JS;
    }

    /**
     * @return string
     */
    protected function rejectScript()
    {
        return <<<'JS'
function (data) {
    var request = data[0], target = data[1];
    
    if (request && typeof request.responseJSON === 'object') {
        LA.error(request.responseJSON.message)
    }
    console.error(request);
}
JS;
    }

    /**
     * @return string
     */
    public function handleActionPromise()
    {
        return <<<JS
process.then({$this->resolverScript()}).catch({$this->rejectScript()});
JS;
    }

    /**
     * @return bool
     */
    public function passesAuthorization()
    {
        return $this->authorize(Admin::user());
    }

    /**
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return true;
    }

    /**
     * @return Response
     */
    public function failedAuthorization()
    {
        return $this->response()->error(__('admin.deny'));
    }
}
