<?php
namespace App\Http\Controllers;

use App\Packages\Response\Error\Code;
use App\Packages\WeChat\OfficialAccount;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $officialAccount;

    public function __construct(OfficialAccount $officialAccount)
    {
        $this->officialAccount = $officialAccount;
    }

    /**
     * 微信网页授权获取code
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Laravel\Lumen\Http\Redirector
     */
    public function authorize(Request $request)
    {
        $url = $request->input('url', $request->url());
        $scope = $request->input('scope', 'snsapi_userinfo');

        $redirectUrl = $this->officialAccount->oauth2AuthorizeUrl($url, $scope);

        return redirect($redirectUrl);
    }

    /**
     * 授权登录的CODE获取ACCESS_TOKEN与用户openID
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accessToken(Request $request)
    {
        if (!$request->has('code'))
            return jsonResponse(Code::INVALID_PARAMETER);

        $result = $this->officialAccount->oauth2AccessTokenByCode($request->input('code'));

        if (!$result)
            return jsonResponse(Code::REMOTE);

        return jsonResponse(Code::SUCCESS, ['openID' => $result['openID']]);
    }

    /**
     * 授权后获得的用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorizeInfo(Request $request)
    {
        if (!$request->has('openID'))
            return jsonResponse(Code::INVALID_PARAMETER);

        $openID = $request->input('openID');

        $token = $this->officialAccount->oauth2AccessTokenByOpenID($openID);

        if (!$token)
            return jsonResponse(Code::REMOTE);

        $userInfo = $this->officialAccount->oauth2UserInfo($openID, $token);

        if (!$userInfo)
            return jsonResponse(Code::REMOTE);

        return jsonResponse(Code::SUCCESS, $userInfo);
    }

    /**
     * 获取公众号用户的用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request)
    {
        if (!$request->has('openID'))
            return jsonResponse(Code::INVALID_PARAMETER);

        $userInfo = $this->officialAccount->userInfo($request->input('openID'));

        if (!$userInfo)
            return jsonResponse(Code::REMOTE);

        return jsonResponse(Code::SUCCESS, $userInfo);
    }
}