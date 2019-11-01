<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid\Exporter;
use Dcat\Admin\Grid\Exporters\AbstractExporter;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools;

trait HasExporter
{
    /**
     * @var Exporter
     */
    protected $exporter;

    /**
     * Export driver.
     *
     * @var string|Grid\Exporters\AbstractExporter
     */
    protected $exportDriver;

    /**
     * Set exporter driver for Grid to export.
     *
     * @param string|Grid\Exporters\AbstractExporter $exporter
     *
     * @return $this
     */
    public function exporter($exporter)
    {
        $this->exportDriver = $exporter;

        return $this;
    }

    /**
     * Handle export request.
     *
     * @param bool $forceExport
     *
     * @return mixed
     */
    protected function handleExportRequest($forceExport = false)
    {
        if (! $scope = request($this->getExporter()->getQueryName())) {
            return;
        }

        if ($this->builder) {
            call_user_func($this->builder, $this);

            $this->builder = null;
        }

        // clear output buffer.
        if (ob_get_length()) {
            ob_end_clean();
        }

        if ($forceExport || $this->allowExporter()) {
            return $this->resolveExportDriver($scope)->export();
        }
    }

    /**
     * @return Exporter
     */
    protected function getExporter()
    {
        return $this->exporter ?: ($this->exporter = new Exporter($this));
    }

    /**
     * @param string $gridName
     */
    protected function setExporterQueryName($gridName)
    {
        if (! $this->allowExporter()) {
            return;
        }

        $this->getExporter()->setQueryName($gridName.'_export_');
    }

    /**
     * @param string $scope
     *
     * @return AbstractExporter
     */
    protected function resolveExportDriver($scope)
    {
        return $this->getExporter()->resolve($this->exportDriver)->withScope($scope);
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
        $input = array_merge(request()->all(), $this->getExporter()->formatExportQuery($scope, $args));

        if ($constraints = $this->model()->getConstraints()) {
            $input = array_merge($input, $constraints);
        }

        return $this->getResource().'?'.http_build_query($input);
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setExporterOptions(array $options)
    {
        if (isset($options['chunk_size'])) {
            $this->options['export_chunk_size'] = (int) $options['chunk_size'];
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
        if (! $this->allowExporter()) {
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
        return $this->option('show_exporter', ! $disable);
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
    public function allowExporter()
    {
        return $this->options['show_exporter'];
    }

}
