<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/18
 * Time: 下午2:52
 */

namespace app\lib\exception;

/**
 * Class BannerMissException
 * 请求不存在的banner的状态码、返回给客户端的信息、网络状态码
 */
class BannerMissException extends BaseException
{
    public $code = 404;

    public $msg = 'request banner not exist';

    public $errCode = 40000;
}