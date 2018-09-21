<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/19
 * Time: 16:24
 */

namespace App\Packages\WeChat\API;


class URL
{
    const ACCESS_TOKEN = 'https://api.weixin.qq.com/cgi-bin/token';
    const OAUTH2_ACCESS_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    const OAUTH2_AUTHORIZE = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    const OAUTH2_REFRESH_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';
    const OAUTH2_USER_INFO =  'https://api.weixin.qq.com/sns/userinfo';
    const OA_USER_INFO = 'https://api.weixin.qq.com/cgi-bin/user/info';
}