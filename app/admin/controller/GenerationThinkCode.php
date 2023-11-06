<?php

namespace app\admin\controller;

use think\facade\Db;
class GenerationThinkCode
{

    /**
     * @var string 作者
     */
    private $author = "hzc";

    /**
     * @var string[] 表前缀
     */
    private $prefix = "";
    /**
     * @var string[] 表名称
     */
    private $tableName = "";
    /**
     * @var string[] 类名称
     */
    private $className = "";
    /**
     * @var string[] 模块名
     */
    private $modular = "";

    private $suffix = "";

    /**
     * @var string[] 验证条件
     */
    private $validateField = [];



    /**
     * @Desc:将数据表名称转换成驼峰
     * @param $tableNama string 数据表名称
     * @param $prefix string 数据表前缀
     * @return string
     * @author: hzc
     * @Time: 2023/2/16 18:06
     */
    private function tableNameToBig()
    {
        //类名称
        $className ="";

        //将数据库名称转化成驼峰
        $tableArray = explode('_',$this->tableName);

        foreach ($tableArray as $key => $vo){
            $vo[0] = strtoupper($vo[0]);
            $className .= $vo;
        }
        return $className;
    }
    /**
     * @Desc:判断类名称
     * @param $className string 类名称
     * @return bool
     * @author: hzc
     * @Time: 2023/2/16 18:06
     */
    private function checkClassName(){
        //判断文件名
        if(!preg_match('/^[A-Za-z]+$/',$this->className)){
            return false;
        }
        return true;
    }


