<?php

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Exception\AdminException;
use Illuminate\Http\Request;

class HandleActionController
{
    /**
     * @param  Request  $request
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        $action = $this->resolveActionInstance($request);

        $action->setKey($request->get('_key'));

        if (! $action->passesAuthorization()) {
            $response = $action->failedAuthorization();
        } else {
            $response = $action->handle($request);
        }

        return $response instanceof Response ? $response->send() : $response;
    }

    /**
     * @param  Request  $request
     * @return Action
     *
     * @throws AdminException
     */
    protected function resolveActionInstance(Request $request): Action
    {
        if (! $request->has('_action')) {
            throw new AdminException('Invalid action request.');
        }

        $actionClass = str_replace('_', '\\', $request->get('_action'));

        if (! class_exists($actionClass)) {
            throw new AdminException("Action [{$actionClass}] does not exist.");
        }

        /** @var Action $action */
        $action = app($actionClass);

        if (! method_exists($action, 'handle')) {
            throw new AdminException("Action method {$actionClass}::handle() does not exist.");
        }

        return $action;
    }
}
