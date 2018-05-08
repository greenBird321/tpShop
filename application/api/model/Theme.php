<?php

namespace app\api\model;

class Theme extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'topic_img_id' , 'head_img_id'];

    public function topicImg()
    {
//      一对一  外键写在那张表里，那张表里就定义成$this->belongsTo(), 反之则使用 $this->hasOne();
        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }

    public function headImg()
    {
        return $this->belongsTo('Image', 'head_img_id', 'id');
    }

    public function product()
    {
        // belongsToMany('关联模型名','中间表名','外键名','当前模型关联键名',['模型别名定义']);
        // 需要关联的模型名称， 关系表名， 关系表对于此表来说的外建名（跟此模型没有关系的外键字段），关系表对于此表来说的主键名(跟此模型有关系的外键字段)
        return $this->belongsToMany('Product', 'theme_product', 'product_id', 'theme_id');
    }

    static public function getThemeById($id = [])
    {
        $themes = self::with('topicImg,headImg')->select($id);
        return $themes;
    }

    static public function getProductByThemeId($themeId)
    {
        $product = self::with('product,headImg,topicImg')->find($themeId);
        return $product;
    }
}
