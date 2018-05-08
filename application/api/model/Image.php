<?php

namespace app\api\model;

use think\Model;

class Image extends BaseModel
{
    // 设置过滤字段
    protected $hidden = ['id', 'from' , 'delete_time', 'update_time'];

    // TP5的读取器命名规则 get + '字段名称 首字母大写 驼峰命名' + Attr 可以接收两个参数 当前的 读取的字段 取值 和 当前所有字段
    // 小型 AOP 的应用
    public function getUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }
}
