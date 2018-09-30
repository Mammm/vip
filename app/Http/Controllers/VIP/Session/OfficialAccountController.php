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

    //想了下 现在其实不需要维护用户，因为每次H5都是单独的 如果记录下当做用户 每次微信登录难道都要更新用户的头像 名称？
    public function store(Request $request)
    {
    }
}