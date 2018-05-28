<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

// Route::rule('路由表达式', '路由地址', '请求类型', '路由参数(数组)', '变量规则(数组)');

//请求类型 : GET / POST / DELETE / PUT / * 缺省的情况下是 *  任意请求类型

//此种写法只支持get
//Route::rule('hello', 'sample/Test/hello', 'GET');

//此种写法支持get 和 Post
//Route::rule('hello/:id', 'sample/Test/hello', 'GET|POST');

//简化写法
//Route::get('hello', 'sample/Test/hello');
//Route::post();

// 获取指定ID的 Banner
Route::get('api/:version/banner/:id', "api/:version.Banner/getBanner");

// 获取所有的主题
/**
Route::get('api/:version/theme', 'api/:version.Theme/getSimpleList');
Route::get('api/:version/theme/:id', 'api/:version.Theme/getComplexOne');
*/

/**
 * 主题的路由组
 */
Route::group('api/:version/theme', function (){
    Route::get('/', 'api/:version.Theme/getSimpleList');
    Route::get('/:id', 'api/:version.Theme/getComplexOne');
});
/*
// 通过产品ID获取 产品的相信信息
Route::get('api/:version/product/:id', 'api/:version.Product/getOne');
// 获取最近的产品
Route::get('api/:version/product/recent', 'api/:version.Product/getRecent');
// 通过分类ID 获取 该分类下的产品
Route::get('api/:version/product/by_category', 'api/:version.Product/getAllInCategory');
*/

/**
 * 产品路由组 路由组的写法效率要比直接平铺的写法效率高
 */
Route::group('api/:version/product', function (){
    // 获取指定ID的主题 如果遇到 前面都是一样的 而参数不一样 可以用第四个参数来限定参数规则 从而让tp知道到底访问的那个路由
   Route::get('/:id', 'api/:version.Product/getOne', [], ['id' => '\d+']);
   Route::get('/recent', 'api/:version.Product/getRecent');
   Route::get('/by_category', 'api/:version.Product/getAllInCategory');
});

/**
 *  订单路由组
 */
//Route::group('api/:version/order', function (){
//    // 下单地址
//    Route::post('/', 'api/:version.Order/placeOrder');
//    // 查询历史订单信息 (简要)
//    Route::get('/by_user', 'api/:version.Order/getSummaryByUser');
//    // 查询历史订单信息 (详细)
//    Route::get('/:id', 'api/:version.Order/getDetail', [], ['id' => '\d+']);
//});

// 获取全部分类
Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories');

// 通过 code 获取 Token
Route::post('api/:version/token/user', 'api/:version.Token/getToken');

// 修改或者更新地址的接口
Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');

// 下单接口
Route::post('api/:version/order', 'api/:version.Order/placeOrder');

// 预处理订单接口
Route::post('api/:version/pre_order', 'api/:version.Pay/getPreOrder');

// 微信回调接口
Route::post('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');

// 获取历史订单
Route::get('api/:version/order/by_user','api/:version.Order/getSummaryByUser');

// 获取订单的详细信息
Route::get('api/:version/order/:id', 'api/:version.Order/getDetail', [], ['id' => '\d+']);

// Demo的测试地址
Route::get('verify', "api/v1.Demo/verify");
Route::get('validator', 'api/v1.Demo/validator');
Route::get('second', 'api/v1.Demo/second');
Route::get('third', 'api/v1.Address/third');