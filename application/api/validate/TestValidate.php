<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/17
 * Time: 下午4:13
 */

namespace app\api\validate;


use think\Validate;

/**
 * Class TestValidate
 * 验证器
 */
class TestValidate extends Validate
{
    protected $rule = [
        'name' => 'require|max:10',
        'email' => 'email',
        'id' => 'number|egt:0'
    ];
}