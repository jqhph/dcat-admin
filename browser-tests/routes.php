<?php

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => 'Tests\Controllers',
    'middleware' => ['web', 'admin'],
], function ($router) {
    $router->resource('tests/users', UserController::class);
    $router->resource('tests/report', ReportController::class);
});
