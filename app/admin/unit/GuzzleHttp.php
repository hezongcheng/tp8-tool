<?php
namespace app\admin\unit;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class GuzzleHttp
{
    protected $client;
    protected $headers;

    /**
     * 初始化客户端
     * @param $headers array 请求头参数
     * @param $isApi boolean 是否是接口
     */
    public function __construct($headers, $isApi = false)
    {


        //关闭ssl验证
        $config['verify'] = false;
        $config['http_errors'] = false;

        //重定向
        $config['allow_redirects'] = false;

        //请求头
        $this->headers = $headers;
        $config['headers'] = $this->headers;

        //json请求
        if($isApi){
            $config['headers']['accept'] ='application/json';
            $config['headers']['Content-Type'] ='application/json';
        }

        //实例化
        $this->client = new Client($config);
    }

    /**
     * @Desc:发送get请求
     * @param $url string 请求地址
     * @param $options array 请求参数
     * @return mixed
     * @author: hzc
     * @Time: 2023/8/11 14:49
     */
    public function get($url, $options = [])
    {
        $response = $this->client->get($url, $options);
        $resContent =  $response->getBody()->getContents();
        return json_decode($resContent,true);
    }

    /**
     * @Desc:发送post请求
     * @param $url string 请求地址
     * @param $options array 请求参数
     * @return mixed
     * @author: hzc
     * @Time: 2023/8/11 14:49
     */
    public function post($url, $options = [])
    {
        $response = $this->client->post($url, $options);
        $resContent =  $response->getBody()->getContents();
        return json_decode($resContent,true);
    }

    /**
     * @Desc:异步post请求
     * @param $requestList array 请求地址列表
     * @return array
     * @author: hzc
     * @Time: 2023/8/11 16:14
     */
    public function asyncPost($requestList){
        if(!in_array($requestList) || empty($requestList)){
            return [];
        }

        //添加批量请求地址和参数
        foreach ($requestList as $key => $vo){
            $queryFromData = [
                'form_params' => $vo['form_params'],
                'headers' =>  $this->headers,
            ];
            $requestListData[$key] = $this->client->postAsync($vo['url'], $queryFromData);
        }

        //批量返回 response
        $responseListData = Promise\unwrap($requestListData);
        return $responseListData;
    }

    /**
     * @Desc:获取url是否正常访问
     * @param $url
     * @return array
     * @author: hzc
     * @Time: 2023/8/11 15:32
     */
    public function urlStatus($url){
        try {
            //针对爬取文章内容设定超时时间 （5秒）
            //开启请求超时，超时后会抛出一个GuzzleHttp\Exception\ConnectException异常
            $client = new Client();
            $response = $client->get($url,['timeout'=>5]);
        } catch (RequestException $e) {
            return ['status' => false, 'msg' => $e->getMessage(),'data' => ''];
        } catch (ConnectException $e){
            return ['status' => false, 'msg' => $e->getMessage(),'data' => ''];
        } catch(ClientException $e){
            return ['status' => false, 'msg' => $e->getMessage(),'data' => ''];
        } catch (Exception $e) {
            return ['status' => false, 'msg' => $e->getMessage(),'data' => ''];
        }

        //判断状态码
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 200 && $statusCode < 400) {
            return ['status' => true, 'msg' => "正常",'data' => ''];
        } else {
            return ['status' => false, 'msg' => "该链接返回code不在200-400之间",'data' => ''];
        }
    }
}