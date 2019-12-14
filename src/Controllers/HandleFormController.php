<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Widgets\Form;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleFormController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        $form = $this->resolveForm($request);

        if ($errors = $form->validate($request)) {
            return $form->validationErrorsResponse($errors);
        }

        $input = $form->sanitize($request->all());

        return $form->handle($input) ?: $form->success();
    }

    /**
     * @param Request $request
     *
     * @throws Exception
     *
     * @return Form
     */
    protected function resolveForm(Request $request)
    {
        if (! $request->has('_form_')) {
            throw new Exception('Invalid form request.');
        }

        $formClass = $request->get('_form_');

        if (! class_exists($formClass)) {
            throw new Exception("Form [{$formClass}] does not exist.");
        }

        /** @var Form $form */
        $form = app($formClass);

        if (! method_exists($form, 'handle')) {
            throw new Exception("Form method {$formClass}::handle() does not exist.");
        }

        return $form;
    }
}
