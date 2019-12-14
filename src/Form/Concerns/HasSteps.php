<?php

namespace Dcat\Admin\Form\Concerns;

use Closure;
use Dcat\Admin\Form\Builder;
use Dcat\Admin\Form\StepBuilder;
use Dcat\Admin\Form\StepForm;

/**
 * @property Builder $builder
 */
trait HasSteps
{
    /**
     * @param Closure|StepForm[]|null $builder
     *
     * @return StepBuilder
     */
    public function multipleSteps($builder = null)
    {
        return $this->builder->multipleSteps($builder);
    }

    /**
     * @param array $data
     *
     * @return void
     */
    protected function prepareStepFormFields(array $data)
    {
        $stepBuilder = $this->builder->stepBuilder();

        if (
            empty($stepBuilder)
            || empty($stepBuilder->count())
            || (! isset($data[StepBuilder::ALL_STEPS]) && ! $this->isStepFormValidationRequest())
        ) {
            return;
        }

        $steps = $stepBuilder->all();

        if ($this->isStepFormValidationRequest()) {
            $currentIndex = $data[StepBuilder::CURRENT_VALIDATION_STEP];

            if (empty($steps[$currentIndex])) {
                return;
            }

            foreach ($steps[$currentIndex]->fields() as $field) {
                $this->pushField($field);
            }

            return;
        }

        if (! empty($data[StepBuilder::ALL_STEPS])) {
            foreach ($steps as $stepForm) {
                foreach ($stepForm->fields() as $field) {
                    $this->pushField($field);
                }
            }
        }
    }

    /**
     * @return bool
     */
    protected function isStepFormValidationRequest()
    {
        $index = $this->request->get(StepBuilder::CURRENT_VALIDATION_STEP);

        return $index !== '' && $index !== null;
    }

    /**
     * Validate step form.
     *
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function validateStepForm(array $data)
    {
        // Handle validation errors.
        if ($validationMessages = $this->validationMessages($data)) {
            return $this->validationErrorsResponse($validationMessages);
        }

        // Stash input data.
        $this->multipleSteps()->stash($data);

        return $this->ajaxResponse('Success');
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|void
     */
    protected function responseDoneStep()
    {
        if (! $builder = $this->builder->stepBuilder()) {
            return;
        }

        return response(
            $builder->doneStep()
                ->finish()
                ->render()
        );
    }

    /**
     * @param array $input
     *
     * @return void
     */
    protected function deleteFileInStepFormStashData($input = [])
    {
        if (empty($input['_column'])) {
            return;
        }

        $this->multipleSteps()->stashIndexByField($input['_column']);
        $this->multipleSteps()->forgetStash($input['_column']);
    }
}
