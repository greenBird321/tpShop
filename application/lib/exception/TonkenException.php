<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/8
 * Time: 下午5:07
 */

namespace app\lib\exception;


class TonkenException extends  BannerMissException
{
    public $code = 401;
    public $msg = 'Token过期或者无效Tonken';
    public $errCode = 100001;
}