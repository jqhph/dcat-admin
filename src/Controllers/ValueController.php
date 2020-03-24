<?php

namespace Dcat\Admin\Controllers;

use Exception;
use Illuminate\Http\Request;

class ValueController
{
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
        $instance = $this->resolve($request);

        if (! $instance->passesAuthorization()) {
            return $instance->failedAuthorization();
        }

        $response = $instance->handle($request);

        if ($response) {
            return $response;
        }

        if (method_exists($instance, 'result')) {
            return $instance->result();
        }
    }

    /**
     * @param Request $request
     *
     * @throws Exception
     *
     * @return \Dcat\Admin\Traits\FromApi
     */
    protected function resolve(Request $request)
    {
        if (! $key = $request->get('_key')) {
            throw new Exception('Invalid request.');
        }

        if (! class_exists($key)) {
            throw new Exception("Class [{$key}] does not exist.");
        }

        $instance = app($key);

        if (! method_exists($instance, 'handle')) {
            throw new Exception("The method '{$key}::handle()' does not exist.");
        }

        return $instance;
    }
}