    /**
     * @Desc:生成模型文件
     * @return string
     * @author: hzc
     * @Time: 2023/2/16 18:06
     */
    private function makeModelFile(){
        //创建文件夹
        $filePath = $this->makePathDirectory($this->modular."/model");

        //拼接文件完整路径
        $fileFullPath = $filePath.$this->className."Model".$this->suffix;

        //写入文件
        $fp=fopen($fileFullPath,'w');
        $conn = "";
        $conn .="<?php\r\n";
        $conn .="declare (strict_types = 1);\r\n";
        $conn .="namespace app\\".$this->modular."\\model;\r\n";
        $conn .="use think\\Model;\r\n";
        $conn .="use app\\".$this->modular."\\validate\\".$this->className."Validate;\r\n";
        $conn .="use think\\exception\\ValidateException;\r\n";
        $conn .="class ".$this->className."Model extends Model{\r\n";
        $conn .="    protected \$table ='".$this->prefix.$this->tableName."';\r\n";

        //模型默认方法
        $conn .= "     /**\r\n";
        $conn .= "      * @Desc: 获取一条数据\r\n";
        $conn .= "      * @param array \$where 查询条件\r\n";
        $conn .= "      * @return array\r\n";
        $conn .= "      * @author: ".$this->author."\r\n";
        $conn .= "      * @Time: ".date('Y/m/d H:i')."\r\n";
        $conn .= "     */\r\n";
        $conn .="    public function getOne(\$where){\r\n";
        $conn .="       return \$this->where(\$where)->find();\r\n";
        $conn .="    }\r\n";




        $conn .= "     /**\r\n";
        $conn .= "      * @Desc: 查询列表数据\r\n";
        $conn .= "      * @param \$where array 查询条件\r\n";
        $conn .= "      * @param \$pageField array 分页条件\r\n";
        $conn .= "      * @return array\r\n";
        $conn .= "      * @author: ".$this->author."\r\n";
        $conn .= "      * @Time: ".date('Y/m/d H:i')."\r\n";
        $conn .= "     */\r\n";
        $conn .="    public function list(\$where,\$pageField){\r\n";
        $conn .="       \$resData =  \$this->where(\$where)->select()->toArray();\r\n";
        $conn .="       \$zCount = count(\$resData);\r\n";
        $conn .="       if(\$zCount > \$pageField['pageSize']){\r\n";
        $conn .="           \$chunkData =  array_chunk(\$resData,(int)\$pageField['pageSize']);\r\n";
        $conn .="           if(\$pageField['pageIndex'] >  count(\$chunkData)){\r\n";
        $conn .="               \$pageField['pageIndex'] = count(\$chunkData);\r\n";
        $conn .="           }\r\n";
        $conn .="           \$arrayKey = \$pageField['pageIndex'] - 1;\r\n";
        $conn .="           \$endData =\$chunkData[\$arrayKey];\r\n";
        $conn .="       }else{\r\n";
        $conn .="           \$endData = \$resData;\r\n";
        $conn .="       }\r\n";
        $conn .="       \$result = [\r\n";
        $conn .="           'total' => \$zCount,\r\n";
        $conn .="           'pageIndex' => \$pageField['pageIndex'],\r\n";
        $conn .="           'pageSize' => \$pageField['pageSize'],\r\n";
        $conn .="           'data' => \$endData,\r\n";
        $conn .="       ];\r\n";
        $conn .="       return \$result;\r\n";
        $conn .="    }\r\n";

        $conn .= "     /**\r\n";
        $conn .= "      * @Desc: 添加数据\r\n";
        $conn .= "      * @param \$data array 表单数据\r\n";
        $conn .= "      * @return array\r\n";
        $conn .= "      * @author: ".$this->author."\r\n";
        $conn .= "      * @Time: ".date('Y/m/d H:i')."\r\n";
        $conn .= "     */\r\n";
        $conn .="    public function add(\$data){\r\n";
        $conn .="       //数据验证\r\n";
        $conn .="       try {\r\n";
        $conn .="           validate(".$this->className."Validate::class)\r\n";
        $conn .="           ->scene('add')\r\n";
        $conn .="           ->check(\$data);\r\n";
        $conn .="       } catch (ValidateException \$e) {\r\n";
        $conn .="           return ['status'=>0,'message'=>\$e->getMessage(),'data'=>[]];\r\n";
        $conn .="       }\r\n";
        $conn .="       \$result =  \$this->insert(\$data);\r\n";
        $conn .="       if(\$result === false){\r\n";
        $conn .="           return ['status'=>0,'message'=>'创建失败','data'=>[]];\r\n";
        $conn .="       }\r\n";
        $conn .="       return ['status'=>1,'message'=>'创建成功','data'=>[]];\r\n";
        $conn .="    }\r\n";

        $conn .= "     /**\r\n";
        $conn .= "      * @Desc: 修改数据\r\n";
        $conn .= "      * @param \$where array 修改条件\r\n";
        $conn .= "      * @param \$data array 表单数据\r\n";
        $conn .= "      * @return array\r\n";
        $conn .= "      * @author: ".$this->author."\r\n";
        $conn .= "      * @Time: ".date('Y/m/d H:i')."\r\n";
        $conn .= "     */\r\n";
        $conn .="    public function edit(\$where,\$data){\r\n";
        $conn .="       //数据验证\r\n";
        $conn .="       try {\r\n";
        $conn .="           validate(".$this->className."Validate::class)\r\n";
        $conn .="           ->scene('edit')\r\n";
        $conn .="           ->check(\$data);\r\n";
        $conn .="       } catch (ValidateException \$e) {\r\n";
        $conn .="           return ['status'=>0,'message'=>\$e->getMessage(),'data'=>[]];\r\n";
        $conn .="       }\r\n";
        $conn .="       \$result = \$this->where(\$where)->update(\$data);\r\n";
        $conn .="       if(\$result === false){\r\n";
        $conn .="           return ['status'=>0,'message'=>'修改失败','data'=>[]];\r\n";
        $conn .="       }\r\n";
        $conn .="       return ['status'=>1,'message'=>'修改成功','data'=>[]];\r\n";
        $conn .="    }\r\n";

        $conn .= "     /**\r\n";
        $conn .= "      * @Desc: 删除数据\r\n";
        $conn .= "      * @param \$where array 删除条件\r\n";
        $conn .= "      * @return bool\r\n";
        $conn .= "      * @author: ".$this->author."\r\n";
        $conn .= "      * @Time: ".date('Y/m/d H:i')."\r\n";
        $conn .= "     */\r\n";
        $conn .="    public function del(\$where){\r\n";
        $conn .="       return \$this->where(\$where)->delete();\r\n";
        $conn .="    }\r\n";

        $conn .= "     /**\r\n";
        $conn .= "      * @Desc: 修改字段\r\n";
        $conn .= "      * @param \$where array 修改条件\r\n";
        $conn .= "      * @param \$editField array 修改字段值\r\n";
        $conn .= "      * @return SystemEmailConfigModel\r\n";
        $conn .= "      * @author: ".$this->author."\r\n";
        $conn .= "      * @Time: ".date('Y/m/d H:i')."\r\n";
        $conn .= "     */\r\n";
        $conn .="    public function editField(\$where,\$editField){\r\n";
        $conn .="       return \$this->where(\$where)->update(\$editField);\r\n";
        $conn .="    }\r\n";

        //结尾
        $conn .="}\r\n";
        $conn .="?>\r\n";
        fwrite($fp,$conn);
        fclose($fp);
    }

