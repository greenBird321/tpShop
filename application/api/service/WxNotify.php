<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/22
 * Time: 下午2:25
 */

namespace app\api\service;

use think\Db;
use think\Exception;
use think\Loader;
use app\api\model\Order as orderModel;
use app\api\service\Order as orderSerice;
use app\lib\enum\OrderStatusEnum;
use app\api\model\Product as productModel;
use think\Log;

Loader::import('WxPay.Wxpay', EXTEND_PATH, 'Api.php');
class WxNotify extends \WxPayNotify
{
    /**
     * 重写父类的回调入口方法
     * @param array $data 回调解释出的参数
     * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
     * Return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    public function NotifyProcess($data, &$msg)
    {
        if ($data['result_code'] == 'SUCCESS') {
            $trade_no = $data['out_trade_no'];
            // 使用事务，防止高并发 多次减少库存
            Db::startTrans();
            try{
                $order = orderModel::where('order_no', '=', $trade_no)->find();
                // 只处理未付款订单
                if ($order->status == 1){
                    $orderService = new orderSerice();
                    $stockStatus = $orderService->checkOrderStock($order->id);
                    if ($stockStatus['pass']){
                        // 修改订单状态 有货的情况
                        $this->updateOrderStatus($order->id);
                        // 减少库存量
                        $this->reduceStock($stockStatus);
                    }else{
                        // 无货的情况 自动退款
                        $this->updateOrderStatus($order->id, false);
                    }
                    Db::commit();
                    return true;
                }
            }catch (Exception $ex){
                Log::error($ex);
                return false;
            }
        }else{
            Db::rollback();
            // 如果 微信支付出错，返回true 即可 是否让微信继续发送异步回调
            Log::error($data);
            return true;
        }
    }

    /**
     * 更新订单信息
     * @param $orderId 订单ID
     * @param $success 库存检测是否通过
     */
    private function updateOrderStatus($orderId, $success = true)
    {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        orderModel::where('id', '=', $orderId)->update(['status' => $status]);
    }

    /**
     * 减少产品库存量
     * @param $stockStatus 库存状态
     */
    private function reduceStock($stockStatus)
    {
        foreach ($stockStatus['$stockStatus'] as $singlePstatus){
            // 使用TP5 中的模型进行减法 并且 框架会自动更新此字段
            productModel::where('id', '=', $singlePstatus['id'])->setDec('stock', $singlePstatus['count']);
        }
    }
}