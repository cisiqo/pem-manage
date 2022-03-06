<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    Route::get('/', function () {
        return redirect('admin/pem');
    });

    $router->resource('pem', PemController::class);
    $router->resource('group', GroupController::class);
    
    $router->any('pem/uploadfile', 'FileController@handle');
    $router->get('pem/{id}/download', 'PemController@download')->name('pem.download');
});
