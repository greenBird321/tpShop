<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/22
 * Time: 下午5:53
 */

namespace app\api\validate;


class PagingParameter extends BaseValidate
{
    protected $rule = [
        'page' => 'isPostiveInteger',
        'size' => 'isPostiveInteger'
    ];

    protected $message = [
        'page' => '分页参数必须是正整数',
        'size' => '分页参数必须是正整数'
    ];
}