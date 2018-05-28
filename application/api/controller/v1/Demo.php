<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/17
 * Time: 下午3:41
 */

namespace app\api\controller\v1;

use app\api\validate\TestValidate;
use think\Controller;
use think\Validate;
class Demo extends Controller
{
    /**
     *  TP的独立验证
     */
    public function verify()
    {
        $data = [
            'name' => '11',
            'email' => 'test@qq.com'
        ];

        //编写验证规则 并实例化
        $verify = new Validate([
            //表示 name 字段是必填的，并且长度最大为10
            'name' => 'require|max:10',
            // 按照tp内部的eamil格式检测
            'email' => 'email'
        ]);

        // 开始批量检测数据 单调用check()只会验证1次，要想批量验证必须调用 batch()方法在进行验证
        $result = $verify->batch()->check($data);

        var_dump($verify->getError());
    }

    /**
     *  TP的验证器
     */
    public function validator()
    {
        $data = [
            'name' => 'test3333333',
            'email' => 'testqq.com'
        ];

        $validator = new TestValidate();

        $result = $validator->batch()->check($data);
        var_dump($validator->getError());
    }


    // 前置方法列表 在调用second方法之前要先执行first方法
    protected $beforeActionList = [
        'first' => ['only' => 'second']
    ];

    protected function  first(){
        echo "first\n";
    }

    public function second(){
        echo "second\n";
    }
}