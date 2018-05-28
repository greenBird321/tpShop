<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/11
 * Time: 下午5:09
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code = 404;
    public $msg = '查询的用户不存在';
    public $errCode = '60000';
}