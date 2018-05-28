<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/17
 * Time: 下午6:22
 */

namespace app\lib\enum;


class OrderStatusEnum
{
    /**
     *  订单未支付
     */
    const UNPAID = 1;
    /**
     * 订单已经支付
     */
    const PAID = 2;
    /**
     * 订单已发货
     */
    const DELIVERED = 3;
    /**
     * 已经付款但是库存不足
     */
    const PAID_BUT_OUT_OF = 4;
}