<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/7
 * Time: 下午5:31
 */

namespace app\lib\exception;


class CategoryException extends BaseException
{
    public $code = '404';
    public $msg = '指定类目不存在，请检查';
    public $errCode = 20000;
}