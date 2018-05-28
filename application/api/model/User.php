<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/8
 * Time: 下午2:50
 */

namespace app\api\model;


class User extends BaseModel
{
    /**
     * 关联方法
     * @return \think\model\relation\HasOne
     */
    public function address()
    {
        // 关系方法 返回的都是模型的关系对象 object
        // user_id 外键不在User表中，所以1对1关系使用hasOne()方法定义
        return $this->hasOne('UserAddress', 'user_id', 'id');
    }

    /**
     * 通过openId 查看用户Id
     * @param $openId
     */
    public static function getUserByOpenId($openId)
    {
        $user = self::where('openid', '=', $openId)->find();
        return $user;
    }
}