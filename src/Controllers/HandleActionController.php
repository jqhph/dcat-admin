<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Actions\Response;
use Exception;
use Illuminate\Http\Request;

class HandleActionController
{
    /**
     * @param Request $request
     *
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        try {
            $action = $this->resolveActionInstance($request);

            $action->setKey($request->get('_key'));

            if (! $action->passesAuthorization()) {
                return $action->failedAuthorization();
            }

            $response = $action->handle($request);
        } catch (\Throwable $exception) {
            return Response::withException($exception)->send();
        }

        return $response instanceof Response ? $response->send() : $response;
    }

    /**
     * @param Request $request
     *
     * @throws Exception
     *
     * @return Action
     */
    protected function resolveActionInstance(Request $request): Action
    {
        if (! $request->has('_action')) {
            throw new Exception('Invalid action request.');
        }

        $actionClass = str_replace('_', '\\', $request->get('_action'));

        if (! class_exists($actionClass)) {
            throw new Exception("Action [{$actionClass}] does not exist.");
        }

        /** @var Action $action */
        $action = app($actionClass);

        if (! method_exists($action, 'handle')) {
            throw new Exception("Action method {$actionClass}::handle() does not exist.");
        }

        return $action;
    }
}
