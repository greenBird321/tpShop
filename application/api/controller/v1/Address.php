<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/5/11
 * Time: 下午2:39
 */

namespace app\api\controller\v1;


use app\api\model\User as userModel;
use app\api\service\Token;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\lib\exception\SuccessException;
use app\lib\exception\UserException;

class Address extends BaseController
{
    // 这些方法 first 与 second 与 third 都是要在一个控制器里才能生效 否则报错
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress']
    ];

    /**
     * 创建或者更新收货地址，必须携带token 和 权限的设置
     * @return \think\response\Json
     * @throws UserException
     */
    public function createOrUpdateAddress()
    {
        $validate = new AddressNew();
        $validate->goCheck();
        // 根据token 拿取 uid
        $uid = TokenService::getCurrentUid();
        // 根据uid来查找用户数据， 判断用户是否存在，不存在抛出异常(按照道理来讲，如果可以更改地址信息，说明这个用户已经存在)
        $user = userModel::get($uid);
        if (!$user) {
            throw new UserException();
        }
        // 获取用户从客户端提交来的地址信息 更改或添加 库中的数据
        // 此处不能直接获取从客户端post过来的数据， 因为不安全，有可能会把库中的数据覆盖
        //  $dataArr = getData();

        // 使用输入助手函数的方法获取post提交过来的所有参数 并进行过滤 不止需要接受一个键对应的值，所以需要用验证器验证后才接收
        $dataArray = $validate->getDataByRule(input('post.'));

        // 利用都是model类型 实现关联查询
        $address = $user->address();
        if (!$address) {
            // 如果不存在 则新增地址信息 实例化模型后调用save方法表示新增, 或者可以直接显式的表明是否是更新操作
            $user->address->isUpdate(false)->save($dataArray);
        } else {
            // 如果存在 则更新地址信息 查询数据后调用save()方法表示更新，save方法传入更新条件后表示更新
            $user->address->isUpdate(true)->save($dataArray);
        }
        return json(new SuccessException(), 201);
    }
}