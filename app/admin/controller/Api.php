<?php

namespace app\admin\controller;

class Api
{
    /**
     * @Desc:返回成功数据
     * @param $data array  返回数据
     * @param $message string 返回信息
     * @param $code integer 状态码
     * @return \think\response\Json
     * @author: hzc
     * @Time: 2023/8/11 9:45
     */
    protected function success($data = [], $message = '操作成功', $code = 200)
    {
        $result = [
            'status'  => true,
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ];

        return json($result);
    }

    /**
     * @Desc:返回错误数据
     * @param $message string 返回信息
     * @param $code integer 状态码
     * @return \think\response\Json
     * @author: hzc
     * @Time: 2023/8/11 9:46
     */
    protected function error($message = '操作失败', $code = 500)
    {
        $result = [
            'status'  => false,
            'code'    => $code,
            'message' => $message,
            'data'    => [],
        ];

        return json($result);
    }


    // 自定义返回数据
    protected function response($data, $message = '操作成功', $status = true, $code = 200)
    {
        $result = [
            'status'  => $status,
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ];

        return json($result);
    }
}