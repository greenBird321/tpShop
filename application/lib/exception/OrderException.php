<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/15
 * Time: 下午5:39
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    public $code = 404;
    public $msg = '订单不存在 请检查!';
    public $errCode = 80000;
}