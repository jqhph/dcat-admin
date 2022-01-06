<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Exporters\ExporterInterface;

/**
 * @mixin Grid\Exporters\AbstractExporter
 *
 * @method mixed export
 */
class Exporter
{
    /**
     * Export scope constants.
     */
    const SCOPE_ALL = 'all';
    const SCOPE_CURRENT_PAGE = 'page';
    const SCOPE_SELECTED_ROWS = 'selected';

    /**
     * Available exporter drivers.
     *
     * @var array
     */
    protected static $drivers = [];

    /**
     * Export query name.
     *
     * @var string
     */
    protected $queryName = '_export_';

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var Grid\Exporters\AbstractExporter
     */
    protected $driver;

    /**
     * @var array
     */
    protected $options = [
        'show_export_all'           => true,
        'show_export_current_page'  => true,
        'show_export_selected_rows' => true,
        'chunk_size'                => 5000,
    ];

    /**
     * Create a new Exporter instance.
     *
     * @param  Grid  $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     *  Get or set option for exporter.
     *
     * @param  string  $key
     * @param  mixed|null  $value
     * @return $this|mixed|null
     */
    public function option($key, $value = null)
    {
        if ($value === null) {
            return $this->options[$key] ?? null;
        }

        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Disable export all.
     *
     * @param  bool  $value
     * @return $this
     */
    public function disableExportAll(bool $value = true)
    {
        return $this->option('show_export_all', ! $value);
    }

    /**
     * Disable export current page.
     *
     * @param  bool  $value
     * @return $this
     */
    public function disableExportCurrentPage(bool $value = true)
    {
        return $this->option('show_export_current_page', ! $value);
    }

    /**
     * Disable export selected rows.
     *
     * @param  bool  $value
     * @return $this
     */
    public function disableExportSelectedRow(bool $value = true)
    {
        return $this->option('show_export_selected_rows', ! $value);
    }

    /**
     * @param  int  $value
     * @return $this
     */
    public function chunkSize(int $value)
    {
        return $this->option('chunk_size', $value);
    }

    /**
     * Get export query name.
     *
     * @return string
     */
    public function getQueryName(): string
    {
        return $this->grid->makeName($this->queryName);
    }

    /**
     * Extends new exporter driver.
     *
     * @param $driver
     * @param $extend
     */
    public static function extend($driver, $extend)
    {
        static::$drivers[$driver] = $extend;
    }

    /**
     * Resolve export driver.
     *
     * @param  string  $driver
     * @return Grid\Exporters\AbstractExporter
     */
    public function resolve($driver = null)
    {
        if ($this->driver) {
            return $this->driver;
        }

        if ($driver && $driver instanceof Grid\Exporters\AbstractExporter) {
            $this->driver = $driver->setGrid($this->grid);
        } elseif ($driver && $driver instanceof ExporterInterface) {
            $this->driver = $driver;
        } else {
            $this->driver = $this->newDriver($driver);
        }

        return $this->driver;
    }

    /**
     * @return Exporters\AbstractExporter
     */
    public function driver()
    {
        return $this->driver ?: $this->resolve();
    }

    /**
     * Get export driver.
     *
     * @param  string  $driver
     * @return Grid\Exporters\AbstractExporter
     */
    protected function newDriver($driver): ExporterInterface
    {
        if (! $driver || ! array_key_exists($driver, static::$drivers)) {
            return $this->makeDefaultDriver();
        }

        $driver = new static::$drivers[$driver]();

        if (method_exists($driver, 'setGrid')) {
            $driver->setGrid($this->grid);
        }

        return $driver;
    }

    /**
     * Get default exporter.
     *
     * @return Grid\Exporters\ExcelExporter
     */
    public function makeDefaultDriver()
    {
        return Grid\Exporters\ExcelExporter::make()->setGrid($this->grid);
    }

    /**
     * Format query for export url.
     *
     * @param  int  $scope
     * @param  null  $args
     * @return array
     */
    public function formatExportQuery($scope = '', $args = null)
    {
        $query = '';

        if ($scope == static::SCOPE_ALL) {
            $query = $scope;
        }

        if ($scope == static::SCOPE_CURRENT_PAGE) {
            $query = "$scope:$args";
        }

        if ($scope == static::SCOPE_SELECTED_ROWS) {
            $query = "$scope:$args";
        }

        return [$this->getQueryName() => $query];
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $this->driver()->$method(...$arguments);

        return $this;
    }
}
