<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/14
 * Time: 下午3:13
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg = '没有权限访问';
    public $errCode = 10001;
}