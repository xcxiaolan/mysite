<?php
namespace app\Home\controller;

use \think\Db;
use think\facade\Session;
use \think\Request;
use \think\Controller;
class Base extends \think\Controller
{
    public function __construct()
    {
        parent::__construct(); // TODO: Change the autogenerated stub
        $this->assign("public","../../../../static");//静态文件夹路径
        $this->assign(["a"=>"","b"=>"","c"=>"","d"=>"","e"=>"",]);
        $this->assign(["a1"=>"","b1"=>"","c1"=>"","d1"=>"","e1"=>"","f1"=>"","i1"=>""]);
        $this->assign("name",Session::get('name'));
    }

}