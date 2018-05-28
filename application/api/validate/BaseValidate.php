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
        if (!$result) {
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

    /**
     * 验证参数是否是正整数
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @param string $desc
     * @return bool
     */
    protected function isPostiveInteger($value, $rule = '', $data = '', $field = '', $desc = '')
    {
        if (is_numeric($value) && $value > 0 && is_int($value + 0)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证参数是否为空
     * @param $value
     * @return bool
     */
    protected function isNotEmpty($value)
    {
        if (empty($value)) {
            return false;
        }
        return true;
    }

    /**
     * 验证参数是否是手机号
     * @param $value
     * @return bool
     */
    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * 参数验证，只取用定义规则的字段，其他字段不使用
     * @param $array
     * @return array
     * @throws ParamError
     */
    public function getDataByRule($array)
    {
        if (array_key_exists('user_id', $array) || array_key_exists('uid', $array)) {
            throw  new  ParamError([
                'msg' => '参数中包含非法的参数名user_id或者uid'
            ]);
        }
        $newArray = [];
        foreach ($this->rule as $key => $value) {
            $newArray[$key] = $array[$key];
        }
        return $newArray;
    }
}