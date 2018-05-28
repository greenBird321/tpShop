<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/10
 * Time: 下午5:40
 */

namespace app\api\model;


class ProductProperty extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'product_id', 'id'];

}