<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/4/18
 * Time: 下午2:41
 */

namespace app\lib\exception;


use Exception;
use think\exception\Handle;
use think\Log;
use think\Request;

/**
 * Class ExceptionHandler
 * 全局异常处理类 覆盖tp中的 Handle 的原有 render() 方法
 */
class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;

    /**
     *  需要覆盖TP中原来的render方法 才可使用自定义的异常抛出 根据是否需要回复客户端来进行判断是否需要记录
     *  AOP思想的一个举例, 不管是从哪里来的 必须经过同一个入口，在入口处进行验证，这就是AOP思想的一个举例
     */
    public function render(Exception $e)
    {
        if (!config('app_debug')) {     // 新增如果是调试模式则不使用 自定义异常抛出
            if ($e instanceof BaseException) {
                // 返回给客户端的错误信息
                $this->code = $e->code;
                $this->msg = $e->msg;
                $this->errorCode = $e->errCode;
            } else {
                // 服务器内部代码错误 需要屏蔽 错误 并记录日志
                $this->code = 500;
                $this->msg = 'server error';
                $this->errorCode = 999;
                // 把错误记录到日志
                $this->recordErrorLog($e);
            }
            $request = Request::instance();
            $result = [
                'errorCode' => $this->errorCode,
                'msg' => $this->msg,
                'request_url' => $request->url()
            ];
            return json($result, $this->code);
        } else {
            return parent::render($e);
        }
    }

    /**
     * 记录错误日志
     * @param Exception $e
     */
    private function recordErrorLog(Exception $e)
    {
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH,
            'level' => ['error']
        ]);
        Log::record($e->getMessage(), 'error');
    }
}