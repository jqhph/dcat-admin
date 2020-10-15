<?php

namespace Dcat\Admin\Form\Concerns;

use Dcat\Admin\Contracts\UploadField as UploadFieldInterface;
use Dcat\Admin\Form\Builder;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\NestedForm;
use Dcat\Admin\Support\WebUploader;
use Illuminate\Support\Arr;
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
        $file = app('admin.web-uploader')->getUploadedFile() ?: ($data[WebUploader::FILE_NAME] ?? null);

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
        return $this->builder->field($column);
    }

    /**
     * 新增页面删除文件.
     *
     * @param array $input
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function deleteFileWhenCreating(array $input)
    {
        if ($response = $this->deleteFileIfIsFileDeleteRequest($input)) {
            return $response;
        }

        $input = $this->handleFileDelete($input);

        $column = $input['_column'] ?? null;

        if (isset($input[Field::FILE_DELETE_FLAG]) && $column) {
            $this->builder->fields()->filter(function ($field) use ($column) {
                /* @var Field $field */

                return $column === $field->column() && $field instanceof UploadFieldInterface;
            })->each(function (UploadFieldInterface $file) use ($input) {
                /* @var Field $file */
                $this->deleteFile($file, $input[Field::FILE_DELETE_FLAG]);
            });

            return $this
                ->response()
                ->status(true)
                ->send();
        }
    }

    /**
     * 删除文件.
     *
     * @param UploadFieldInterface|Field $field
     * @param array                      $input
     */
    protected function deleteFile(UploadFieldInterface $field, $input = null)
    {
        if ($input) {
            if (
                is_string($input)
                || (is_array($input) && ! Arr::isAssoc($input))
            ) {
                $input = [$field->column() => $input];
            }

            $field->setOriginal($input);
        }

        if ($this->callFileDeleting($field) === false) {
            return;
        }

        $field->destroy();

        $this->callFileDeleted($field);
    }

    /**
     * 如果是删除文件请求，则直接删除文件.
     *
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse|void
     */
    protected function deleteFileIfIsFileDeleteRequest(array $data)
    {
        if (! array_key_exists(Field::FILE_DELETE_FLAG, $data)) {
            return;
        }

        $column = $data['_column'] ?? null;
        $filePath = $data['key'] ?? null;
        $relation = $data['_relation'] ?? null;

        if (! $column && ! $filePath) {
            return;
        }

        if (empty($relation)) {
            $field = $this->findFieldByName($column);
        } else {
            $field = $this->getFieldByRelationName($relation[0], $column);
        }

        if ($field && $field instanceof UploadFieldInterface) {
            $this->deleteFile($field, $filePath);

            return $this
                ->response()
                ->status(true)
                ->send();
        }

        return;
    }

    /**
     * 获取hasMany的子表单字段.
     *
     * @param string $relation
     * @param string $column
     *
     * @return mixed
     */
    public function getFieldByRelationName($relation, $column)
    {
        $relation = $this->findFieldByName($relation);

        if ($relation && $relation instanceof Field\HasMany) {
            return $relation->buildNestedForm()->fields()->first(function ($field) use ($column) {
                return $field->column() === $column;
            });
        }
    }

    /**
     * 根据传入数据删除文件.
     *
     * @param array $input
     * @param bool  $forceDelete
     */
    public function deleteFiles($input, $forceDelete = false)
    {
        // If it's a soft delete, the files in the data will not be deleted.
        if (! $forceDelete && $this->isSoftDeletes) {
            return;
        }

        $this->builder
            ->fields()
            ->filter(function ($field) {
                return $field instanceof UploadFieldInterface;
            })
            ->each(function (UploadFieldInterface $field) use ($input) {
                $this->deleteFile($field, $input);
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
}
