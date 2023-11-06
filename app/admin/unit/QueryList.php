<?php

namespace app\admin\unit;

use GuzzleHttp\Exception\RequestException;
use QL\QueryList as QueryListLib;

class QueryList
{
    //超时时间
    const TimeOut = 30;

    /**
     * @Desc:爬取文章和图片
     * @param $url string 请求链接
     * @param $param array 请求参数
     * @param $headers array 请求头信息
     * @param $rules array 规则
     * @param $proxy string 代理地址
     * @return string
     * @author: hzc
     * @Time: 2023/7/5 15:28
     */
    public function queryGet($url, $param, $rules, $headers = [],$proxy =''){
        //默认请求头
        if(empty($headers)){
            $headers = [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
                'Accept-Encoding' => 'gzip, deflate, br'
            ];
        }
        try{
            //拓展方法
            $ql = QueryListLib::getInstance();
            $ql = $ql->bind('downloadImage',function ($path){
                $data = $this->getData()->map(function ($item) use($path){

                    //正则匹配图片路径
                    $pattern= "/<img .*?src=[\"|\']+(.*?)[\"|\']+.*?>/";
                    preg_match_all ($pattern,$item,$imgArr);
                    $imgArr = array_filter($imgArr);

                    //保存图片$imgArr 0是完整标签 1是src地址
                    if(count($imgArr) > 0  && isset($imgArr[1])){
                        foreach ($imgArr[1] as $key => $vo){
                            //准备文件夹
                            $fullPath = public_path($path);
                            $fileGeneration = new FileService();
                            $fileGeneration->makeDirectory($fullPath);

                            //保存图片
                            $img = file_get_contents($vo);
                            $endFullPath = $fullPath.'/'.md5($img).'.jpg';
                            file_put_contents($endFullPath,$img);

                            //替换内容图片地址为本地
                            $item = str_replace($vo,'/'.$path.'/'.md5($img).'.jpg',$item);
                        }
                    }
                    return $item;
                });

                //更新data属性
                $this->setData($data);
                return $this;
            });

            //请求参数
            $option = [
                'proxy' => $proxy,
                'timeout' => self::TimeOut,
                'headers' => $headers,
            ];

            //get获取数据
            $data = $ql->get($url,$param,$option)->rules($rules)->query()->downloadImage('storage/queryList/img')->getData();
            return $data;
        }catch(RequestException $e){
            return $e->getMessage();
        }
    }
}