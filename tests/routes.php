<?php

use Illuminate\Routing\Router;

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => 'Dcat\Admin\Tests\Controllers',
    'middleware' => ['web', 'admin'],
], function (Router $router) {
    $router->resource('tests/users', 'UserController');
    $router->resource('tests/report', 'ReportController');
});
