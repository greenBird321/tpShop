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
Route::get('api/:version/theme', 'api/:version.Theme/getSimpleList');
// 获取指定ID的主题
Route::get('api/:version/theme/:id', 'api/:version.Theme/getComplexOne');
// 获取最近的产品
Route::get('api/:version/product/recent', 'api/:version.Product/getRecent');
// 获取全部分类
Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories');
// 通过分类ID 获取 该分类下的产品
Route::get('api/:version/product/by_category', 'api/:version.Product/getAllInCategory');

// Demo的测试地址
Route::get('verify', "api/v1.Demo/verify");
Route::get('validator', 'api/v1.Demo/validator');