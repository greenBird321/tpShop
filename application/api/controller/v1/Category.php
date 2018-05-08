<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/7
 * Time: 下午4:51
 */

namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;
class Category
{
    public function getAllCategories()
    {
        // all([], '关联方法')  如果[] 为空的话 就是 说明全部查询
        // 等同于 with('img')->select()
        $categories = CategoryModel::all([], 'img');
        if ($categories->isEmpty()){
            throw new CategoryException();
        }
        return $categories;
    }
}