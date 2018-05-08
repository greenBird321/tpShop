<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/17
 * Time: 下午3:17
 */

namespace app\api\controller\v1;

use app\api\model\Banner AS BannerModel;
use app\lib\exception\BannerMissException;
use think\Loader;

class Banner
{
    /**
     * @URL domain/api/v1/banner/:id
     * @id  指定是那个位置的banner
     * @http Get
     */
    public function getBanner($id)
    {
        // 拦截请求 验证参数
        Loader::validate('IDMustBePostiveInt')->goCheck();

        //  使用模型的好处 是在TP中为模型写了很多的方法，提高编码效率以及使代码更加美观
        $banner = BannerModel::getBannerById($id);
        // 隐藏不需要客户端知道的属性
//        $response = $banner->hidden(['delete_time', 'update_time']);

        // 给客户端只展示那些属性
//        $response = $banner->visible(['id','name', 'description', 'url']);
        if (!$banner){
            throw new BannerMissException();
        }
        return $banner;
    }
}