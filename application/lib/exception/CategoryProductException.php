<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/7
 * Time: 下午6:29
 */

namespace app\lib\exception;


class CategoryProductException extends BaseException
{
    public $code = '404';
    public $msg = '该分类下没有产品，请检查分类ID';
    public $errCode = 20001;
}