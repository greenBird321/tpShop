<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/17
 * Time: 下午6:28
 */

namespace app\api\validate;


use app\lib\exception\ParamError;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    public function goCheck()
    {
        $requestParam = Request::instance()->param();
        // 需要检测规则 在实例化validate时传入
        $result = $this->batch()->check($requestParam);
        if (!$result){
            // 使用oop思想
            $err = new ParamError([
                'msg' => $this->error
            ]);
            // 没有使用oop的思想
//            $err->msg = $this->error;
            throw $err;
        }
        return true;
    }

    protected function isPostiveInteger($value, $rule = '', $data = '' , $field = '', $desc = '')
    {
        if (is_numeric($value) && $value > 0 && is_int($value + 0)){
            return true;
        }else{
            return false;
        }
    }
}