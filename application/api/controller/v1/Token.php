<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/8
 * Time: 上午11:51
 */

namespace app\api\controller\v1;


use app\api\service\UserToken;
use think\Loader;

class Token
{
    public function getToken($code = '')
    {
        Loader::validate('TokenGet')->goCheck();
        $userToken = new UserToken($code);
        $token = $userToken->getUserToken();
        return [
            'token' => $token
        ];
    }

}