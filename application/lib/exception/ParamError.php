<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/20
 * Time: 上午10:34
 */

namespace app\lib\exception;


class ParamError extends BaseException
{
    public $code = 400;
    public $msg = 'Param error';
    public $errCode = 10000;
}