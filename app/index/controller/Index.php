<?php
declare (strict_types = 1);

namespace app\index\controller;

use think\facade\Db;
use app\admin\controller\GenerationThinkCode;

class Index
{
    public function index()
    {

        //获取数据表
        $tables = Db::query('SHOW TABLE STATUS');
        $tablesArray = [];
        foreach ($tables as $key => $vo){
            if($vo['Name']){
                array_push($tablesArray,$vo['Name']);
            }
        }

        //代码生成
        $codeGen = new GenerationThinkCode();
        foreach ($tablesArray as  $v){
            $codeGen->makeThinkCode($v,"test");
        }
        return '生成';
    }
}
