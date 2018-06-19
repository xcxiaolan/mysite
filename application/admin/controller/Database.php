<?php
namespace app\admin\controller;

use \think\Controller;
use \think\Db;
class Database extends Base{
    public function showtables(){
        $tables = Db::query('show tables;');
        $this->assign('tables',$tables);
        return $this->fetch();
    }
    
    public function back(){
        $tables = $this->tables();
        $this->assign('tables',$tables);
        return $this->fetch();
    }

    
    public function setbackup(){
        //获取表名称,由选择输入;
        $table = $_POST['tablename'];
        
        $is_table = Db::query("SHOW TABLES LIKE '".$table[0]."'");
        if($is_table){
            
        }else{
            $this->error('表不存在');
        }
    }

    private function tables(){
        return Db::query('show tables;');

    }

    public function exportDatabase(){
        header("Content-type:text/html;charset=utf-8");
        $path = RUNTIME_PATH.'mysql/';
        $database = config('database')['database'];
        //echo "运行中，请耐心等待...<br/>";
        $info = "-- ----------------------------\r\n";
        $info .= "-- 日期：".date("Y-m-d H:i:s",time())."\r\n";
        $info .= "-- MySQL - 5.5.52-MariaDB : Database - ".$database."\r\n";
        $info .= "-- ----------------------------\r\n\r\n";
        $info .= "CREATE DATAbase IF NOT EXISTS `".$database."` DEFAULT CHARACTER SET utf8 ;\r\n\r\n";
        $info .= "USE `".$database."`;\r\n\r\n";

        // 检查目录是否存在
        if(is_dir($path)){
            // 检查目录是否可写
            if(is_writable($path)){
                //echo '目录可写';exit;
            }else{
                //echo '目录不可写';exit;
                chmod($path,0777);
            }
        }else{
            //echo '目录不存在';exit;
            // 新建目录
            mkdir($path, 0777, true);
            //chmod($path,0777);
        }

        // 检查文件是否存在
        $file_name = $path.$database.'-'.date("Y-m-d",time()).'.sql';
        if(file_exists($file_name)){
            echo "数据备份文件已存在！";
            exit;
        }
        file_put_contents($file_name,$info,FILE_APPEND);

        //查询数据库的所有表
        $result = Db::query('show tables');
        //print_r($result);exit;
        foreach ($result as $k=>$v) {
            //查询表结构
            $val = $v['Tables_in_'.$database];
            $sql_table = "show create table ".$val;
            $res = Db::query($sql_table);
            //print_r($res);exit;
            $info_table = "-- ----------------------------\r\n";
            $info_table .= "-- Table structure for `".$val."`\r\n";
            $info_table .= "-- ----------------------------\r\n\r\n";
            $info_table .= "DROP TABLE IF EXISTS `".$val."`;\r\n\r\n";
            $info_table .= $res[0]['Create Table'].";\r\n\r\n";
            //查询表数据
            $info_table .= "-- ----------------------------\r\n";
            $info_table .= "-- Data for the table `".$val."`\r\n";
            $info_table .= "-- ----------------------------\r\n\r\n";
            file_put_contents($file_name,$info_table,FILE_APPEND);
            $sql_data = "select * from ".$val;
            $data = Db::query($sql_data);
            //print_r($data);exit;
            $count= count($data);
            //print_r($count);exit;
            if($count<1) continue;
            foreach ($data as $key => $value){
                $sqlStr = "INSERT INTO `".$val."` VALUES (";
                foreach($value as $v_d){
                    $v_d = str_replace("'","\'",$v_d);
                    $sqlStr .= "'".$v_d."', ";
                }
                //需要特别注意对数据的单引号进行转义处理
                //去掉最后一个逗号和空格
                $sqlStr = substr($sqlStr,0,strlen($sqlStr)-2);
                $sqlStr .= ");\r\n";
                file_put_contents($file_name,$sqlStr,FILE_APPEND);
            }
            $info = "\r\n";
            file_put_contents($file_name,$info,FILE_APPEND);
        }
        echo "数据备份完成！";
    } 
}
