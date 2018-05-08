<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/26
 * Time: 下午4:25
 */

namespace app\api\controller\v1;


use app\lib\exception\ProductMissException;
use app\lib\exception\CategoryProductException;
use think\Loader;
use app\api\model\Product as productModel;
class Product
{
    /***
     * 最近的产品列表
     * @param int $count 默认显示最近商品的条数
     * @return mixed 详细的产品信息
     * @throws ProductMissException
     */
    public function getRecent($count = 20)
    {
        Loader::validate('Count')->goCheck();
        $products = productModel::getMostRecent($count);
        if (!$products){
            throw new ProductMissException();
        }
        // 调用 collection类 的 hidden 方法，使从数据库中查询出来的数组临时隐藏一些属性
        return $products->hidden(['summary']);;
    }

    /***
     * 通过分类ID 查找该分类下的产品
     * @param $id 分类ID
     * @return false|\PDOStatement|string|\think\Collection
     * @throws CategoryProductException
     */
    public function getAllInCategory($id)
    {
        Loader::validate('IDMustBePostiveInt')->goCheck();
        $products = productModel::getProductsByCategory($id);
        if ($products->isEmpty()){
            throw new CategoryProductException();
        }

        return $products->hidden(['summary', 'img_id']);
    }
}