    /**
     * @Desc:生成验证器文件
     * @return string
     * @author: hzc
     * @Time: 2023/2/16 18:06
     */
    private function makeValidateFile(){
        //创建文件夹
        $filePath = $this->makePathDirectory($this->modular."/validate");
        try {
            //查询字段
            $tableColumns = Db::query("SHOW FULL COLUMNS FROM ".$this->prefix.$this->tableName);
        }catch (\Exception $e){
            //echo $e->getMessage();
            return false;
        }

        //将不是主键且Default值为空的字段添加到controller接收当中
        foreach ($tableColumns as $v){
            //验证场景做准备
            if($v['Default'] == "" && $v['Key'] != "PRI"){
                array_push($this->validateField,$v['Field']);
            }
        }


        //拼接文件完整路径
        $fileFullPath = $filePath.$this->className."Validate".$this->suffix;

        header("content-type:text/html;charset=utf-8");
        //写入文件
        $fp=fopen($fileFullPath,'w');


        $conn = "";
        $conn .="<?php\r\n";
        $conn .="declare (strict_types = 1);\r\n";
        $conn .="namespace app\\".$this->modular."\\validate;\r\n";

        $conn .="use think\Validate;\r\n";
        $conn .="class ".$this->className."Validate extends Validate{\r\n";
        $conn .="   protected \$rule =   [\r\n";



        //获取所有字段验证规则
        foreach ($tableColumns as $v){

            $validateField = [];
            //是否为空
            if($v['Null'] == "NO"){
                array_push($validateField,"require");
            }
            //判断是否是int和string
            if(stripos($v['Type'],'int') !== false || stripos($v['Type'],'varchar') !== false){
                preg_match('/(?:\()(.*)(?:\))/i', $v['Type'], $match);
                $fieldLenght = $match[1];
                array_push($validateField,"max:".$fieldLenght);

            }

            //有条件
            if(count($validateField) > 0){
                $validateKey = $v['Field']."|".$v['Comment'];
                $validateValue = implode("|",$validateField);
                $conn .="       '".$validateKey."'  => '".$validateValue."',\r\n";
            }

        }
        $conn .="   ];\r\n";


        //有from字段
        if(count($this->validateField) > 0){
            $scene = ['Add','Edit'];
            foreach ($scene as $v){
                $conn .="   public function scene".$v."(){\r\n";
                $conn .="       return \$this->only(".json_encode($this->validateField).");\r\n";
                $conn .="   }\r\n";
            }
        }

        //结尾
        $conn .="}\r\n";
        $conn .="?>\r\n";
        fwrite($fp,$conn);
        fclose($fp);
    }


