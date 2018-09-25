<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/25
 * Time: 17:18
 */

namespace App\Packages\WeChat;



use Illuminate\Support\ServiceProvider;

class WeChatServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->configure('wechat-connect');

        $this->app->singleton('WeChatOA', function ($app) {
            $weChat = new OfficialAccounts();

            return $weChat->setAppID($app->config->get('wechat-connect.OA.appid'))
                ->setAppSecret($app->config->get('wechat-connect.OA.secret'));
        });
    }

}