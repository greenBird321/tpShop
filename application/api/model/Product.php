<?php

namespace app\api\model;

use think\Db;

class Product extends BaseModel
{
    protected $hidden = ['from', 'create_time', 'update_time', 'pivot', 'category_id', 'delete_time'];
    // 模型的读取器 驼峰命名
    public function getMainImgUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }

    public static function getMostRecent($count)
    {
        $product = self::order('create_time desc')->limit($count)->select();
        return $product;
    }

    public static function getProductsByCategory($categoryId)
    {
        $products = self::where('category_id', '=', $categoryId)->select();
        return $products;
    }

}
