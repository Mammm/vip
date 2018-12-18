<?php
//看看情况
const ROOT = __DIR__;
const DOMAIN = 'http://vip.com';

require_once 'Container.php';
require_once 'Helper.php';
require_once 'DB.php';
require_once 'WeChat/WeChat.php';
require_once 'Response/Error/Code.php';
require_once 'Response/Error/Message.php';

$app = new Container();

$app->bind('db', function () {
    $config = [
        'host' => '',
        'dbName' => '',
        'username' => '',
        'password' => ''
    ];

    return new DB($config);
});

$app->bind('redis', function () {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->auth('123456');
    return $redis;
});

$app->bind('wechat', function () {
   $wechat = new WeChat();
   return $wechat->setAppID('wxfc0d50aec0853020')
       ->setAppSecret('7393999030dc931e3ca8bcd961b38c42');
});

if (!paramHas($_REQUEST, ['C', 'F']))
    return jsonResponse(Code::INVALID_PARAMETER);

$controller = $_REQUEST['C'].'Controller';
$controllerFile = 'Controllers/'.$controller.'.php';

if (!file_exists($controllerFile))
    return jsonResponse(Code::INVALID_PARAMETER);

require_once $controllerFile;
$controller = new $controller;

if (!method_exists($controller, $_REQUEST['F']))
    return jsonResponse(Code::INVALID_PARAMETER);


return call_user_func([$controller, $_REQUEST['F']], $_REQUEST);

