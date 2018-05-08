<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/24
 * Time: 下午2:54
 */

namespace app\lib\exception;


class ThemeMissException extends BaseException
{
    public $code = 404;
    public $msg = 'request themeId not exist';
    public $errCode = 30000;
}