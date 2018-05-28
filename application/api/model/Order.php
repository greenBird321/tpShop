<?php 

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    // 可以把时间 交给 tp5 框架去处理 维护 必须使用 Model 操作才可以使用 下面的属性 也可以去配置文件 去设置全局的时间戳设置
    //protected $autoWriteTimestamp = true;

    // 读取器
    public function getSnapItemsAttr($value)
    {
        if (empty($value)){
            return null;
        }

        return json_decode($value);
    }

    public function getSnapAddressAttr($value)
    {
        if (empty($value)){
            return null;
        }

        return json_decode($value);
    }

    public static function getSummaryByUser($uid, $page = 1, $size = 15)
    {
        // paginate() 返回的不是数组 而返回的是paginate对象
        // 参数1: 每页的数据数量
        // 参数2：是否是简洁模式，简洁模式：不需要算出总的数据量。 传统模式：需要算出总数据量
        // 参数3：配置参数
        // paginate() 等同于 select find 方法 相当于去数据库去查询。
        $summary = self::where('user_id', '=', $uid)->order('create_time desc')->paginate($size, true, ['page' => $page]);
        return $summary;
    }
}