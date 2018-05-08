<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/26
 * Time: 下午4:26
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule = [
        'count' => 'isPostiveInteger|between:1,20'
    ];
}