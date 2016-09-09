<?php
namespace Home\Controller;
use Logic\FamilyLogic;
use Think\Controller;

class IndexController extends BaseController {
    public function index(){
        $logic = logic('Family');
        $family_id = session('user_id');
        $data = $logic->getFamilyInfo($family_id);
        if($data === false) {
            $this->error($logic->getErrorMessage());
        }
        $banks = FamilyLogic::$bankFields;
        $this->assign('banks',$banks);
        $this->assign('data',$data);
        $this->display('Index/main');
    }

    public function left() {
        $this->display();
    }


    public function editMain() {
        $logic = logic('Family');
        if(IS_POST) {
            $info = I('post.info');
            $res = $logic->editFamily($info);
            if($res === false) {
                $this->error($logic->getErrorMessage());
            }
            $this->success('编辑成功',U('/index'));
        }else{
            $family_id = session('user_id');
            $data = $logic->getFamilyInfo($family_id);
            if($data === false) {
                $this->error($logic->getErrorMessage());
            }
            $banks = FamilyLogic::$bankFields;
            $this->assign('banks',$banks);
            $this->assign('data',$data);
            $this->display('Index/edit');
        }
    }

    public function changePwd() {
        if(IS_POST) {
            $id  = I('id');
            $initpwd = I('initpwd');
            $password = I('password');
            $repassword = I('re_pwd');
            if($password !=$repassword) {
                $this->error('两次密码输入不一致');
            }
            $logic = logic('User');
            $res = $logic->changePwd($id,$initpwd,$password);
            if($res === false) {
                $this->error($logic->getErrorMessage());
            }else{
                $this->success('密码更新成功',U('/index'));
            }
        }else{
            $logindata = M('admin_family_login')->field('id,account,last_login_time,last_login_ip')->where(array('id'=>session('user_id')))->find();
            if($logindata === false){
                $this->error('家族登录信息获取失败');
            }
            $this->assign('logindata',$logindata);
            $this->display();
        }

    }
}