<?php
namespace app\Home\controller;

use \think\Db;
use think\facade\Session;
use think\Request;
use app\Home\model\User as Homu;
class Home extends Base
{
    //首页
    public function index(){
        $this->assign("a1","active");
        return $this->fetch();
    }
    //注册
    public function registration(){
        $this->assign("a","active");
        return $this->fetch();
    }
    //关于我
    public function about(){
        $this->assign("b1","active");
        return $this->fetch();
    }
    //服务
    public function services(){
        $this->assign("d1","active");
        $this->assign("c","active");
        return $this->fetch();
    }
    //登录
    public function login(){
        $this->assign("c1","active");
        $this->assign("d","active");
        return $this->fetch();
    }
    //更新页面
    public function update(){
        $this->assign("i1","active");
        return $this->fetch();
    }
    //联系我们
    public function contact(){
        $this->assign("f1","active");
        $this->assign("e","active");
        return $this->fetch();
    }
    //画廊
    public function gallery(){
        $this->assign("e1","active");
        return $this->fetch();
    }
    //猴子选王
    public function acr(){
        return $this->fetch();
    }

    //注册操作
    public function regis(){
        $name=trim($_POST["name"]);
        $email=trim($_POST["email"]);
        $password=trim($_POST["password"]);
        $model=new Homu();
        if($this->ba($name)){
            if($model->registration($name,$password,$email)){
                echo "<script>alert('注册成功！');window.location.href='login'</script>";
            }else{
                echo "<script>alert('注册失败！');window.location.href='registration'</script>";
            }
        }else{
            echo "<script>alert('用户名已存在！');window.location.href='registration'</script>";
        }

    }
    //登录操作
    public function logining(){
        $account=@trim($_POST["account"]);
        $password=@trim($_POST["password"]);
        if($account=="" || $password==""){
            echo "<script>alert('用户名或密码不能为空!');window.location.href='login'</script>";
        }else{
            $model=new Homu();
            if($user=$model->login($account,$password)){
                Session::set("name",$user["user_name"]);
                Session::set("id",$user["user_id"]);
                echo "<script>alert('登录成功！');window.location.href='index'</script>";
            }else{
                echo "<script>alert('用户名或密码错误！');window.location.href='login'</script>";
            }
        }
    }
    //更新操作
    public function ajaxUpdate(Request $request){
        $name1=Session::get('name');
        $name=$request->post('name');
        $password=$request->post('password');
        if($name!='' && $password!='')
        {
            $id=Session::get('id');
            $model=new Homu();
            if($this->ba($name,$name1)){
                $result=$model->save(["user_name"=>$name,"user_pwd"=>$password,"user_addtime"=>time()],["user_id"=>$id]);
                if($result)
                {
                    Session::set("name",$name);
                    echo "更新成功";
                }else{
                    echo "更新失败";
                }
            }else{
                echo "用户名已存在";
            }

        }
        else{
            echo '用户名或密码没有输入';
       }
    }
    //退出登录
    public function exitLogon(){
        Session::delete("name");
        echo "<script>alert('退出成功！');window.location.href='login'</script>";
        //$this->success("退出成功","Home/index");
    }
    //联系我们
    public function contactUs(Request $request){
        $content=$request->post('content');
        $id=session::get("id");
        $data=["con_userid"=>$id,"con_content"=>$content];
        $result=Db::table("contact")->insert($data);
        if($result){
            echo "<script>alert('提交成功!');window.location.href='contact'</script>";
        }else{
            echo "<script>alert('提交失败!');window.location.href='contact'</script>";
        }
    }
    public function file(){
        $dir="../";
        $a= is_dir($dir);
        var_dump($a);
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    echo "filename: $file : filetype: " . filetype($dir . $file) . "<br/>";
                }
                closedir($dh);
            }
        }
    }
    //把用户名写入文件
    public function ca(){
        $a="cf";
        $handle=fopen("1.txt","a");
        fwrite($handle,$a);
        fwrite($handle,"\n");
        fclose($handle);
    }
    //获取数据库的值进行判断名字是否存在，存在为false，不存在为true
    public function ba($name,$name1){
        $a=Db::table("user")->where("user_name","in",[$name,$name1])->select();
        if(count($a)==1){
            return true;
        }else{
            return false;
        }
    }
    public function cr(){
        $name=$_POST["name"];
        if($this->ba($name)){
            echo "可以注册";
        }else{
            echo "用户名已存在";
        }
    }


}