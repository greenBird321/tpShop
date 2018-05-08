<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/17
 * Time: 下午4:56
 */

namespace app\api\validate;



class IDMustBePostiveInt extends BaseValidate
{

    protected $rule = [
        'id' => 'require|isPostiveInteger'
    ];

    // 自定义错误信息
    protected $message =[
        'id' => 'id参数必须是正整数'
    ];
}