    /**
     * @Desc:生成路由文件
     * @return string
     * @author: hzc
     * @Time: 2023/2/16 18:06
     */
    private function makeRouteFile(){

        //创建文件夹
        $filePath = $this->makePathDirectory($this->modular."/route");
        //拼接文件完整路径
        $fileFullPath = $filePath."/".$this->className.$this->suffix;

        header("content-type:text/html;charset=utf-8");
        //写入文件
        $Rfp=fopen($fileFullPath,'w');

        //创建路由文件
        $fileContent = "";
        $fileContent .="<?php\r\n";
        $fileContent .="use think\\facade\\Route;\r\n";
        $fileContent .="Route::group('".$this->className."', function () {\r\n";
        $fileContent .="  Route::rule('list', '".$this->className."/list','get');\r\n";
        $fileContent .="  Route::rule('add', '".$this->className."/add','post');\r\n";
        $fileContent .="  Route::rule('edit', '".$this->className."/edit','post');\r\n";
        $fileContent .="  Route::rule('delete', '".$this->className."/delete','post');\r\n";
        $fileContent .="});\r\n";
        $fileContent .="?>\r\n";

        fwrite($Rfp,$fileContent);
        fclose($Rfp);
    }
    /**
     * @Desc:生成控制器文件
     * @return string
     * @author: hzc
     * @Time: 2023/2/16 18:06
     */
    private function makeControllerFile(){
        //创建文件夹
        $filePath = $this->makePathDirectory($this->modular."/controller");

        //拼接文件完整路径
        $fileFullPath = $filePath.$this->className.$this->suffix;
        header("content-type:text/html;charset=utf-8");


        //写入文件
        $fp=fopen($fileFullPath,'w');
        $conn = "";
        $conn .="<?php\r\n";
        $conn .="declare (strict_types = 1);\r\n";
        $conn .="namespace app\\".$this->modular."\\controller;\r\n";

        $conn .="use app\\".$this->modular."\\model\\".$this->className."Model;\r\n";
        $conn .="use app\\admin\\controller\\Api;\r\n";
        $conn .="use think\\Request;\r\n";
        $conn .="class ".$this->className." extends Api{\r\n";
        $conn .="   public \$model;\r\n";

        //构造方法
        $conn .="   public function __construct(){\r\n";
        $conn .="       \$this->model = new ".$this->className."Model();\r\n";
        $conn .="   }\r\n";


        //方法定义
        $conn .= "     /**\r\n";
        $conn .= "      * @Desc: 列表\r\n";
        $conn .= "      * @return ApiService|\\think\Response\r\n";
        $conn .= "      * @author: ".$this->author."\r\n";
        $conn .= "      * @Time: ".date('Y/m/d H:i')."\r\n";
        $conn .= "     */\r\n";
        $conn .="   public function list(){\r\n";
        $conn .="       \$where = [];\r\n";
        $conn .="       \$pageField = [\r\n";
        $conn .="           'pageIndex' => input('pageIndex',1),\r\n";
        $conn .="           'pageSize' => input('pageSize',10),\r\n";
        $conn .="       ];\r\n";
        $conn .="       \$data = \$this->model->list(\$where,\$pageField);\r\n";
        $conn .="       return \$this->success(\$data,'获取成功');\r\n";
        $conn .="   }\r\n";

        $conn .= "     /**\r\n";
        $conn .= "      * @Desc: 添加\r\n";
        $conn .= "      * @return ApiService|\\think\Response\r\n";
        $conn .= "      * @author: ".$this->author."\r\n";
        $conn .= "      * @Time: ".date('Y/m/d H:i')."\r\n";
        $conn .= "     */\r\n";
        $conn .="   public function add(){\r\n";
        $conn .="       \$paramData = [\r\n";
        if(count($this->validateField) > 0){
            foreach ($this->validateField as $v){
                $conn .="           '".$v."' => input('".$v."'),\r\n";
            }
        }
        $conn .="       ];\r\n";
        $conn .="       \$result = \$this->model->add(\$paramData);\r\n";
        $conn .="       if(\$result['status'] == 0){\r\n";
        $conn .="           return \$this->error(\$result['message']);\r\n";
        $conn .="       }\r\n";
        $conn .="       return \$this->success([],'添加成功');\r\n";
        $conn .="   }\r\n";

        $conn .= "     /**\r\n";
        $conn .= "      * @Desc: 修改\r\n";
        $conn .= "      * @return ApiService|\\think\Response\r\n";
        $conn .= "      * @author: ".$this->author."\r\n";
        $conn .= "      * @Time: ".date('Y/m/d H:i')."\r\n";
        $conn .= "     */\r\n";
        $conn .="   public function edit(Request \$request){\r\n";

        $conn .="       if(\$request->isGet()){\r\n";
        $conn .="           \$result = \$this->model->getOne(['id'=>input('id')]);\r\n";
        $conn .="           return \$this->success(\$result,'获取成功');\r\n";
        $conn .="       }\r\n";


        $conn .="       \$where = [];\r\n";
        $conn .="       \$paramData = [\r\n";
        $conn .="           'id' => input('id'),\r\n";
        if(count($this->validateField) > 0){
            foreach ($this->validateField as $v){
                $conn .="           '".$v."' => input('".$v."'),\r\n";
            }
        }
        $conn .="       ];\r\n";
        $conn .="       \$result = \$this->model->edit(\$where,\$paramData);\r\n";
        $conn .="       if(\$result['status'] == 0){\r\n";
        $conn .="           return \$this->error(\$result['message']);\r\n";
        $conn .="       }\r\n";
        $conn .="       return \$this->success([],'修改成功');\r\n";
        $conn .="   }\r\n";

        $conn .= "     /**\r\n";
        $conn .= "      * @Desc: 删除\r\n";
        $conn .= "      * @return ApiService|\\think\Response\r\n";
        $conn .= "      * @author: ".$this->author."\r\n";
        $conn .= "      * @Time: ".date('Y/m/d H:i')."\r\n";
        $conn .= "     */\r\n";
        $conn .="   public function delete(){\r\n";
        $conn .="       \$where = [\r\n";
        $conn .="           'id' => input('id')\r\n";
        $conn .="       ];\r\n";
        $conn .="       \$data = \$this->model->del(\$where);\r\n";
        $conn .="       return \$this->success(\$data,'删除成功');\r\n";
        $conn .="   }\r\n";


        //结尾
        $conn .="}\r\n";
        $conn .="?>\r\n";
        fwrite($fp,$conn);
        fclose($fp);

    }

