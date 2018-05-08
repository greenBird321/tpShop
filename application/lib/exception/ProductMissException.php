<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/26
 * Time: 下午2:39
 */

namespace app\lib\exception;


class ProductMissException extends BaseException
{
    public $code = 400;
    public $message = 'request product not exist';
    public $errCode = 20000;
}