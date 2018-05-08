<?php

namespace app\api\model;

class BannerItem extends BaseModel
{
    protected $table = 'banner_item';
    // 设置过滤字段
    protected $hidden = ['id', 'img_id', 'type', 'delete_time', 'banner_id' , 'update_time'];

    public function img()
    {
        // model: 模型的名字 foreignKey: 模型对应的表的外键 localKey: 本模型的主键
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}
