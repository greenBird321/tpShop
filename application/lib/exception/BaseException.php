<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/18
 * Time: 下午2:51
 */

namespace app\lib\exception;

use think\Exception;

/**
 * Class BaseException
 *  定义错误码、消息、以及错误的网络状态码
 */
class BaseException extends Exception
{
    // 错误的网络状态码 默认是参数错误
    public $code = 400;

    // 错误信息
    public $msg = 'request param error';

    // 错误的状态码
    public $errCode = 10000;

    public function __construct($params = [])
    {
        if (!is_array($params))
        {
            return;
        }

        array_key_exists('code', $params) == true ? $this->code = $params['code'] : $this->code;
        array_key_exists('msg', $params) == true ? $this->msg = $params['msg'] : $this->msg;
        array_key_exists('errCode', $params) == true ? $this->errCode = $params['errCode'] : $this->errCode;
    }
}