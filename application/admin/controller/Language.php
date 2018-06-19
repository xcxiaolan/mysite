<?php
namespace app\admin\controller;

use think\Request;
use think\facade\Session as Session;
use think\Db;
use app\admin\model\Language as languageModel;

class Language extends Base
{
    //留言列表  $sum 总记录数 $num 每页显示记录数 $pagecount 总页数 $start 起始位置
    public function lelist(){
        $language=new languageModel;
        $num=5;
        $page = 1;
        $list=$language->limit(0,$num)->select();
        for($i=0;$i<count($list);$i++){
            $addtime=$list[$i]["lang_addtime"];
            $list[$i]["lang_addtime"]=date("Y-m-d H:i:s",$addtime);
        }
        $sum=$language->select()->count();
        $pagecount=ceil($sum/$num);
        $pagelist=$this->page($pagecount);//分页的页码
        $this->assign("pageNum",$pagelist);
        $this->assign("list",$list);
        return $this->fetch();
    }
    //分页列表
    public function letable(Request $request){
        $language=new languageModel;
        $num=5;
        $page = $request->post("page");
        $start=($page-1)*$num;
        $list=$language->limit($start,$num)->select();
        for($i=0;$i<count($list);$i++){
            $addtime=$list[$i]["lang_addtime"];
            $list[$i]["lang_addtime"]=date("Y-m-d H:i:s",$addtime);
        }
        echo $list;
    }
    //留言查询操作
    public function listajax(Request $request){
        $language=new languageModel;
        $search=$request->post("search");
        Session::set("seaName",$search);
        $num=5;
        $list=$language->where('lang_name',"like","%$search%")->limit(0,$num)->select();
        for($i=0;$i<count($list);$i++){
            $addtime=$list[$i]["lang_addtime"];
            $list[$i]["lang_addtime"]=date("Y-m-d H:i:s",$addtime);
        }
        $sum=$language->where('lang_name',"like","%$search%")->select()->count();
        $pagecount=ceil($sum/$num);
        $pagelist=$this->page($pagecount);//分页的页码
        $data = [$list,$pagelist];
        echo json_encode($data);

    }
    public function langlist(Request $request){
        $language=new languageModel;
        $search=Session::get("seaName");
        $num=5;
        $page = $request->post("page");
        $start=($page-1)*$num;
        $list=$language->where('lang_name',"like","%$search%")->limit($start,$num)->select();
        for($i=0;$i<count($list);$i++){
            $addtime=$list[$i]["lang_addtime"];
            $list[$i]["lang_addtime"]=date("Y-m-d H:i:s",$addtime);
        }
        echo $list;
    }
    //修改状态操作
    public function upstate(Request $request){
        $language=new languageModel;
        $id=$request->get("id");
        $state=$request->get("state");
        if($state == "1"){
            $list=$language->where("lang_id",$id)->update(["lang_state"=>0]);
        }else{
            $list=$language->where("lang_id",$id)->update(["lang_state"=>1]);
        }
        echo $list;
    }
    //删除留言
    public function delect(Request $request){
        $language=new languageModel;
        $id=$request->post("id");
        $list=$language->where(["lang_id"=>$id])->delete();
        echo $list;
    }
    //修改留言页面
    public function update(Request $request){
        $id=$request->get("id");
        $language=new languageModel;
        $list=$language->where("lang_id",$id)->select();
        $this->assign("list",$list);
        return $this->fetch();
    }
    //修改用户操作
    public function aupdate(Request $request){
        $language=new languageModel;
        $id=$request->post("id");
        $usname=$request->post("usname");
        $name=$request->post("name");
        $uspwd=$request->post("uspwd");
        $password=$request->post("password");
        $sex=$request->post("sex");
        $age=$request->post("age");
        $email=$request->post("email");
//        $data = $request->post();
//        $rule = [
//            'name'  => 'require|max:25',
//            'email' => 'email'
//        ];
//        $validate = new Validate($rule);
//        if (!$validate->check($data)) {
//              dump($validate->getError());
//        }
        if($password == ""){
            $list=["user_name"=>$name,"user_pwd"=>$uspwd,"user_sex"=>$sex,"user_age"=>$age,"user_email"=>$email];
        }else{
            $list=["user_name"=>$name,"user_pwd"=>$password,"user_sex"=>$sex,"user_age"=>$age,"user_email"=>$email];
        }
        if($this->daname($usname,$name)){
            $a=$language->where(['user_id'=>$id])->update($list);
            if($a){
                echo "<script>alert('更新成功！');window.location.href='uslist'</script>";
            }
        }else{
            echo "<script>alert('用户名已存在，请更换');window.location.href='update?id=$id'</script>";
        }
    }
    //检查修改的用户名是否和数据库中其他用户名重名
    //$usname是修改前的用户名,$name是修改后的用户名
    public function daname($usname,$name){
        $language=new languageModel;
        if($usname==$name){
            return true;
        }else{
            $list=$language->where("lang_name","not in",$usname)->column("lang_name");
            for($i=0;$i<count($list);$i++){
                if($list[$i]==$name){
                    return false;
                }else{
                    return true;
                }
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
}