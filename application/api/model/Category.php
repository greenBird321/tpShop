<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/7
 * Time: 下午4:52
 */

namespace app\api\model;


class Category extends BaseModel
{
    // 隐藏一些不需要的属性
    protected $hidden = ['delete_time', 'topic_img_id', 'update_time'];

    /**
     *  设置 分类 与 image 的关联关系
     */
    public function img()
    {
        // model：被关联的模型  foreignKey: 此模型的关联外键 localkey： 被关联模型的外键
        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }
}