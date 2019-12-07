<?php

namespace Dcat\Admin\Form\Field;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class BootstrapMultipleImage extends BootstrapMultipleFile
{
    use ImageField;

    /**
     * {@inheritdoc}
     */
    protected $view = 'admin::form.bootstrapmultiplefile';

    /**
     *  Validation rules.
     *
     * @var string
     */
    protected $rules = ['image'];

    /**
     * Prepare for each file.
     *
     * @param UploadedFile $image
     *
     * @return mixed|string
     */
    protected function prepareForeach(UploadedFile $image = null)
    {
        $this->name = $this->getStoreName($image);

        $this->callInterventionMethods($image->getRealPath(), $image->getMimeType());

        return tap($this->upload($image), function () {
            $this->name = null;
        });
    }

    /**
     * Render a image form field.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $this->options(['allowedFileTypes' => ['image']]);

        return parent::render();
    }
}
