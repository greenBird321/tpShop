<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/15
 * Time: 下午2:57
 */

namespace app\api\service;

// 对于复杂的逻辑 不适合直接写在controller中 或者 直接写在model中 需要多抽出一层service来保证 低耦合
use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Cache;
use app\api\service\RedisConfig as redisService;
use think\Db;
use think\Exception;
use app\api\model\Order as orderModel;
use app\lib\enum\OrderStatusEnum;

class Order
{
    // 客户端传递过来的 单个商品 包括 product_id 与 count
    protected $oProducts;
    // 从库中拿到的商品信息 包括 库存
    protected $products;

    protected $uid;

    /**
     * 下单接口
     * @param $oProducts
     */
    public function place($oProducts, $uid)
    {
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid = $uid;

        // 库存量检测
        $orderStatus = $this->getOrderStatus();
        if (!$orderStatus['pass']) {
            // 如果检测没通过， 则返回一个不正确的order_id 来保持数据的完整性
            $orderStatus['order_id'] = -1;
            return $orderStatus;
        }
        // 生成订单快照信息
        $orderSnap = $this->snapOrder($orderStatus);
        // 创建订单
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;
    }

    /**
     * 创建快照订单
     * @param $snap
     * @return array
     * @throws Exception
     */
    private function createOrder($snap)
    {
        // tp5 中 事务的使用
        // 开始事务
        Db::startTrans();
        try {
            $orderNo = $this->makeOrderNo();
            $order = new orderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_items = json_encode($snap['pStatus']);
            $order->snap_address = json_encode($snap['snapAddress']);
            // 另一种 创建记录的方式 save() 只能保存 一条数据 1维数组 TP5 1对多 建议 先保存1 然后在保存多
            $order->save();

            $orderId = $order->id;
            $create_time = $order->create_time;
            // 从客户端传递过来的数据 是数据结构为
            /**
             *  products: [
             *              "productid" : "1",
             *              "count" : 2
             *           ]
             * 只需要在客户端传递过来的数据上在加上order_id 即可插入 order_product表中
             */
            foreach ($this->oProducts as &$p) {
                $p['order_id'] = $orderId;
            }
            $orderProduct = new OrderProduct();
            // saveAll() 是保存一组数据， 2维数组
            $orderProduct->saveAll($this->oProducts);
            // 提交事务
            DB::commit();

            return [
                'order_id' => $orderId,
                'order_no' => $orderNo,
                'create_time' => $create_time
            ];
        } catch (Exception $ex) {
            // 回滚事务
            Db::rollback();
            throw $ex;
        }
    }

    /**
     * 生成订单号的方法 减少重复概率
     * 年 + 月 + 日 + 时间戳 + 毫秒时间戳 +  3位随机字符串
     */
    public function makeOrderNo()
    {
        $orderSn = intval(date('Y')) . strtoupper(dechex(date('m'))) . intval(date('d')) . substr(time(), -5) .
            substr(microtime(), 2, 5) . sprintf('%03d', rand(0, 999));
        return $orderSn;
    }

    /**
     * 生成订单快照 历史订单使用
     * @param $orderStatus
     */
    private function snapOrder($orderStatus)
    {
        $snap = [
            // 订单总价格
            'orderPrice' => 0,
            // 订单的商品总件数
            'totalCount' => 0,
            // 订单状态
            'pStatus' => [],
            // 订单地址
            'snapAddress' => null,
            // 商品名称
            'snapName' => '',
            // 商品缩略图
            'snapImg' => ''
        ];

        $result = $this->getUserAddress();
        if (!$result) {
            throw  new Exception([
                'msg' => 'redis 发生错误'
            ]);
        }
        $snap['orderPrice'] = $orderStatus['orderPrice'];
        $snap['totalCount'] = $orderStatus['totalCount'];
        $snap['pStatus'] = $orderStatus['pStatusArray'];
        $snap['snapAddress'] = Cache::get('user_id:' . $this->uid);
        $snap['snapName'] = count($this->products) > 1 ? $this->products[0]['name'] . '等' : $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];

