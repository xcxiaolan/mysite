<?php
namespace app\admin\controller;

use think\Request;
use think\facade\Session as Session;
use think\Db;
use app\admin\model\Category as CategoryModel;

class Category extends Base
{
    //文章列表  $sum 总记录数 $num 每页显示记录数 $pagecount 总页数 $start 起始位置
    public function catlist(){
        $Category=new CategoryModel;
        $num=5;
        $page = 1;
        $list=$Category->sellist();
        for($i=0;$i<count($list);$i++){
            $addtime=$list[$i]["cat_addtime"];
            $list[$i]["cat_addtime"]=date("Y-m-d H:i:s",$addtime);
        }
        $cate=$Category->selin();//做下拉选项的顶级类别
        $sum=$Category->where("cat_id","not in","0")->select()->count();
        $pagecount=ceil($sum/$num);
        $pagelist=$this->page($pagecount);//分页的页码
        $this->assign("pageNum",$pagelist);
        $this->assign("list",$list);
        $this->assign("cate",$cate);
        return $this->fetch();
    }
    //分页列表
    public function artable(Request $request){
        $Category=new CategoryModel;
        $num=5;
        $page = $request->post("page");
        $start=($page-1)*$num;
        $list=$Category->sellist($start,$num);
        for($i=0;$i<count($list);$i++){
            $addtime=$list[$i]["cat_addtime"];
            $list[$i]["cat_addtime"]=date("Y-m-d H:i:s",$addtime);
        }
        echo json_encode($list,JSON_UNESCAPED_UNICODE);
    }
    //文章类别查询操作
    public function listajax(Request $request){
        $Category=new CategoryModel;
        $search=$request->post("search");
        Session::set("seaName",$search);
        $num=5;
        if($search==""){
            $list=$Category->sellist();
        }else{
            $list=$Category->seler($search);
        }
        for($i=0;$i<count($list);$i++){
            $addtime=$list[$i]["cat_addtime"];
            $list[$i]["cat_addtime"]=date("Y-m-d H:i:s",$addtime);
        }
        if($search==""){
            $sum=$Category->where("cat_id","not in","0")->select()->count();
        }else{
            $sum=$Category->where('cat_pid',$search)->select()->count();
        }
        $pagecount=ceil($sum/$num);
        $pagelist=$this->page($pagecount);//分页的页码
        $data = [$list,$pagelist];
        echo json_encode($data,JSON_UNESCAPED_UNICODE);

    }
    public function arcalist(Request $request){
        $Category=new CategoryModel;
        $search=Session::get("seaName");
        $num=5;
        $page = $request->post("page");
        $start=($page-1)*$num;
        if($search==""){
            $list=$Category->sellist($start,$num);
        }else{
            $list=$Category->seler($search,$start,$num);
        }
        for($i=0;$i<count($list);$i++){
            $addtime=$list[$i]["cat_addtime"];
            $list[$i]["cat_addtime"]=date("Y-m-d H:i:s",$addtime);
        }
        echo json_encode($list,JSON_UNESCAPED_UNICODE);
    }
    //修改状态操作
    public function upstate(Request $request){
        $Category=new CategoryModel;
        $id=$request->get("id");
        $state=$request->get("state");
        if($state == "1"){
            $list=$Category->where("cat_id",$id)->update(["cat_state"=>0]);
        }else{
            $list=$Category->where("cat_id",$id)->update(["cat_state"=>1]);
        }
        echo $list;
    }
    //删除类别
    public function delect(Request $request){
        $Category=new CategoryModel;
        $id=$request->post("id");
        $list=$Category->where(["cat_id"=>$id])->delete();
        echo $list;
    }
    //渲染添加类别页面
    public function add(){
        $Category=new CategoryModel;
        $cate=$Category->selin();
        $this->assign("cate",$cate);
        return $this->fetch();
    }
    //添加类别操作
    public function addcon(Request $request){
        $Category=new CategoryModel;
        $cat_name=$request->post("name");
        $cat_pid=$request->post("pid");
        $id=$this->shid();
        if(!$this->daname($cat_name)){
            echo "<script>alert('该类别名已存在，请更换');window.location.href='add'</script>";
        }
        $Category->data(["cat_id"=>$id,"cat_name"=>$cat_name,"cat_pid"=>$cat_pid,"cat_addtime"=>time()]);
        $list=$Category->save();
        if($list){
            echo "<script>alert('添加类别成功');window.location.href='add'</script>";
        }else{
            echo "<script>alert('添加类别失败');window.location.href='add'</script>";
        }
    }
    //修改类别页面
    public function update(Request $request){
        $id=$request->get("id");
        $Category=new CategoryModel;
        $list=$Category->where("cat_id",$id)->find();
        $cate=$Category->selin();
        $this->assign("cate",$cate);
        $this->assign("list",$list);
        return $this->fetch();
    }
    //修改类别操作
    public function aupdate(Request $request){
        $Category=new CategoryModel;
        $id=$request->post("id");
        $usname=$request->post("usname");
        $name=$request->post("name");
        $pid=$request->post("pid");
        $list=["cat_name"=>$name,"cat_pid"=>$pid,];
        if($this->daname($name,$usname)){
            $a=$Category->where(['cat_id'=>$id])->update($list);
            if($a){
                echo "<script>alert('更新成功！');window.location.href='catlist'</script>";
            }else{
                echo "<script>alert('没有更新！');window.location.href='update?id=$id'</script>";
            }
        }else{
            echo "<script>alert('用户名已存在，请更换');window.location.href='update?id=$id'</script>";
        }
    }
    //检查修改的用户名是否和数据库中其他用户名重名
    //$usname是修改前的用户名,$name是修改后的用户名
    //数据库存在新增的用户名,返回false,不存在,返回true
    public function daname($name,$usname=1){
        $Category=new CategoryModel;
        if($usname==$name){
            return true;
        }else{
            $list=$Category->where("cat_name","not in",$usname)->column("cat_name");
            $num=0;
            for($i=0;$i<count($list);$i++){
                if($list[$i]!=$name){
                    $num=$num+1;
                }
            }
            if($num==count($list)){
                return true;
            }else{
                return false;
            }
        }
    }
    //页码
    public function page($page){
        $list="";
        for($i=1;$i<=$page;$i++){
            $list.="<li  class='paginate_button previous ' id='dataTables-example_previous'>
            <a class='page' aria-controls='dataTables-example' data-dt-idx='".$i."' tabindex='0'>$i</a></li>";
        }
        return $list;
    }
    //获取数据表中ID最大的值
    public function shid(){
        $Category = new CategoryModel;
        $num=$Category->limit(0,1)->order("cat_id","desc")->column("cat_id");
        $numu=$num[0]+1;
        return $numu;
    }

    public function ca(Request $request){
        $Category = new CategoryModel;
        $num=5;
//        $search=$request->get("search");
        $search="0";
//        $a=$Category->where("cat_id","not in","0")->select()->count();
//        echo $a;
        if($search==""){
            $result=$Category->sellist();
        }else{
            $result=$Category->seler($search);
        }
        print_r($result);
    }
    public function se(){
        $Category = new CategoryModel;
//        var_dump($Category->table);
        $result = $Category->sel();
        dump($result);

    }

    public function le(){
        $Category = new CategoryModel;
        $Category->check();

    }

    public function bact(){
        return $this->fetch();
    }

    public function getcontent(){
        var_dump($_POST);
    }

    public function getimage(Request $request){

        $info =  array(
            "state" => 0,
            "url" => "11",
            "title" => 'fhwek',
            "original" => "alkwehf",
            "type" => "jpg",
            "size" => "ewalfjwe",
            "file" => $_FILES,
        );
        return json_encode($info);
    }
}