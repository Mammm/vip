<?php
const ROOT = __DIR__;
const DOMAIN = 'http://local.com';

require_once 'Container.php';
require_once 'Helper.php';
require_once 'DB.php';
require_once 'WeChat/WeChat.php';
require_once 'Response/Error/Code.php';
require_once 'Response/Error/Message.php';

$app = new Container();

$app->bind('db', function () {
    return new DB();
});

$app->bind('redis', function () {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    return $redis->auth('123456');
});

$app->bind('wechat', function () {
   return new WeChat();
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

