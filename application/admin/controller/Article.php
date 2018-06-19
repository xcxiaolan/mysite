<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/14
 * Time: 15:10
 */
namespace app\Admin\controller;

use think\facade\Session as Session;
use think\Request;
use app\admin\model\Article as ArticleModel;

class Article extends Base
{
    protected $num=10;
    //文章列表页面
    public function articlist(){
        $article=new ArticleModel;
        $list=$article->sellist();
        for($i=0;$i<count($list);$i++){
            $list[$i]["art_addtime"]=date("Y-m-d H:i:s",$list[$i]["art_addtime"]);
        }
        $cate=$article->selin();
        //$pagelist=$this->page(1);//分页的页码
        $this->assign("list",$list);
        $this->assign("cate",$cate);
        //$this->assign("pageNum",$pagelist);
        return $this->fetch();
    }
    //文章分页
    public function articpage(){

    }
    //文章查询 ajax
    public function articajax(){

    }
    //文章查询分页
    public function artajpage(){

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

    //文章添加页面
    public function artadd(){
        $article=new ArticleModel;
        $cate=$article->selin();
        $this->assign("cate",$cate);
        return $this->fetch();
    }
    //文章添加
    public function add(Request $request){
        $num=$this->maxid();
        $name=$request->post("name");
        if($name==""){
            echo "<script>alert('文章名字不能为空！');window.location.href='artadd'</script>";
        }
        if(!$this->usename($name)){
            echo "<script>alert('文章名字已被使用！');window.location.href='artadd'</script>";
        }
        $pid=$request->post("pid");
        $catid=$request->post("catid");
        if($pid==""){
            echo "<script>alert('文章所属类别不能为空！');window.location.href='artadd'</script>";
        }
        $content=$request->post("content");
        if($content==""){
            echo "<script>alert('文章内容不能为空！');window.location.href='artadd'</script>";
        }
        $article=new ArticleModel;
        $article->data(["art_id"=>$num,"art_catid"=>$catid,"art_name"=>$name,"art_addtime"=>time(),"art_content"=>$content]);
        $result=$article->save();
        if($result){
            echo "<script>alert('文章新增成功！');window.location.href='artadd'</script>";
        }else{
            echo "<script>alert('文章新增失败！');window.location.href='artadd'</script>";
        }
    }

    //修改文章页面
    public function artupdate(Request $request){
        $id=$request->get("id");
        $article=new ArticleModel;
        $list=$article->where(["art_id"=>$id])->find();
        print_r($list);
        $cate=$article->selin();
        $this->assign("list",$list);
        $this->assign("cate",$cate);
        return $this->fetch();
    }
    //修改操作
    public function update(Request $request){
        $id=$request->post("id");
        $name=$request->post("name");
        $usname=$request->post("usname");
        $pid=$request->post("pid");
        $content=$request->post("content");
        if($this->usename($name,$usname)){
            $list=["art_name"=>$name,"art_catid"=>$pid,"art_content"=>$content];
            $article=new ArticleModel;
            $result=$article->where(["art_id"=>$id])->update($list);
            if($result){
                echo "<script>alert('文章更新成功！');window.location.href='articlist'</script>";
            }else{
                echo "<script>alert('文章更新失败！');window.location.href='artupdate?id=$id'</script>";
            }
        }else{
            echo "<script>alert('文章名称已被使用，请更换！');window.location.href='artupdate?id=$id'</script>";
        }
    }
    //修改文章状态
    public function upstate(Request $request){
        $article=new ArticleModel;
        $id=$request->get("id");
        $state=$request->get("state");
        if($state == "1"){
            $result=$article->where("art_id",$id)->update(["art_state"=>"0"]);
        }else{
            $result=$article->where("art_id",$id)->update(["art_state"=>"1"]);
        }
        return $result;
    }

    //删除文章操作
    public function delete(Request $request){
        $article=new ArticleModel;
        $id=$request->post("id");
        $result=$article->where(["art_id"=>$id])->delete();
        echo $result;
    }
    //获取数据库表中最大的ID下一位
    public function maxid(){
        $article=new ArticleModel;
        $num=$article->limit(0,1)->order("art_id","desc")->column("art_id");
        return $num["0"]+1;
    }
    //名字是否可以使用
    public function usename($name,$usname=""){
        if($name==$usname){
            return true;
        }else{
            $article=new ArticleModel;
            $list=$article->column("art_name");
            $num=0;
            for($i=0;$i<count($list);$i++){
                if($list[$i]!=$name){
                    $num=$num+1;
                }
            }
            if(count($list)==$num){
                return true;
            }else{
                return false;
            }
        }
    }
    public function arck(){
        $article=new ArticleModel;
        $list=$article->selin();
        $this->assign("list",$list);
        return $this->fetch();
    }
    public function chaxun(Request $request){
        $pid=$request->get("pid");
        $article=new ArticleModel;
        $list=$article->selin("$pid");
        echo json_encode($list,JSON_UNESCAPED_UNICODE);
    }
    public function actk(Request $request){
        $pid=$request->post("pid");
        $catid=$request->post("catid");
        if(!$catid==""){
            echo $catid;
        }else{
            echo $pid;
        }
    }
}