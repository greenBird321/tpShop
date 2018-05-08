<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/24
 * Time: 下午5:46
 */

namespace app\api\validate;


class IDSCollection extends BaseValidate
{
    protected $rule = [
        'ids' => 'require|checkIDs'
    ];

    // 框架内要求的写法 变量名字必须是 $message
    protected $message = [
        'ids' => 'ids参数必须是以逗号分隔的多个正整数'
    ];

    // 包含了 判断参数 是否是正整数、是不是用 ',' 分割的字符串
    // value就是客户端传递过来的 ids = id1,id2,id3,...
    protected function checkIDs($value){
        $values = explode(',', $value);
        if (empty($values)){
            return false;
        }
        foreach ($values as $id){
            $result = $this->isPostiveInteger($id);
            if (!$result){
                return false;
            }
        }
        return true;
    }
}