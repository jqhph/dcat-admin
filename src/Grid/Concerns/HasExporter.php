<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid\Exporter;
use Dcat\Admin\Grid\Exporters\AbstractExporter;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools;
use Illuminate\Support\Facades\Input;

trait HasExporter
{
    /**
     * Export driver.
     *
     * @var string|Grid\Exporters\AbstractExporter
     */
    protected $exporter;

    /**
     * Set exporter driver for Grid to export.
     *
     * @param string|Grid\Exporters\AbstractExporter $exporter
     *
     * @return $this
     */
    public function exporter($exporter)
    {
        $this->exporter = $exporter;

        return $this;
    }

    /**
     * Handle export request.
     *
     * @param bool $forceExport
     */
    protected function handleExportRequest($forceExport = false)
    {
        if (!$scope = request(Exporter::$queryName)) {
            return;
        }

        // clear output buffer.
        if (ob_get_length()) {
            ob_end_clean();
        }

        $this->model()->usePaginate(false);

        if ($this->builder) {
            call_user_func($this->builder, $this);

            $this->getExporter($scope)->export();
        }

        if ($forceExport) {
            $this->getExporter($scope)->export();
        }
    }

    /**
     * @param string $scope
     *
     * @return AbstractExporter
     */
    protected function getExporter($scope)
    {
        return (new Exporter($this))->resolve($this->exporter)->withScope($scope);
    }


    /**
     * Get the export url.
     *
     * @param int  $scope
     * @param null $args
     *
     * @return string
     */
    public function getExportUrl($scope = 1, $args = null)
    {
        $input = array_merge(Input::all(), Exporter::formatExportQuery($scope, $args));

        if ($constraints = $this->model()->getConstraints()) {
            $input = array_merge($input, $constraints);
        }

        return $this->getResource().'?'.http_build_query($input);
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setExportOptions(array $options)
    {
        if (isset($options['limit'])) {
            $this->options['export_limit'] = $options['limit'];
        }

        if (isset($options['all'])) {
            $this->options['show_export_all'] = $options['show_all'];
        }

        if (isset($options['current_page'])) {
            $this->options['show_export_current_page'] = $options['current_page'];
        }

        if (isset($options['selected_rows'])) {
            $this->options['show_export_selected_rows'] = $options['selected_rows'];
        }

        return $this;
    }

    /**
     * Render export button.
     *
     * @return string
     */
    public function renderExportButton()
    {
        if (!$this->options['show_exporter']) {
            return '';
        }
        return (new Tools\ExportButton($this))->render();
    }

    /**
     * Disable export.
     *
     * @return $this
     */
    public function disableExporter(bool $disable = true)
    {
        return $this->option('show_exporter', !$disable);
    }

    /**
     * Show export button.
     *
     * @return $this
     */
    public function showExporter(bool $val = true)
    {
        return $this->disableExporter(!$val);
    }

    /**
     * If grid show export btn.
     *
     * @return bool
     */
    public function allowExportBtn()
    {
        return $this->options['show_exporter'];
    }

}
