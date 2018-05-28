<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/11
 * Time: 下午5:40
 */

namespace app\lib\exception;


class SuccessException extends BaseException
{
    public $code = 201;
    public $msg = 'ok';
    public $errCode = 0;
}