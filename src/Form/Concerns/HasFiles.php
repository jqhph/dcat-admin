<?php

namespace Dcat\Admin\Form\Concerns;

use Dcat\Admin\Form\Builder;
use Dcat\Admin\Form\Field;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @property Builder $builder
 */
trait HasFiles
{
    /**
     * @param array $data
     */
    protected function handleUploadFile($data)
    {
        $column = $data['upload_column'] ?? null;
        $file = $data['file'] ?? null;

        if (! $column && ! $file instanceof UploadedFile) {
            return;
        }

        $field = $this->builder->field($column) ?: $this->builder->stepField($column);

        if ($field && $field instanceof Field\File) {
            return $field->upload($file);
        }
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse|void
     */
    protected function handleFileDeleteBeforeCreate(array $data)
    {
        if (! array_key_exists(Field::FILE_DELETE_FLAG, $data)) {
            return;
        }

        $column = $data['_column'] ?? null;
        $file = $data['key'] ?? null;

        if (! $column && ! $file) {
            return;
        }

        $field = $this->builder->field($column) ?: $this->builder->stepField($column);

        if ($field && in_array(Field\UploadField::class, class_uses($field))) {
            $field->deleteFile($file);

            return response()->json(['status' => true]);
        }
    }

    /**
     * @param array $input
     *
     * @return void
     */
    public function deleteFilesWhenCreating(array $input)
    {
        $this->builder
            ->fields()
            ->filter(function ($field) {
                return $field instanceof Field\File;
            })
            ->each(function (Field\File $file) use ($input) {
                $file->setOriginal($input);

                $file->destroy();
            });
    }

    /**
     * Remove files in record.
     *
     * @param array $data
     * @param bool  $forceDelete
     */
    public function deleteFiles($data, $forceDelete = false)
    {
        // If it's a soft delete, the files in the data will not be deleted.
        if (! $forceDelete && $this->isSoftDeletes) {
            return;
        }

        $this->builder->fields()->filter(function ($field) {
            return $field instanceof Field\BootstrapFile
                || $field instanceof Field\File;
        })->each(function ($file) use ($data) {
            $file->setOriginal($data);

            $file->destroy();
        });
    }

    /**
     * @param array $input
     *
     * @return array
     */
    protected function handleFileDelete(array $input = [])
    {
        if (! array_key_exists(Field::FILE_DELETE_FLAG, $input)) {
            return $input;
        }

        $input[Field::FILE_DELETE_FLAG] = $input['key'];
        unset($input['key']);

        if (! empty($input['_column'])) {
            $input[$input['_column']] = '';

            unset($input['_column']);
        }

        $this->request->replace($input);

        return $input;
    }

    /**
     * @param array $input
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleFileDeleteWhenCreating(array $input)
    {
        $input = $this->handleFileDelete($input);

        $column = $input['_column'] ?? null;

        if (isset($input[Field::FILE_DELETE_FLAG]) && $column) {
            $this->builder->fields()->filter(function ($field) use ($column) {
                /* @var Field $field */

                return $column === $field->column() && $field instanceof Field\File;
            })->each(function (Field\File $file) use ($input) {
                $file->deleteFile($input[Field::FILE_DELETE_FLAG]);
            });

            return \response()->json(['status' => true]);
        }
    }
}
