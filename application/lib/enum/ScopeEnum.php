<?php
/**
 * 权限类，PHP中没有枚举类.
 * User: lihe
 * Date: 2018/5/14
 * Time: 下午12:05
 */

namespace app\lib\enum;


class ScopeEnum
{
    /**
     * 用户权限
     */
    const User = 16;

    /**
     * 管理员权限
     */
    const Super = 32;
}