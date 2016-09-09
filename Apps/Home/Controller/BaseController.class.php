<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2016/8/22
 * Time: 16:03
 */
namespace Home\Controller;
use Think\Controller;

class BaseController extends Controller{

    function __construct(){
        parent::__construct();
       // $this->filterAuth();
    }

    // public function filterAuth() {
    //     if(!session('user_id')) {
    //         $this->error('请先登录',U('Public/login'));
    //     }
    // }


    protected function checkToken() {
        if (IS_POST) {
            if (!M()->autoCheckToken($_POST)) {
                $this->error('[hash]数据验证失败');
            }
        }
    }
}