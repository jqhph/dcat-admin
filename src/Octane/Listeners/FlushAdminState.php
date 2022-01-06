<?php

namespace Dcat\Admin\Octane\Listeners;

use Dcat\Admin\AdminServiceProvider;
use Illuminate\Container\Container;

class FlushAdminState
{
    protected $adminServices = [
        'admin.app',
        'admin.asset',
        'admin.color',
        'admin.sections',
        'admin.extend',
        'admin.extend.update',
        'admin.extend.version',
        'admin.navbar',
        'admin.menu',
        'admin.context',
        'admin.setting',
        'admin.web-uploader',
        'admin.translator',
    ];

    protected $app;

    public function __construct(Container $container)
    {
        $this->app = $container;
    }

    public function handle($event): void
    {
        $provider = new AdminServiceProvider($this->app);

        $this->forgetServiceInstances();

        $provider->registerServices();
        $provider->registerExtensions();
        $provider->boot();
    }

    protected function forgetServiceInstances()
    {
        foreach ($this->adminServices as $service) {
            $this->app->forgetInstance($service);
        }
    }
}
