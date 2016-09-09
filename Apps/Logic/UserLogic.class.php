<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2016/8/26
 * Time: 20:04
 */
namespace Logic;
use Logic\BaseLogic;

class UserLogic extends BaseLogic{

    public function changePwd($id,$initpwd,$pwd) {
        if(empty($id) || empty($pwd)|| empty($initpwd)) {
            $this->errorMessage = '参数不合法';
            return false;
        }
        $model = model('admin_family_login');
        $family_login_info = $model->where(array('id'=>$id))->find();
        if($family_login_info === false) {
            $this->errorMessage='家族登陆信息获取失败';
            return false;
        }
        if(empty($family_login_info)) {
            $this->errorMessage = '家族登陆记录不存在';
            return false;
        }
        if($family_login_info['try_times']>C('ALLOW_TRY_ERROR_TIMES')) {
            $this->errorMessage = '密码输入错误次数过多，账号已禁用，请联系管理员';
            return false;
        }
        $initpwd = md5(md5($initpwd)+$family_login_info['code']);
        if($initpwd != $family_login_info['password']) {
            $this->errorMessage = '初始密码输入错误';
            $model->where(array('id'=>$id))->save(array('try_times'=>array('exp','`try_times`+1')));
            return false;
        }
        $newpassword = md5(md5($pwd)+$family_login_info['code']);
        $res = $model->where(array('id'=>$id))->save(array('password'=>$newpassword));
        if($res === false) {
            $this->errorMessage = '密码设置失败';
            return false;
        }
        return true;
    }

}