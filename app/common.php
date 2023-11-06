<?php
// 应用公共文件


/**
 * @Desc:生成随机字符
 * @param $len integer 长度
 * @return string 随机字符
 * @author: hzc
 * @Time: 2023/7/5 15:35
 */
function strRandom($len){
    $chars_array = [ "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z"];
    $charsLen = count($chars_array) - 1;
    $outputstr = "";
    for ($i=0; $i<$len;$i++) {
        $outputstr .= $chars_array[mt_rand(0, $charsLen)];
    }
    return $outputstr;
}

/**
 * 生成永远唯一的密钥码 (sha512(返回128位) sha384(返回96位) sha256(返回64位) md5(返回32位))
 * @param int $type 返回格式：0大小写混合  1全大写  2全小写
 * @param string $func 启用算法：
 * @return string
 * @author: hzc
 * @Time: 2023/7/5 15:35
 */
function strSecret($type=0, $func='md5')
{
    $uid = md5(uniqid(rand(),true).microtime());
    $hash = hash($func, $uid);
    $arr = str_split($hash);
    foreach($arr as $v){
        if($type==0){
            $newArr[]= empty(rand(0,1)) ? strtoupper($v) : $v;
        }
        if($type==1){
            $newArr[]= strtoupper($v);
        }
        if($type==2){
            $newArr[]= $v;
        }
    }
    return implode('', $newArr);
}

/**
 * @Desc:递归获取无线下级
 * @param $items array 数据源
 * @param $pid string 父级字段名称
 * @param $child string 子级数组下标名称
 * @return array
 * @author: hzc
 * @Time: 2023/4/13 14:03
 */
function arrayToTree($items, $pid = 'parent_id', $child = 'children')
{
    $map = [];
    $tree = [];
    foreach ($items as &$it) {
        $map[$it['menu_id']] = &$it;
    }
    foreach ($items as &$it) {
        $parent = &$map[$it[$pid]];
        if ($parent) {
            $parent[$child][] = &$it;
        } else {
            $tree[] = &$it;
        }
    }
    return $tree;
}