    /**
     * @Desc: 生成框架文件
     * @param $tableName string 表名称
     * @param $modular string 模块名
     * @param $prefix string 表前缀
     * @param $suffix string 文件后缀
     * @return bool|void
     * @author: hzc
     * @Time: 2023/2/15 14:53
     */
    public function makeThinkCode($tableName, $modular = "test", $prefix = "", $suffix = ".php")
    {
        try {
            //初始化类属性
            $this->modular = $modular;
            $this->suffix = $suffix;
            $this->prefix = $prefix;
            //去除表前缀
            if($prefix){
                $tableName = str_replace($prefix,'',$tableName);
            }
            $this->tableName = $tableName;


            //将数据表名称转换成驼峰
            $this->className = $this->tableNameToBig();
            //判断文件名
            $checkClassName = $this->checkClassName();
            if(!$checkClassName){
                return "'判断类名称'失败";
            }

            $this->makeModelFile();
            $this->makeValidateFile();
            $this->makeRouteFile();
            $this->makeControllerFile();

            return "生成成功";

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @Desc:创建文件
     * @param $modular string 模块名称
     * @param $type string 文件属性 controller validate model route
     * @param $className string 文件驼峰名称
     * @param $suffix string 文件后缀
     * @param $fileContent string 文件内容
     * @return bool
     * @author: hzc
     * @Time: 2023/2/17 11:01
     */
    private function makePhpFile($modular, $type, $className, $suffix, $fileContent)
    {
        //创建文件夹
        $filePath = $this->makePathDirectory($modular."/".$type);
        //拼接文件完整路径
        $fileFullPath = $filePath."/".$className.$suffix;

        header("content-type:text/html;charset=utf-8");
        //写入文件
        $Rfp=fopen($fileFullPath,'w');
        $Rwrite = fwrite($Rfp,$fileContent);
        fclose($Rfp);

        if(!$Rwrite){
            return false;
        }
        return true;
    }


    /**
     * @Desc:根据路径创建文件夹
     * @param $directoryPath string 路径
     * @return false|void
     * @author: hzc
     * @Time: 2023/2/17 10:13
     */
    private function makePathDirectory($directoryPath)
    {
        //文件夹路径
        $filePath = base_path($directoryPath);
        $filePath = str_replace('/', '\\', $filePath);
        //判断文件夹是否存在
        if(!file_exists($filePath)){
            $folderCreate = $this->makeDirectory($filePath);
            if(!$folderCreate){
                return false;
            }
        }
        return $filePath;
    }

    /**
     * @Desc:创建文件夹
     * @param $dir string 文件夹路径
     * @param $mode integer 权限
     * @return bool
     * @author: hzc
     * @Time: 2023/2/15 13:38
     */
    private function makeDirectory($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
        if (!$this->makeDirectory(dirname($dir), $mode)) return FALSE;
        return @mkdir($dir, $mode);
    }

}