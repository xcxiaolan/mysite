<?php
namespace app\home\model;

use \think\Model;
class User extends Model
{
    public function login($account,$password){
        $user = user::get(['user_account' => $account,'user_pwd'=>$password]);
        return $user;
    }
    public function upda($name,$password,$id){
        $user = new user;
        $a=$user->save(["user_name"=>$name,"user_pwd"=>$password,"user_addtime"=>time()],["user_id"=>$id]);
        echo $a;
    }
    //注册用户
    public function registration($name,$password,$email){
        $user=new user;
        $user->data([
            'user_name'  =>  $name,
            'user_pwd'  =>  $password,
            'user_email' =>  $email,
            'user_addtime' =>  time()
        ]);
        Session::set("name",$name);
        return $user->save();
    }
    //把用户名写入文件
    public function ca($name){
        $handle=fopen("1.txt","a");
        fwrite($handle,$name);
        fwrite($handle,"\n");
        fclose($handle);
    }
    //判断用户名是否存在
    public function isstr($name){
        $c=$name."\n";
        $a=file("1.txt");
        for($i=0;$i<count($a);$i++){
            if($a[$i]==$c){
                echo "true";
            }
        }
    }


}