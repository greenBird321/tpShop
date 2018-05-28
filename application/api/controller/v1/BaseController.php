<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/14
 * Time: 下午5:07
 */

namespace app\api\controller\v1;


use think\Controller;
use app\api\service\Token as TokenService;

class BaseController extends Controller
{
    public function checkPrimaryScope()
    {
        TokenService::needPrimaryScope();
    }

    public function checkExclusiveScope()
    {
        TokenService::needExclusiveScope();
    }
}