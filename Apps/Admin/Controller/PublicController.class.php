<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2016/8/22
 * Time: 17:41
 */
namespace Home\Controller;
use Think\Controller;
use Org\Util\Image as Image;

class PublicController extends Controller{

    public function login() {
        $this->display();
    }

    public function verify() {
        $type = I('type', 'gif');
        Image::buildImageVerify( 4, 5, $type);
    }

    public function checkLogin() {
        $name = I('account');
        $pwd = I('password');
        $verifyImg = I('verify');
        $logic = logic('FamilyLogin');
        $res = $logic->loginVerify($name,$pwd,$verifyImg);
        $request = I('request.redirect',U('/index'));
        if($res === false) {
            $this->ajaxReturn(array('code'=>10001,'message'=>$logic->getErrorMessage()));
        } else{
            $this->ajaxReturn(array('code'=>0,'message'=>'','data'=>array('redirect'=>$request)));
        }

    }

    public function loginOut() {
        session(null);
        session_destroy();
        unset($_COOKIE);
        //setcookie("user_name",'');
        $this->success('登出成功',U('/login'));
    }


}
