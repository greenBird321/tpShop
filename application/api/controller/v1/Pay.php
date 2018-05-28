<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/17
 * Time: 下午3:46
 */

namespace app\api\controller\v1;


use app\api\service\WxNotify;
use think\Loader;
use app\api\service\Pay as payService;

class Pay extends BaseController
{
    // 权限检查 多个接口需要检查权限 ['only' => 'getPreOrder1, getPreOrder2, ...']
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];

    /**
     *  预订单处理接口
     */
    public function getPreOrder($orderId = '')
    {
        Loader::validate('IDMustBePostiveInt')->goCheck();
        $pay = new payService($orderId);
        return $pay->pay();
    }

    /**
     *  微信回调接口
     */
    public function receiveNotify()
    {
        // 检测库存量
        // 修改订单状态 并 删少库存
        // 如成功处理，返回微信成功处理的信息
        $notify = new WxNotify();
        // SDK 调用 NotifyProcess 方法(隐式调用)
        $notify->Handle();
    }
}