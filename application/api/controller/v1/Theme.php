<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/24
 * Time: 下午2:29
 */

namespace app\api\controller\v1;

use app\lib\exception\ThemeMissException;
use think\Loader;
use app\api\model\Theme as themeModel;
class Theme
{
    /**
     * @url /theme?ids=id1,id2,id3,...
     * @return 一组theme模型
     */
    public function getSimpleList($ids='')
    {
        Loader::validate('IDSCollection')->goCheck();
        $ids = explode(',' , $ids);
        $result = themeModel::getThemeById($ids);
        if ($result->isEmpty()){
            throw new ThemeMissException();
        }
        return $result;
    }

    /**
     * @url /theme/:id
     * @param $id
     * @throws ThemeMissException
     */
    public function getComplexOne($id)
    {
        Loader::validate('IDMustBePostiveInt')->goCheck();
        $product = themeModel::getProductByThemeId($id);
        if (!$product){
            throw new ThemeMissException();
        }
        return $product;
    }
}