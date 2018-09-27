<?php
namespace App\Http\Controllers\VIP\Session;

use App\Http\Controllers\Controller;
use App\Packages\Response\Error\Code;
use App\Packages\WeChat\OfficialAccount;
use Illuminate\Http\Request;

class OfficialAccountController extends Controller
{
    private $officialAccount;

    public function __construct(OfficialAccount $officialAccount)
    {
        $this->officialAccount = $officialAccount;
    }

    public function store(Request $request)
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


    }
}