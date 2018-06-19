<?php
namespace app\Admin\controller;


use think\Request;
use think\facade\Session as Session;
use think\Db;
use think\Image;
use app\Admin\model\Admin as AdminModel;
use think\captcha\Captcha as Captcha;
class Admin extends Base
{
    //首页
    public function index(){
        $session = new Session();
        //if($session->has('adname')){
            return $this->fetch();
//        }else{
//            return $this->login();
//        }
    }
    //注册页面
    public function regi(){
        return $this->fetch();
    }
    //验证码
    public function captcha(){
        $cap = new Captcha();
        return $cap->entry();
    }
    public function black(){
        return $this->fetch();
    }
    public function buttons(){
        return $this->fetch();
    }
    public function forms(){
        return $this->fetch();
    }
    public function grid(){
        return $this->fetch();
    }
    public function icons(){
        return $this->fetch();
    }
    public function notifications(){
        return $this->fetch();
    }
    //排版页面
    public function panels(){
        return $this->fetch("panels-wells");
    }
    public function typography(){
        return $this->fetch();
    }
    //网站设置
    public function webset(){
        return $this->fetch();
    }
    //缩略图页面
    public function thumbset(){
        return $this->fetch();
    }
    //登录页面
    public function login(){
        return $this->fetch();
    }
    //添加管理员
    public function add(Request $request){
        $admin=new AdminModel;
        $name=$request->post("name");
        $password=$request->post("password");
        $sex=$request->post("sex");
        $age=$request->post("age");
        $file="/www/blog1/01.txt";
        $a=fopen($file,'w+');
        fclose($a);
        if($this->sdkname($name)){
            echo "<script>alert('用户名已存在，请更换！');window.location.href='regi'</script>";
        }
        $admin->data([
            "name"=>$name,
            "password"=>$password,
            "sex"=>$sex,
            "age"=>$age,
            "addtime"=>time()
        ]);
        if($admin->save()){
            $current=file_get_contents($file);
            $current.=$name."\n";
            file_put_contents($file,$current);
            echo "<script>alert('用户注册成功！');window.location.href='regi'</script>";
        }
    }
    //修改管理员页面
    public function update(Request $request){
        $id=$request->get("id");
        $admin=new AdminModel;
        $list=$admin->where("id",$id)->select();
        $this->assign("list",$list);
        return $this->fetch();
    }
    public function aupdate(Request $request){
        $admin=new AdminModel;
        $id=$request->post("id");
        $adname=$request->post("adname");
        $name=$request->post("name");
        $adpwd=$request->post("adpwd");
        $password=$request->post("password");
        $sex=$request->post("sex");
        $age=$request->post("age");
        if($password==""){
            $list=[["id"=>$id,"name"=>$name,"password"=>$adpwd,"sex"=>$sex,"age"=>$age]];
        }else{
            $list=[["id"=>$id,"name"=>$name,"password"=>$password,"sex"=>$sex,"age"=>$age]];
        }
        if($this->daname($adname,$name)){
            $a=$admin->saveAll($list);
            if($a){
                echo "<script>alert('更新成功！');window.location.href='alist'</script>";
            }
        }else{
            echo "<script>alert('用户名已存在，请更换');window.location.href='update?id=$id'</script>";
        }
    }
    public function logining(Request $request){
        $Captcha=new Captcha();
        $name=$request->post("name");
        $password=$request->post("password");
        $code=$request->post("code");
        $result=AdminModel::get(['name' => $name,'password'=>$password]);
        $ca=$Captcha->check($code);
        if($ca){
            if($result){
                Session::set("adname",$name);
                echo "<script>alert('登录成功');window.location.href='index'</script>";
            }else{
                echo "<script>alert('用户名或密码错误，请重试');window.location.href='login'</script>";
            }
        }else{
            echo "<script>alert('验证码错误，请重试');window.location.href='login'</script>";
        }

    }
    //退出账号
    public function Logout(){
        Session::delete("name");
        echo "<script>alert('退出成功');window.location.href='login'</script>";
    }
    //管理员列表  $sum 总记录数 $num 每页显示记录数 $pagecount 总页数 $start 起始位置
    public function alist(){
        $admin=new AdminModel;
        $num=5;
        $page = 1;
        $list=$admin->limit(0,$num)->select();
        for($i=0;$i<count($list);$i++){
            $addtime=$list[$i]["addtime"];
            $list[$i]["addtime"]=date("Y-m-d H:i:s",$addtime);
        }
        $sum=$admin->select()->count();
        $pagecount=ceil($sum/$num);
        $pagelist=$this->page($pagecount);//分页的页码
        $this->assign("pageNum",$pagelist);
        $this->assign("list",$list);
        return $this->fetch();
    }
    public function adtable(Request $request){
        $admin=new AdminModel;
        $num=5;
        $page = $request->post("page");
        $start=($page-1)*$num;
        $list=$admin->limit($start,$num)->select();
        for($i=0;$i<count($list);$i++){
            $addtime=$list[$i]["addtime"];
            $list[$i]["addtime"]=date("Y-m-d H:i:s",$addtime);
        }
        echo $list;
    }
    //管理员查询操作
    public function listajax(Request $request){
        $admin=new AdminModel;
        $search=$request->post("search");
        Session::set("seaName",$search);
        $num=5;
        $list=$admin->where('name',"like","%$search%")->limit(0,$num)->select();
        for($i=0;$i<count($list);$i++){
            $addtime=$list[$i]["addtime"];
            $list[$i]["addtime"]=date("Y-m-d H:i:s",$addtime);
        }
        $sum=$admin->where('name',"like","%$search%")->select()->count();
        $pagecount=ceil($sum/$num);
        $pagelist=$this->page($pagecount);//分页的页码
        $data = [$list,$pagelist];
        echo json_encode($data);

    }
    public function adlist(Request $request){
        $admin=new AdminModel;
        $search=Session::get("seaName");
        $num=5;
        $page = $request->post("page");
        $start=($page-1)*$num;
        $list=$admin->where('name',"like","%$search%")->limit($start,$num)->select();
        for($i=0;$i<count($list);$i++){
            $addtime=$list[$i]["addtime"];
            $list[$i]["addtime"]=date("Y-m-d H:i:s",$addtime);
        }
        echo $list;
    }
    //修改状态操作
    public function upstate(Request $request){
        $admin=new AdminModel;
        $id=$request->get("id");
        $state=$request->get("state");
        if($state == "1"){
            $list=$admin->where("id",$id)->update(["state"=>0]);
        }else{
            $list=$admin->where("id",$id)->update(["state"=>1]);
        }
        echo $list;
    }
    //删除管理员
    public function delect(Request $request){
        $admin=new AdminModel;
        $id=$request->post("id");
        $list=$admin->where(["id"=>$id])->delete();
        echo $list;
    }
    public function liandong(){
        $pcode="0";
        $ca=Db::query("select * from diqu where dfeilei={$pcode}");
        $this->assign("list",$ca);
        return $this->fetch();
    }
    public function lian(){
        $pcode=$_POST["pcode"];
        echo $pcode;
        $ca=Db::query("select * from diqu where dfeilei='{$pcode}'");
        echo $ca;
    }
    public function upload(){
        $fileName=$_FILES['image']['name'];//得到上传文件的名字
        $file=request()->file('image');
        $info=$file->move(ROOT_PATH."public/uploads");
        if($info){
            $date=date("Ymd");
            $img="/public/uploads/".$date."/".$info->getFilename();
            $this->assign("img",$img);
            $oldPath="/www/blog1/public/uploads/".$date."/".$info->getFilename();
            $newPath="/www/blog1/public/uploads/".$date."/".$fileName;
            rename(trim($oldPath),trim($newPath));
            //echo "<script>alert('上传成功');window.location.href='webset'</script>";
        }else{
            echo $file->getError();
            echo "<script>alert('上传失败');window.location.href='webset'</script>";
        }
    }
    public function rean(){
        $oldPath="/www/blog1/public/uploads/20180510/2727cd03e417fb33355896b047826cb5.png";
        $newPath="/www/blog1/public/uploads/20180510/13.jpg";
        if(rename(trim($oldPath),trim($newPath))){
            echo "success";
        }else{
            echo "defact";
        }
    }
    public function webseting(){//网站设置操作
        $requery=Request::instance();
        $arr=$requery->param();
        $uploaddir = '/www/blog1/public/uploads/';
        $uploadfile = $uploaddir . basename($_FILES['image']['name']);
        if(is_uploaded_file($_FILES['image']['tmp_name'])){
            if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)){
                $file="/public/uploads/".$_FILES['image']['name'];
                $arr["webimg"]=$_FILES["image"]["name"];
                $data=["web_name"=>$arr["webname"],"web_logo"=>$file,"web_word"=>$arr["webword"],"web_intro"=>$arr["webintro"]];
                Db::table("webset")->insert($data);
                $this->assign("file",$file);
                echo "<script>alert('上传成功');window.location.href='webset'</script>";
            }else{
                echo "Possible file upload attack!\n";
            }
        }else{
            echo "不是通过表单提交的";
        }
    }
    public function thumbseting(Request $request){//缩略图设置操作
        $width=$request->post("width");
        $height=$request->post("height");
        $shuiying=$request->post("shuiying");
        $image=\think\Image::open(request()->file("image"));
        if($shuiying==""){
            $ioc=$image->crop($width,$height,30,100)->save("./image/11.jpg");//切割一块区域
            //$ioc=$image->thumb($width,$height)->save("./image/44.jpg");//将原图等比例缩略
        }else{
            $ioc=$image->water('./favicon.ico',\think\image::WATER_SOUTHWEST,30)->save("./image/55.jpg");
        }
        dump($image);
        dump($ioc);
    }
    //检查数据库是否有该用户名
    public function sdkname($name){
        $admin=new AdminModel;
        $list=$admin->column("name");
        for($i=0;$i<count($list);$i++){
            if($list[$i]==$name){
                return true;
            }else{
                return false;
            }
        }
    }
    //检查修改的用户名是否和数据库中其他用户名重名
    //$adname是修改前的用户名,$name是修改后的用户名
    public function daname($adname,$name){
        $admin=new AdminModel;
        if($adname==$name){
            return true;
        }else{
            $list=$admin->where("name","not in",$adname)->column("name");
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
    public function canall(){
        $file=array();
        $uploaddir = '/www/blog1/public/uploads/';
        foreach($_FILES['image']['error'] as $key => $error){
            if($error == UPLOAD_ERR_OK){
                $name = $uploaddir.$_FILES['image']['name'][$key];
                $tmp_name=$_FILES['image']['tmp_name'][$key];
                move_uploaded_file($tmp_name,$name);
                $file[$key]="/public/uploads".$_FILES['image']['name'][$key];
                $this->assign("file".$key,$file[$key]);
            }
        }
        return $this->fetch("webset");
    }
    public function acb(){
        $data = ['name' => 'bar', 'age' => 'foo'];
        $a=Db::table('admin')->insert($data);
        $userId = Db::name('admin')->getLastInsID();
        echo $userId;
    }
    public function ca(){
        $a="/www/blog1/1.txt";
        $file=fopen($a,'w');
        $c="ca\n";
        fwrite($file,$c);
        $c="fk\n";
        fwrite($file,$c);
        fclose($file);
    }
    public function cd(){//字符串形式替换，成功
        $file="/www/blog1/2.php";
        $ac=file_get_contents($file);
        //print_r($ac);
        //dump($ac);
        $a=5;
        $v=10;
        $conf=str_replace('"page" => '.$a,'"page" => '.$v,$ac);
        //echo $conf;
        $b=file_put_contents($file,$conf);//成功返回写入文件的字节数，失败返回false
        //echo $b;
        if($b){
            echo "success";
        };
    }
    public function ce(){//数组形式替换,还不能自动搜索要替换的值
        $a=10;
        $file="/www/blog1/2.php";
        $ac=file($file);
        $ad=array();
        $ab=array("e"=>'        "page" => '.$a."\n");
        $aa=array();
        $c=array("a","b","c","d","e","f","g","h","i","j","k",);
        for($i=0;$i<count($ac);$i++){
            $ad[$c[$i]]=$ac[$i];
            $arr=array_merge($ad,$ab);
            for($j=$i;$j<=$i;$j++){
                $aa[$j]=$arr[$c[$j]];
            }
        }
        $b=file_put_contents($file,$aa);
        if($b){
            echo "success";
        };
    }
}