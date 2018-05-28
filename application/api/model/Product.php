<?php

namespace app\api\model;

class Product extends BaseModel
{
    protected $hidden = ['from', 'create_time', 'update_time', 'pivot', 'category_id', 'delete_time'];
    // 模型的读取器 驼峰命名
    public function getMainImgUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }


    // 1 对 多关联

    public function imgs()
    {
        // hasMany('关联模型名','外键名','主键名',['模型别名定义']);
        return $this->hasMany('ProductImage', 'product_id', 'id');
    }

    // 1 对 多 关联
    public function properties()
    {
        return $this->hasMany('ProductProperty', 'product_id', 'id');
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

    public static function getProductByProductId($productId)
    {
        // with(['imgs.imgUrl, properties']) 也是可以的，2种写法
        // 解决关联表的复杂排序 把一个复杂查询分解成多个简单查询 使用闭包方式解决 链式查询 每一链返回的都是一个Query对象 并会把Query对象传递到闭包的参数中，从而使用$query参数
        $product = self::with([
            'imgs' => function($query){
                $query->with('imgUrl')->order('order', 'asc');
            }
        ])->with(['properties'])->find($productId);
        return $product;
    }
}
