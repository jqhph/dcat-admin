<?php
/*
 * This file is part of the Dcat package.
 *
 * (c) Pian Zhou <pianzhou2021@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dcat\Admin\Form\Concerns;

use Dcat\Admin\Contracts\FieldsCollection;
use Dcat\Admin\Form\Field;
use Illuminate\Support\Collection;

trait HasFields
{
    /**
     * @var Collection|Field[]
     */
    private $fields;

    /**
     * Get fields of this builder.
     *
     * @return Collection
     */
    public function fields()
    {
        if (! $this->fields) {
            $this->resetFields();
        }

        return $this->fields;
    }

    /**
     * Get specify field.
     *
     * @param  string|Field  $name
     * @return Field|null
     */
    public function field($name)
    {
        return $this->fields()->first(function (Field $field) use ($name) {
            if (is_array($field->column())) {
                $result = in_array($name, $field->column(), true) || $field->column() === $name ? $field : null;

                if ($result) {
                    return $result;
                }
            }

            return $field === $name || $field->column() === $name;
        });
    }

    /**
     * Remove Field.
     *
     * @param $column
     * @return void
     */
    public function removeField($column)
    {
        $this->fields = $this->fields()->filter(function (Field $field) use ($column) {
            return $field->column() != $column;
        });
    }

    /**
     * Push Field.
     *
     * @param  Field  $field
     * @return Collection
     */
    public function pushField(Field $field)
    {
        $this->fields()->push($field);
    }

    /**
     * Reset Fields.
     *
     * @return void
     */
    public function resetFields()
    {
        $this->fields = new Collection();
    }

    /**
     * Reject Fields.
     *
     * @param [type] $reject
     * @return void
     */
    public function rejectFields($reject)
    {
        $this->fields = $this->fields()->reject($reject);
    }

    /**
     * Set Fields.
     *
     * @param  Collection  $fields
     * @return void
     */
    public function setFields(Collection $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Get all merged fields.
     *
     * @return array
     */
    protected function mergedFields()
    {
        $fields = [];
        foreach ($this->fields() as $field) {
            if ($field instanceof FieldsCollection) {
                /** @var Field $field */
                $fields = array_merge($fields, $field->mergedFields());
            } else {
                $fields[] = $field;
            }
        }

        return $fields;
    }
}
