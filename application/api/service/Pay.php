<?php
/**
 * 用户客户端吊起微信支付，返回客户端需要的参数—
 * User: lihe
 * Date: 2018/5/17
 * Time: 下午4:15
 */

namespace app\api\service;


use app\api\model\Order as orderModel;
use app\api\service\Order as orderService;
use app\api\service\Token as tokenService;
use app\lib\exception\OrderException;
use app\lib\exception\TonkenException;
use think\Exception;
use think\Loader;
use think\Log;

// import(文件夹名称. 文件的名称(第一个小数点之前的名称), 存放第三方库的绝对路径, 除了第一个小数点之后的所有);
// extend/WxPay/WxPay.api.php
Loader::import('Wxpay.Wxpay', EXTEND_PATH, 'Api.php');

class Pay
{
    private $orderID;
    private $orderNo;

    public function __construct($orderId)
    {
        if (!$orderId) {
            throw new Exception('订单号不允许为null');
        }
        $this->orderID = $orderId;
    }

    /**
     * 支付核心方法
     */
    public function pay()
    {
        // 检测订单号 订单号有可能根本就不存在
        // 订单号确实存在，但是，订单号和当前用户是不匹配的
        // 检查订单状态，订单只有在没有支付的情况下，才可以支付
        // 检查库存
        // 检测订单的顺序原则，首先把发生几率最高的放在前面，可以节省资源，第二，对服务器和数据库性能消耗大的放在后面

        $this->checkOrderValidate();
        $order = new orderService();
        $orderStatus = $order->checkOrderStock($this->orderID);
        if (!$orderStatus['pass']) {
            return $orderStatus;
        }
        return $this->makeWxPreOrder($orderStatus['orderPrice']);
    }

    /**
     * 组装微信需要数据并向微信发送预订单请求
     */
    private function makeWxPreOrder($totalPrice)
    {
        $openId = tokenService::getCurrentTokenVar('openid');
        if (!$openId) {
            throw new TonkenException();
        }
        // 如果 引入的类中没有命名空间的话，new 的时候需要在 类名前添加 \
        $wxOrderData = new \WxPayUnifiedOrder();
        // 设置订单号
        $wxOrderData->SetOut_trade_no($this->orderNo);
        // 设置交易类型
        $wxOrderData->SetTrade_type('JSAPI');
        // 设置订单总金额
        $wxOrderData->SetTotal_fee($totalPrice * 100);
        // 商品描述
        $wxOrderData->SetBody('支付测试');
        // 设置 openId
        $wxOrderData->SetOpenid($openId);
        // 设置回调地址
        $wxOrderData->SetNotify_url('http://xxxx.com');
        return $this->getPaySignature($wxOrderData);
    }

    /**
     * 向微信服务器发送预订单处理请求
     * @param $wxOrderData
     * 微信返回预处理的数据结构
     * {
     *   "appid": "aaaaaaaa",
     *   "mch_id": "bbbbbbbb",
         "nonce_str": "cccccccc",
         "prepay_id": "ddddddddddd",
         "result_code": "SUCCESS",
         "return_code": "SUCCESS",
         "return_msg": "OK",
         "sign": "eeeeeeeeeee",
         "trade_type": "JSAPI"
       }
     */
    private function getPaySignature($wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrderData['return_code'] != 'SUCCESS' || $wxOrderData['result_code'] != 'SUCCESS') {
            Log::record($wxOrder, 'error');
            Log::record('获取预支付订单失败', 'error');
            throw new \WxPayException('微信支付失败');
        }
        // 更新order 的 prepay_id 字段 CMS用的上
        $this->recordPreOrder($wxOrder);

        return $this->getSign($wxOrder);
    }

    /**
     * 记录prepay_id 到 order 表
     * @param $wxOrder
     */
    private function recordPreOrder($wxOrder)
    {
        orderModel::where('id', '=', $this->orderID)->update(['prepay_id' => $wxOrder['prepay_id']]);
    }

    /**
     * 检测订单合法性
     */
    private function checkOrderValidate()
    {
        $order = orderModel::where('id', '=', $this->orderID)->find();
        if (!$order) {
            throw new OrderException();
        }

        if (!tokenService::isValidateOperate($order->user_id)) {
            throw new TonkenException([
                'msg' => '下单用户与登陆用户不匹配'
            ]);
        }

        if (!orderService::isOrderPay($order->status)) {
            throw new OrderException([
                'msg' => '订单已经支付过'
            ]);
        }

        $this->orderNo = $order->order_no;

        //订单通过验证
        return true;
    }

    /**
     * 生成小程序需要支付的参数及签名
     * @param $wxOrderData
     */
    private function getSign($wxOrderData)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp(strval(time()));
        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetSignType('md5');
        $jsApiPayData->SetPackage('prepay_id=' . $wxOrderData['prepay_id']);
        $sign = $jsApiPayData->MakeSign();
        // 获取WxPayJsApiPay()类 生成的原始数组
        $rawValue = $jsApiPayData->GetValues();
        $rawValue['paySign'] = $sign;
        // 删除不需要的数据
        unset($rawValue['app_id']);

        return $rawValue;
    }
}