<?php
namespace app\admin\model;

use think\Db;
use think\Model;
use think\facade\Session;
use think\facade\Request;
class Category extends Model
{
    protected $article = "ms_articele";
    //查询文章类别表父类别ID为0的顶级类别的ID和名字
    public function selin($id=0){
        //$where=["cat_id","cat_name"];
        $data = $this->query("select cat_id,cat_name from ".$this->getTable()." where cat_pid=$id");
        return $data;
    }
    //查询文章类别表
    public function sellist($start=0,$num=5){
        $data=$this->query("select a.cat_id,a.cat_name,b.cat_name as cat_pname,a.cat_addtime,a.cat_state 
        from ".$this->getTable()." as a join ".$this->getTable()." as b on a.cat_pid=b.cat_id 
        where a.cat_id not in(0) limit $start,$num");
        return $data;
    }
    //查询文章类别表中符合条件的
    public function seler($search,$start=0,$num=5){
        $data=$this->query("select a.cat_id,a.cat_name,b.cat_name as cat_pname,a.cat_addtime,a.cat_state 
        from ".$this->getTable()." as a join ".$this->getTable()." as b on a.cat_pid=b.cat_id 
        where a.cat_pid=$search limit $start,$num");
        return $data;
    }
    public function sel(){
        $data = $this->query("select * from ".$this->getTable());
        var_dump($this->getLastSql());
        return $data;
    }

    public function selectById(){
        $id = 0;
        $id = Request::param('id');
//        $id = $request->get('id',true);
        if(empty($id)){
            return 0;
//            exit();
        }else{
            if( $result = $this->where(['arcate_id'=> $id])->find()){
                return $result;
            }else{
                return 0;
//                exit();
            };
        }

    }

    /*
     * 根据两个字段查询；
     *
     * @where array 包含两个字段名称；
     */
    public function check(){
//        $table = $this->table('article');
//        var_dump($table);
        $data = $this->query('select * from ' . $this->getTable()  . ' as c left join ' . $this->getTable('articele') . ' as a on cat_id = art_catid where cat_id=1  ');
        var_dump($data);
    }
}