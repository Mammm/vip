<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

Route::group(['prefix' => 'api', 'namespace' => 'Api'], function(){
    Route::any('router', 'RouterController@index');  // API 入口
});
$router->get('test/set', 'TestController@index');
$router->get('test/cache', 'TestController@getRedisCache');

$router->group(['namespace' => 'VIP'], function () use ($router) {

});

$router->group([
    'namespace' => 'WeChat',
    'prefix' => 'we-chat'
], function () use ($route) {

    $route->get('user/authorize', 'UserController@authorize');
    $route->post('user/access-token', 'UserController@accessToken');
    $route->get('user/authorize-info', 'UserController@authorizeInfo');
    $route->get('user/info', 'UserController@info');

});