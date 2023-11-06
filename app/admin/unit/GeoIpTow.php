<?php
namespace app\admin\unit;

use GeoIp2\Database\Reader;
class GeoIpTow
{
    /**
     * @Desc:根据ip返回当前ip地址
     * @param $ip string ip地址
     * @return array
     * @author: hzc
     * @Time: 2023/7/5 15:24
     */
    public function getIpInfo($ip)
    {
        //验证ip
        //说明 ：FILTER_VALIDATE_IP默认验证IPv4地址，但您可以通过将第三个参数设置为FILTER_FLAG_IPV6来验证IPv6地址,或者通过将第三个参数设置为FILTER_FLAG_NO_PRIV_RANGE来禁止私有IP地址的验证
        //ipv4验证
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return  ['code' => 201 ,'msg' => '不是有效的IP地址','data' => []];
        }


        $file_path = root_path('extend\geoip2-mmdb').'GeoLite2-City.mmdb';
        $reader = new Reader($file_path);
        try {

            $record = $reader->city($ip);
            //判断语言编码
            $thisLanguageCode = 'zh-CN';
            //返回数据
            $result = [
                'isoCode' => $record->country->isoCode,
                //国家
                'country' => $record->country->names[$thisLanguageCode],
                //省
                'economize' => $record->mostSpecificSubdivision->names[$thisLanguageCode],
                //市
                'market' => $record->city->names[$thisLanguageCode],
                //纬度
                'latitude' => $record->location->latitude,
                //经度
                'longitude' => $record->location->longitude,

                'network' => $record->traits->network,
            ];

        }catch (\Exception $e){
            if($e->getCode() === 0){
                return ['code' => 201 ,'msg' => 'Current IP "'.$ip.'" address not found','data' => []];
            }
        }

        return ['code' => 200 ,'msg' => 'Select success','data' => $result];
    }
}