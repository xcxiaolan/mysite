<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/14
 * Time: 15:15
 */
namespace app\Admin\model;

use think\Model;
class Article extends Model
{
    //查询文章类别表父类别ID为0的顶级类别的ID和名字
    public function selin($id=0){
        $data = $this->query("select cat_id,cat_name from ".$this->getTable("category")." where cat_pid=$id");
        return $data;
    }
    //查询文章列表，显示所属分类
    public function sellist(){
        $data=$this->query("select art_id,art_name,b.cat_name,c.cat_name as cat_pname,art_addtime,art_content,art_state,art_stick from "
        .$this->getTable()." as a join ".$this->getTable("category")." as b on a.art_catid=b.cat_id join "
        .$this->gettable("category")." as c on b.cat_pid=c.cat_id");
        return $data;
    }
}