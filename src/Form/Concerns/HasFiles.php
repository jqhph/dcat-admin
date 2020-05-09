<?php

namespace Dcat\Admin\Form\Concerns;

use Dcat\Admin\Contracts\UploadField as UploadFieldInterface;
use Dcat\Admin\Form\Builder;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\NestedForm;
use Dcat\Admin\Support\WebUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property Builder $builder
 */
trait HasFiles
{
    /**
     * 文件上传操作.
     *
     * @param array $data
     *
     * @return Response|void
     */
    protected function handleUploadFile($data)
    {
        $column = $data['upload_column'] ?? null;
        $file = app('admin.web-uploader')->getCompleteUploadedFile() ?: ($data[WebUploader::FILE_NAME] ?? null);

        if (! $column || ! $file instanceof UploadedFile) {
            return;
        }

        $relation = $data['_relation'] ?? null;

        if (empty($relation)) {
            $field = $this->findFieldByName($column);
        } else {
            // hasMany表单文件上传
            $relation = explode(',', $relation)[0];

            $field = $this->getFieldByRelationName($relation, $column);
        }

        if ($field && $field instanceof UploadFieldInterface) {
            if (($results = $this->callUploading($field, $file)) && $results instanceof Response) {
                return $results;
            }

            $response = $field->upload($file);

            if (($results = $this->callUploaded($field, $file, $response)) && $results instanceof Response) {
                return $results;
            }

            return $response;
        }
    }

    /**
     * 根据字段名称查找字段.
     *
     * @param string|null $column
     *
     * @return Field|null
     */
    public function findFieldByName(?string $column)
    {
        if ($field = $this->builder->field($column)) {
            return $field;
        }

        return $this->builder->field($column) ?: $this->builder->stepField($column);
    }

    /**
     * 新增之前删除文件操作.
     *
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
        $relation = $data['_relation'] ?? null;

        if (! $column && ! $file) {
            return;
        }

        if (empty($relation)) {
            $field = $this->builder->field($column) ?: $this->builder->stepField($column);
        } else {
            $field = $this->getFieldByRelationName($relation[0], $column);
        }

        if ($field && $field instanceof UploadFieldInterface) {
            $field->deleteFile($file);

            return response()->json(['status' => true]);
        }
    }

    /**
     * 获取hasMany的子表单字段.
     *
     * @param string $relation
     * @param string $column
     *
     * @return mixed
     */
    protected function getFieldByRelationName($relation, $column)
    {
        $relation = $this->findFieldByName($relation);

        if ($relation && $relation instanceof Field\HasMany) {
            return $relation->buildNestedForm()->fields()->first(function ($field) use ($column) {
                return $field->column() === $column;
            });
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
                return $field instanceof UploadFieldInterface;
            })
            ->each(function (UploadFieldInterface $file) use ($input) {
                $file->setOriginal($input);

                $file->destroy();
            });
    }

    /**
     * 根据传入数据删除文件.
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
                || $field instanceof UploadFieldInterface;
        })->each(function (UploadFieldInterface $file) use ($data) {
            $file->setOriginal($data);

            $file->destroy();
        });
    }

    /**
     * 编辑页面删除上传文件操作.
     *
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

        if (! empty($input['_column'])) {
            if (empty($input['_relation'])) {
                $input[$input['_column']] = '';
            } else {
                [$relation, $relationKey] = $input['_relation'];
                $keyName = $this->builder()->field($relation)->getKeyName();

                $input[$relation] = [
                    $relationKey => [
                        $keyName                     => $relationKey,
                        $input['_column']            => '',
                        NestedForm::REMOVE_FLAG_NAME => null,
                    ],
                ];
            }
        }

        unset($input['key'], $input['_column'], $input['_relation']);

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

                return $column === $field->column() && $field instanceof UploadFieldInterface;
            })->each(function (UploadFieldInterface $file) use ($input) {
                $file->deleteFile($input[Field::FILE_DELETE_FLAG]);
            });

            return \response()->json(['status' => true]);
        }
    }
}
