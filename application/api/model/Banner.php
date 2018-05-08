<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/18
 * Time: 下午2:33
 */

namespace app\api\model;


class Banner extends BaseModel
{
    // 设置过滤字段
    protected $hidden = ['delete_time' , 'update_time'];

    /**
     *  关联方法， 并不是普通的方法
     */
    public function items()
    {
        // 1对多的 关系模型方法 hasMany
        return $this->hasMany('BannerItem', 'banner_id', 'id');
    }

    /**
     * 通过BannerId 查询 Banner的具体信息
     * @param $id Banner的ID
     */
    static public function getBannerById($id)
    {
        // 嵌套载入  可以理解为 Banner 下必须有 items 这个方法， items 关联的 模型下 有 img 方法 (必须掌握 表的关联关系 是一对一 还是一对多 还是 多对多)
        // Db:select(),get() model: find(),all() model可以使用Db的方法 而Db不能使用 model的方法
        $banner = self::with(['items', 'items.img'])->find($id);
        return $banner;

        //TODO TP5 数据库操作的第一种方法 直接使用sql
//        $result = Db::query('SELECT * FROM `banner_item` WHERE banner_id = ?', [$id]);
//        return $result;

        // TODO TP5 数据库操作的第二种方法 是使用TP5中的查询构造器 注意 不同的链式方法是没有先后顺序的 相同的链式方法是有先后顺序的 可能会影响查询结果
//        $result = Db::table('banner_item')->where('banner_id', '=', $id)->select();
//        return $result;

        // TODO WHERE方法的3中调用形式，表达式、数组(不推荐)、闭包
//        $result = Db::table('banner_item')
//            ->where(function ($query) use ($id){
//                $query->where('banner_id', '=' , $id);
//        })
//            ->select();
//        return $result;
    }
}