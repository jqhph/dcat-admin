<?php

namespace Dcat\Admin\Repositories;

use Dcat\Admin\Form;

abstract class RepositoryListener
{
    /**
     * Creating event.
     *
     * @param Form $form
     */
    public function creating(Form $form)
    {
    }

    /**
     * Created event.
     *
     * @param Form  $form
     * @param mixed $newId
     */
    public function created(Form $form, $newId)
    {
    }

    /**
     * Updating event.
     *
     * @param Form  $form
     * @param array $originalAttributes
     */
    public function updating(Form $form, array $originalAttributes)
    {
    }

    /**
     * Updated event.
     *
     * @param Form  $form
     * @param array $originalAttributes
     * @param bool  $result
     */
    public function updated(Form $form, array $originalAttributes, $result)
    {
    }

    /**
     * Deleting event.
     *
     * @param Form  $form
     * @param array $originalAttributes
     */
    public function deleting(Form $form, array $originalAttributes)
    {
    }

    /**
     * Deleted event.
     *
     * @param Form  $form
     * @param array $originalAttributes
     * @param $result
     */
    public function deleted(Form $form, array $originalAttributes, $result)
    {
    }
}
