<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/14
 * Time: 下午3:49
 */

namespace app\api\controller\v1;


use app\lib\exception\OrderException;
use think\Loader;
use app\api\service\Token as tokenService;
use app\api\service\Order as orderService;
use app\api\model\Order as orderModel;

class Order extends BaseController
{
    // 用户在选择商品后，向api提交包含所选择的商品信息
    // 服务器接受到信息后，验证商品信息的库存量是否够
    // 有库存，把订单入库，也就是下单成功，返回客户端消息，告诉客户端可以支付了
    // 调用我们的支付接口， 进行支付
    // 还需要再次进行库存量的检测(此次检查是因为下单是下单，支付是支付，支付是可以有时间限制的，譬如三十分之内，这样就不能保证用户支付的时候所购买的产品还有库存)
    // 服务器就可以调用微信的支付接口
    // 微信会返回给我们一个支付的结果(异步)
    // 成功：也需要进行库存量的检测
    // 成功: 进行库存量的扣除

    // 创建订单的前置方法，检测权限
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getSummaryByUser, getDetail']
    ];

    /**
     * 创建订单
     * @return array
     */
    public function placeOrder()
    {
        Loader::validate('OrderPlace')->goCheck();
        //todo 获取Post 过来的数据， 注意 如果参数是数组的话 需要在助手函数中 + /a 才能获取到数组参数
        $products = input('post.products/a');
        $uid = tokenService::getCurrentUid();
        $order = new orderService();
        // 下单的核心方法
        $status = $order->place($products, $uid);
        return $status;
    }

    /**
     * 获取历史订单(简要信息)
     * @param int $page 页数
     * @param int $size 大小
     */
    public function getSummaryByUser($page = 1, $size = 15)
    {
        Loader::validate('PagingParameter')->goCheck();
        $uid = tokenService::getCurrentUid();
        $summary = orderModel::getSummaryByUser($uid, $page, $size);
        // 如果没有分页数据
        if  ($summary->isEmpty()){
            return [
                'page_current' => $summary->getCurrentPage(),
                'data' => []
            ];
        }
        // 查询到分页数据
        return [
            'page_current' => $summary->getCurrentPage(),
            'data' => $summary->hidden(['prepay_id', 'snap_items', 'snap_address'])
        ];
    }

    /**
     * 获取订单的详细信息
     * @param $orderId
     */
    public function getDetail($id)
    {
        Loader::validate('IDMustBePostiveInt')->goCheck();
        $orderDetail = orderModel::get($id);
        if (!$orderDetail){
            throw new  OrderException();
        }
        return $orderDetail->hidden(['prepay_id']);
    }
}