        return $snap;
    }

    /**
     * 获取用户的 收货地址信息
     * @return bool
     * @throws UserException
     */
    private function getUserAddress()
    {
        $userAddress = UserAddress::where('user_id', '=', $this->uid)->find();
        if (!$userAddress) {
            throw new UserException([
                'msg' => '当前用户收货地址不存在,下单失败',
                'errCode' => 60001
            ]);
        }
        // 使用redis
        redisService::redisConnect();
        return Cache::set('user_id:' . $this->uid, $userAddress->toArray(), 0);
    }

    /**
     * 对外提供检查库存接口 以便其他类进行调用查询
     * @param $orderId
     * @return array
     */
    public function checkOrderStock($orderId)
    {
        $oProduct = OrderProduct::where('order_id', '=', $orderId)->select();
        // 由于 getOrderStatus 是通过 this->oProducts 循环获取的，所以需要赋值
        $this->oProducts = $oProduct;
        $this->products = $this->getProductsByOrder($oProduct);
        // 返回查询库存状态
        return $this->getOrderStatus();
    }

    /**
     * 库存量检测 获取订单状态
     */
    private function getOrderStatus()
    {
        // 订单的状态
        $status = [
            // 订单是否通过检测
            'pass' => true,
            // 订单的总价
            'orderPrice' => 0,
            // 此笔订单中所有商品的件数
            'totalCount' => 0,
            // 订单的每个商品详细信息
            'pStatusArray' => []
        ];

        //todo 通过客户端传递过来的数据 与 数据库中的数据 进行比对 最主要的是比对库存量
        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus($this->products, $oProduct['product_id'], $oProduct['count']);
            $status['pass'] = $pStatus['haveStock'] ? true : false;
            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['count'];
            array_push($status['pStatusArray'], $pStatus);
        }

        return $status;
    }

    /**
     * 获取产品状态 订单状态是根据产品状态 改变而改变
     * 需要检测 客户端传递过来的product_id 在 查询出来的 product 的序号(index)
     * @param $product 查询出来的产品
     * @param $oPid    订单中中的产品ID
     * @param $oCount   订单中的购买数量
     */
    private function getProductStatus($product, $oPid, $oCount)
    {
        $pIndex = -1;
        // 此数组中保存着 每一个产品的详细信息
        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'count' => 0,
            'name' => '',
            // 单个商品的总价格
            'totalPrice' => 0
        ];

        for ($i = 0; $i < count($product); $i++) {
            if ($oPid == $product[$i]['id']) {
                // 由于 $opid 不是数组 ，只是个数字 所以需要保存查到商品的索引
                $pIndex = $i;
            }
        }

        if ($pIndex == -1) {
            throw new  OrderException([
                'msg' => '商品ID为' . $oPid . '不存在，下单失败'
            ]);
        } else {
            $oneProduct = $product[$pIndex];
            $pStatus['id'] = $oneProduct['id'];
            $pStatus['haveStock'] = $oneProduct['stock'] - $oCount >= 0 ? true : false;
            $pStatus['count'] = $oCount;
            $pStatus['name'] = $oneProduct['name'];
            $pStatus['totalPrice'] = $oneProduct['price'] * $oCount;
        }
        return $pStatus;
    }

    /**
     * 通过订单中的 产品id 查询出 产品的详细信息
     * @param $oProduct
     */
    private function getProductsByOrder($oProducts)
    {
        // 此处不能直接循环$oProducts数组 然后进行分开查询，对这样不可控制的数组 遍历查询 会造成 数据库负担严重
        $oPids = [];
        foreach ($oProducts as $item) {
            array_push($oPids, $item['product_id']);
        }
        // 通过数组去查询数据 只显示 以下5个字段 并且转成数组
        $products = Product::all($oPids)->visible(['id', 'price', 'stock', 'name', 'main_img_url'])->toArray();
        return $products;
    }

    /**
     *  检测订单是否已经付款
     */
    public static function isOrderPay($orderStatus)
    {
        if (empty($orderStatus)) {
            throw new OrderException([
                'msg' => '传入的订单状态有误，请检查'
            ]);
        }
        if ($orderStatus != OrderStatusEnum::UNPAID) {
            return false;
        }
        return true;
    }
}