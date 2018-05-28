<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/8
 * Time: 上午11:51
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
      'code' => 'require|isNotEmpty'
    ];

    protected $message = [
      'code' => 'code无效'
    ];
}