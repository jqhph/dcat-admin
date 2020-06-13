<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Form\Field\File;
use Dcat\Admin\Traits\HasUploadedFile;
use Dcat\Admin\Widgets\Form;
use Exception;
use Illuminate\Http\Request;

class HandleFormController
{
    use HasUploadedFile;

    public function handle(Request $request)
    {
        $form = $this->resolveForm($request);

        if (! $form->passesAuthorization()) {
            return $form->failedAuthorization();
        }

        if ($errors = $form->validate($request)) {
            return $form->validationErrorsResponse($errors);
        }

        $input = $form->sanitize($request->all());

        return $form->handle($input) ?: $form->success();
    }

    public function uploadFile(Request $request)
    {
        $form = $this->resolveForm($request);

        /* @var $field File */
        $field = $form->field($this->uploader()->upload_column);

        return $field->upload($this->file());
    }

    public function destroyFile(Request $request)
    {
        $form = $this->resolveForm($request);

        /* @var $field File */
        $field = $form->field($request->_column);

        $field->deleteFile($request->key);

        return $this->responseDeleted();
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
        if (! $request->has(Form::REQUEST_NAME)) {
            throw new Exception('Invalid form request.');
        }

        $formClass = $request->get(Form::REQUEST_NAME);

        if (! class_exists($formClass)) {
            throw new Exception("Form [{$formClass}] does not exist.");
        }

        /** @var Form $form */
        $form = app($formClass);

        if (! method_exists($form, 'handle')) {
            throw new Exception("Form method {$formClass}::handle() does not exist.");
        }

        if (method_exists($form, 'form')) {
            $form->form();
        }

        return $form;
    }
}
