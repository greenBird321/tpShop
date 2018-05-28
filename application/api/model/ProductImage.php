<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/10
 * Time: 下午5:38
 */

namespace app\api\model;


class ProductImage extends  BaseModel
{
    protected $hidden = ['img_id','delete_time','product_id'];

    // 还需定义 productImage 与 image 的关系
    public function imgUrl()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}