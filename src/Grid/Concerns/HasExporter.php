<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Exporter;
use Dcat\Admin\Grid\Exporters\AbstractExporter;
use Dcat\Admin\Grid\Tools;

trait HasExporter
{
    /**
     * @var Exporter
     */
    protected $exporter;

    /**
     * @var bool
     */
    protected $enableExporter = false;

    /**
     * @var bool
     */
    protected $exported = false;

    /**
     * Set exporter driver for Grid to export.
     *
     * @param string|Grid\Exporters\AbstractExporter|array $exporterDriver
     *
     * @return Exporter
     */
    public function export($exporterDriver = null)
    {
        $this->enableExporter = true;

        $titles = [];

        if (is_array($exporterDriver) || $exporterDriver === false) {
            $titles = $exporterDriver;
            $exporterDriver = null;
        }

        $exporter = $this->exporter();

        if ($exporterDriver) {
            $exporter->resolve($exporterDriver);
        }

        return $exporter->titles($titles);
    }

    /**
     * Handle export request.
     *
     * @param bool $forceExport
     *
     * @return mixed
     */
    public function handleExportRequest($forceExport = false)
    {
        if (
            $this->exported
            || (
                (! $this->allowExporter()
                    || ! $scope = request($this->exporter()->queryName()))
                && ! $forceExport
            )
        ) {
            return;
        }

        $this->exported = true;

        $this->callBuilder();

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
    public function exporter()
    {
        return $this->exporter ?: ($this->exporter = new Exporter($this));
    }

    /**
     * @param string $name
     *
     * @return void
     */
    protected function setExporterQueryName(string $name = null)
    {
        if (! $this->exporter) {
            return;
        }

        $this->exporter->setQueryName(($name ?: $this->getName()).'_export_');
    }

    /**
     * @param string $scope
     *
     * @return AbstractExporter
     */
    protected function resolveExportDriver($scope)
    {
        return $this->exporter()->driver()->withScope($scope);
    }

    /**
     * Get the export url.
     *
     * @param int  $scope
     * @param null $args
     *
     * @return string
     */
    public function exportUrl($scope = 1, $args = null)
    {
        $input = array_merge(request()->all(), $this->exporter()->formatExportQuery($scope, $args));

        if ($constraints = $this->model()->getConstraints()) {
            $input = array_merge($input, $constraints);
        }

        return $this->resource().'?'.http_build_query($input);
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
     * If grid show export btn.
     *
     * @return bool
     */
    public function allowExporter()
    {
        return $this->enableExporter;
    }
}
