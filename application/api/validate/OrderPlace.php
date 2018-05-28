<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/14
 * Time: 下午6:09
 */

namespace app\api\validate;


use app\lib\exception\ProductMissException;

class OrderPlace extends BaseValidate
{
    /**
     *  数据结构
     * @var array
     *  public $products = [
     * [
     * 'product_id' => 1,
     * 'count' => 3
     * ],
     * [
     * 'product_id' => 1,
     * 'count' => 3
     * ],
     * [
     * 'product_id' => 1,
     * 'count' => 3
     * ]
     * ];
     */


    protected $rule = [
        'products' => 'require|checkProducts'
    ];

    // products 子项的 验证机制 需要手动调用
    protected $singRule = [
        'product_id' => 'require|isPostiveInteger',
        'count'      => 'require|isPostiveInteger'
    ];

    protected function checkProducts($values)
    {
        if (!is_array($values)) {
            throw new ProductMissException([
                'msg' => '商品参数结构不正确'
            ]);
        }

        if (empty($values)) {
            throw new ProductMissException([
                'msg' => '商品列表不能为空'
            ]);
        }
        foreach ($values as $value) {
            $this->checkProduct($value);
        }
        return true;
    }

    // 复用BaseValidate 中的 isPostiveInteger 验证方法
    protected function checkProduct($value)
    {
        // 手动调用验证器 因为singRule 定义的 这几个规则都实现在BaseValidate中
        $validate = new BaseValidate($this->singRule);
        $result = $validate->check($value);
        if (!$result){
            throw new ProductMissException([
                'msg' => '商品子项的数据结构不正确'
            ]);
        }
    }
}