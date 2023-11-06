<?php
namespace app\admin\unit;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
class FirebaseJwt
{
    //加密中常用的 盐
    private static $key='hezongcheng';

    /**
     * @Desc:jwt加密用户数据
     * @param $userInfo
     * @return string
     * @author: hzc
     * @Time: 2023/1/30 10:45
     */
    public static function generateToken($userInfo){
        $payload = [
            'key' => self::$key,
            'iss' => 'http://example.org',
            'aud' => 'http://example.com',
            'iat' => time(),
            'nbf' => time(),
            'exp' => time()+7*12*3600,
            'data'=> [
                'id' => $userInfo['id'],
                'username' => $userInfo['name'],
            ],
        ];

        return JWT::encode($payload, self::$key, 'HS256');
    }



    /**
     * @Desc:验证token
     * @param $token
     * @return array|int[]
     * @author: hzc
     * @Time: 2023/1/30 10:51
     */
    public static function validateToken($token){
        try {
            if(!$token){
                return ['code' => 0,'msg'=>'非法操作','data'=>[]];
            }
            JWT::$leeway = 60;
            $decoded = JWT::decode($token, new Key(self::$key,'HS256'));
            $arr = (array)$decoded;
            return ['code' => 1,'msg'=>'获取成功','data'=>$arr['data']];
        } catch(SignatureInvalidException $e) {
            return ['code' => 0,'msg'=>'签名不正确','data'=>[]];
        }catch(BeforeValidException $e) {
            return ['code' => 0,'msg'=>'token未生效','data'=>[]];
        }catch(ExpiredException $e) {  // token过期
            return ['code' => 0,'msg'=>'token过期','data'=>[]];
        }catch(Exception $e) {
            return ['code' => 0,'msg'=>$e->getMessage(),'data'=>[]];
        }
    }
}