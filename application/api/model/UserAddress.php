<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/14
 * Time: 上午11:00
 */

namespace app\api\model;


class UserAddress extends BaseModel
{
    protected $hidden = ['id', 'user_id', 'delete_time', 'update_time'];
}