<?php
namespace App\Packages\Token;


use function foo\func;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;

class TokenServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->configure('token-default');

        $this->app->singleton(Token::class, function ($app) {
            $token = new Token();
            return $token->setRefreshTokenExpire($app->config->get('token-default.refresh_expire_in'))
                ->setTokenExpire($app->config->get('token-default.expire_in'))
                ->setSecret($app->config->get('token-default.secret'));
        });
    }
}