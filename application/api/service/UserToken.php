<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/8
 * Time: 下午12:16
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\TonkenException;
use think\Exception;
use app\api\model\User as userModel;

class UserToken extends Token
{
    /**
     * 小程序的appId
     */
    protected $appId;

    /**
     * 小程序的app secret
     */
    protected $secret;

    protected $wxLoginUrl;
    protected $code;

    public function __construct($code)
    {
        $this->appId = config('wx.app_id');
        $this->secret = config('wx.app_secret');
        $this->code = $code;
        // sprintf 把格式化的字符串写入变量中
        $this->wxLoginUrl = sprintf(config('wx.login_url'), $this->appId, $this->secret, $this->code);
    }

    /**
     * 通过 code 获取 token 服务层
     * @param $code ospbl5YLyCHSx6vpnlpwDvQIDglA 5分钟失效
     */
    public function getUserToken()
    {
        $result = curl_get($this->wxLoginUrl);
        $result = json_decode($result, true);
        if (isset($result['errcode'])) {
            throw new Exception('登录请求验证换取token失败');
        }
        $openId = $result['openid'];
        // 查询库中是否含有此openID 如果有说明此用户之前已经登录过 没有则说明是新用户 需要入库
        $user = userModel::getUserByOpenId($openId);
        // 不存在 入库， 如果存在不处理 返回 uid
        if ($user) {
            $uid = $user->id;
        } else {
            $newUser = userModel::create([
                'openid' => $openId,
                'create_time' => time()
            ]);
            $uid = $newUser->id;
        }
        // 做缓存 用户可以使用缓存找到一些有用的信息， key: token value: wx.result uid rid
        $cacheValue = $this->prepareCacheValue($result, $uid);
        $token = $this->saveToCache($cacheValue);
        return $token;
    }

    /**
     * 准备 缓存数据 数据的组装
     * @param $wxReult
     * @param $uid
     * @return mixed
     */
    private function prepareCacheValue($wxReult, $uid)
    {
        $cacheValue = $wxReult;
        $cacheValue['uid'] = $uid;
        // 可以理解为 role_id
        // 类似其他语言中的枚举
        $cacheValue['rid'] = ScopeEnum::User;
        return $cacheValue;
    }

    /**
     * 保存缓存数据到缓存
     * @param $cacheValue
     */
    private function saveToCache($cacheValue)
    {
        $key = self::generateToken();
        // 存入缓存必须是字符串
        $value = json_encode($cacheValue);
        $expire_in = config('setting.token_expire_in');

        //写入缓存 tp5默认使用是文件缓存 可用redis 或者 其他数据库
        $requst = cache($key, $value, $expire_in);
        if (!$requst) {
            throw new TonkenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 100005
            ]);
        }
        return $key;
    }
}