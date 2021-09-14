<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Support\Helper;

class MultipleFile extends File
{
    protected $view = 'admin::form.file';

    /**
     * Allow to sort files.
     *
     * @param  bool  $value
     * @return $this
     */
    public function sortable(bool $value = true)
    {
        $this->options['sortable'] = $value;

        return $this;
    }

    /**
     * Set a limit of files.
     *
     * @param  int  $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        if ($limit < 2) {
            return $this;
        }
        $this->options['fileNumLimit'] = $limit;

        return $this;
    }

    /**
     * Prepare for saving.
     *
     * @param  string|array  $file
     * @return array
     */
    protected function prepareInputValue($file)
    {
        if ($path = request(static::FILE_DELETE_FLAG)) {
            $this->deleteFile($path);

            return array_values(array_diff($this->original, [$path]));
        }

        $file = Helper::array($file, true);

        $this->destroyIfChanged($file);

        return $file;
    }

    protected function forceOptions()
    {
    }
}
