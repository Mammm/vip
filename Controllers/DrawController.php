<?php

class DrawController
{
    private $wechat;
    private $db;

    const PACKAGE_DRAW = 'poster_13';

    private $luckyID = [
        1,
        3000
    ];

    public function __construct()
    {
        $this->wechat = app('wechat');
        $this->db = app('db');
    }

    public function user($request)
    {
        if (!paramHas($request, 'openid'))
            return jsonResponse(Code::INVALID_PARAMETER);
        $user = $this->findUser($request['openid']);

        if ($user)
            return jsonResponse(Code::SUCCESS, compact('user'));

        $token = $this->wechat->oauth2AccessTokenByOpenID($request['openid']);

        if (!$token)
            return jsonResponse(Code::REMOTE);

        $userInfo = $this->wechat->oauth2UserInfo($token['openID'], $token['token']);

        if (!$userInfo)
            return jsonResponse(Code::REMOTE);

        $user = $this->addUser($userInfo['openid'], $userInfo['nickname']);

        return jsonResponse(Code::SUCCESS, compact('user'));
    }

    public function downloadDraw($request)
    {
        if (!paramHas($request, ['draw_id', 'openid']))
            return jsonResponse(Code::INVALID_PARAMETER);

        if (!$user = $this->findUser($request['openid']))
            return jsonResponse(Code::USER_NOT_FOUND);

        if (!in_array($user['id'], $this->luckyID) && $request['draw_id'] == self::PACKAGE_DRAW)
            return jsonResponse(Code::INVALID_PARAMETER);

        $item = $this->findItem($request['draw_id'], $request['openid']);

        if ($item)
            return jsonResponse(Code::SUCCESS, ['draw' => $item['draw']]);

        $original = $this->drawOriginalPath($request['draw_id']);

        if (!$original)
            return jsonResponse(Code::INVALID_PARAMETER);

        $savePath = giveMeASavePath('jpg');

        if (!$this->makeDraw($original, $savePath, $user['nickname'], publicPath('SIMHEI.TTF')))
            return jsonResponse(Code::FAILED);

        $url = str_replace(ROOT, DOMAIN, $savePath);

        $item = $this->addItem($request['openid'], $request['draw_id'], addslashes($url));

        if (!$item)
            return jsonResponse(Code::FAILED);

        return jsonResponse(Code::SUCCESS, ['draw' => $item['draw']]);
    }

    private function findUser($openid)
    {
        return sqlFirst("select * from vip_h5_draw_user where openid = '{$openid}'");
    }

    private function addUser($openid, $nickname)
    {
        $this->db->query("insert into vip_h5_draw_user values (DEFAULT, '{$openid}', '{$nickname}', 
NOW(), NOW())");

        return $this->findUser($openid);
    }

    private function findItem($drawID, $openID)
    {
        return sqlFirst("select * from vip_h5_draw_item where draw_code = '{$drawID}' and openid = '{$openID}'");
    }

    private function drawOriginalPath($imageCode)
    {
        $imagePath = publicPath("Image/Draw/{$imageCode}.jpg");

        if (!file_exists($imagePath))
            return false;

        return $imagePath;
    }

    public function makeDraw($drawOriginal, $savePath, $nickname, $fontFile)
    {
        $copy = imagecreatefromjpeg($drawOriginal);

        $fontSize = 20;

        $x = (750 - 9 * strlen($nickname)) / 2;
        $y = 140;

        $color = imagecolorallocate($copy, 255, 0, 0);

        imagettftext($copy, $fontSize, 0, $x, $y, $color, $fontFile, $nickname);

        return imagejpeg($copy, $savePath);
    }

    private function addItem($openID, $drawId, $draw)
    {
        $this->db->query("insert into vip_h5_draw_item values (DEFAULT, '{$openID}', '{$drawId}', '{$draw}', 
NOW(), NOW())");

        return $this->findItem($drawId, $openID);
    }
}