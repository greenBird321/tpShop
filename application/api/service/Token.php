<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/8
 * Time: 下午4:35
 */

namespace app\api\service;

use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TonkenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    /**
     * 生成Token的方法
     * @return string
     */
    public static function generateToken()
    {
        $randChars = getRandChar(32);
        $token = md5($randChars . time() . config('secure.token_salt'));
        return $token;
    }

    /**
     * 从缓存中获取wx.result rid uid
     * @param $key
     * @return mixed
     * @throws TonkenException
     */
    public static function getCurrentTokenVar($key)
    {
        // 限定只能在POST头中传递token
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        // 从缓存中拿取token对应的value
        if (!$vars) {
            throw new TonkenException();
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
            if (array_key_exists($key, $vars)) {
                return $vars[$key];
            } else {
                throw new Exception('尝试获取的token变量并不存在');
            }
        }
    }

    /**
     * 获取当前uid
     * @return mixed
     */
    public static function getCurrentUid()
    {
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    /**
     * 检测用户权限 用户与后台管理员都可以访问的权限
     * @return bool
     * @throws ForbiddenException
     * @throws TonkenException
     */
    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('rid');
        if ($scope) {
            if ($scope >= ScopeEnum::User) {
                return true;
            }
            throw new ForbiddenException();
        } else {
            throw new TonkenException();
        }
    }

    /**
     * 检测用户权限 只能是用户访问的权限
     * @return bool
     */
    public static function needExclusiveScope()
    {
        $scope = self::getCurrentTokenVar('rid');
        if ($scope == ScopeEnum::User) {
            return true;
        }
        return false;
    }

    /**
     * 检查UID 是否 合法
     * @param $checkUid
     * @throws Exception
     * @throws TonkenException
     */
    public static function isValidateOperate($checkUid)
    {
        if (empty($checkUid)){
            throw new Exception('检查传入的UID,UID值错误');
        }
        if ($checkUid != self::getCurrentUid()){
            return false;
        }
        return true;
    }
}