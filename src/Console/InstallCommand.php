<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Models\AdminTablesSeeder;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the admin package';

    /**
     * Install directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->initDatabase();

        $this->initAdminDirectory();

        $this->info('Done.');
    }

    /**
     * Create tables and seed it.
     *
     * @return void
     */
    public function initDatabase()
    {
        $this->call('migrate');

        $userModel = config('admin.database.users_model');

        if ($userModel::count() == 0) {
            $this->call('db:seed', ['--class' => AdminTablesSeeder::class]);
        }
    }

    /**
     * Set admin directory.
     *
     * @return void
     */
    protected function setDirectory()
    {
        $this->directory = config('admin.directory');
    }

    /**
     * Initialize the admin directory.
     *
     * @return void
     */
    protected function initAdminDirectory()
    {
        $this->setDirectory();

        if (is_dir($this->directory)) {
            $this->warn("{$this->directory} directory already exists !");

            return;
        }

        $this->makeDir('/');
        $this->line('<info>Admin directory was created:</info> '.str_replace(base_path(), '', $this->directory));

        $this->makeDir('Controllers');
        $this->makeDir('Metrics/Examples');

        $this->createHomeController();
        $this->createAuthController();
        $this->createMetricCards();

        $this->createBootstrapFile();
        $this->createRoutesFile();
    }

    /**
     * Create HomeController.
     *
     * @return void
     */
    public function createHomeController()
    {
        $homeController = $this->directory.'/Controllers/HomeController.php';
        $contents = $this->getStub('HomeController');

        $this->laravel['files']->put(
            $homeController,
            str_replace(
                ['DummyNamespace', 'MetricsNamespace'],
                [$this->namespace('Controllers'), $this->namespace('Metrics\\Examples')],
                $contents
            )
        );
        $this->line('<info>HomeController file was created:</info> '.str_replace(base_path(), '', $homeController));
    }

    /**
     * Create AuthController.
     *
     * @return void
     */
    public function createAuthController()
    {
        $authController = $this->directory.'/Controllers/AuthController.php';
        $contents = $this->getStub('AuthController');

        $this->laravel['files']->put(
            $authController,
            str_replace(
                ['DummyNamespace'],
                [$this->namespace('Controllers')],
                $contents
            )
        );
        $this->line('<info>AuthController file was created:</info> '.str_replace(base_path(), '', $authController));
    }

    /**
     * @return void
     */
    public function createMetricCards()
    {
        $map = [
            '/Metrics/Examples/NewUsers.php'      => 'metrics/NewUsers',
            '/Metrics/Examples/NewDevices.php'    => 'metrics/NewDevices',
            '/Metrics/Examples/ProductOrders.php' => 'metrics/ProductOrders',
            '/Metrics/Examples/Sessions.php'      => 'metrics/Sessions',
            '/Metrics/Examples/Tickets.php'       => 'metrics/Tickets',
            '/Metrics/Examples/TotalUsers.php'    => 'metrics/TotalUsers',
        ];

        $namespace = $this->namespace('Metrics\\Examples');

        foreach ($map as $path => $stub) {
            $this->laravel['files']->put(
                $this->directory.$path,
                str_replace(
                    'DummyNamespace',
                    $namespace,
                    $this->getStub($stub)
                )
            );
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function namespace($name = null)
    {
        $base = str_replace('\\Controllers', '\\', config('admin.route.namespace'));

        return trim($base, '\\').($name ? "\\{$name}" : '');
    }

    /**
     * Create routes file.
     *
     * @return void
     */
    protected function createBootstrapFile()
    {
        $file = $this->directory.'/bootstrap.php';

        $contents = $this->getStub('bootstrap');
        $this->laravel['files']->put($file, $contents);
        $this->line('<info>Bootstrap file was created:</info> '.str_replace(base_path(), '', $file));
    }

    /**
     * Create routes file.
     *
     * @return void
     */
    protected function createRoutesFile()
    {
        $file = $this->directory.'/routes.php';

        $contents = $this->getStub('routes');
        $this->laravel['files']->put($file, str_replace('DummyNamespace', $this->namespace('Controllers'), $contents));
        $this->line('<info>Routes file was created:</info> '.str_replace(base_path(), '', $file));
    }

    /**
     * Get stub contents.
     *
     * @param $name
     *
     * @return string
     */
    protected function getStub($name)
    {
        return $this->laravel['files']->get(__DIR__."/stubs/$name.stub");
    }

    /**
     * Make new directory.
     *
     * @param string $path
     */
    protected function makeDir($path = '')
    {
        $this->laravel['files']->makeDirectory("{$this->directory}/$path", 0755, true, true);
    }
}
