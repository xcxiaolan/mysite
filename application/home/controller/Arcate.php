<?php
namespace app\Home\controller;

use \think\Db;
use think\facade\Session;
use think\Request;
use app\Home\model\User as Homu;
class Arcate extends Base
{
    //文章列表
    public function arcate(){
        $this->assign("b","active");
        return $this->fetch();
    }
    //单一文章页面
    public function single(){
        return $this->fetch();
    }
}