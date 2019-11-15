<?php

namespace Dcat\Admin\Form\Concerns;

use Closure;
use Dcat\Admin\Form\Builder;
use Dcat\Admin\Form\StepBuilder;

/**
 * @property Builder $builder
 */
trait HasSteps
{
    /**
     * @param Closure|null $builder
     * @return StepBuilder
     */
    public function step(\Closure $builder = null)
    {
        return $this->builder->step($builder);
    }

    /**
     * @param array $data
     * @return void
     */
    protected function prepareStepFormFields(array $data)
    {
        $stepBuilder = $this->builder->getStepBuilder();

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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function validateStepForm(array $data)
    {
        // Handle validation errors.
        if ($validationMessages = $this->validationMessages($data)) {
            return $this->makeValidationErrorsResponse($validationMessages);
        }

        // Stash input data.
        $this->step()->stash($data);

        return $this->ajaxResponse('Success');
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|void
     */
    protected function responseDoneStep()
    {
        if (! $builder = $this->builder->getStepBuilder()) {
            return;
        }

        return response(
            $builder->getDoneStep()
                ->finish()
                ->render()
        );
    }

    /**
     * @param array $input
     * @return void
     */
    protected function deleteFileInStepFormStashData($input = [])
    {
        if (empty($input['_column'])) {
            return;
        }

        $this->step()->stashIndexByField($input['_column']);
        $this->step()->forgetStash($input['_column']);
    }
}
