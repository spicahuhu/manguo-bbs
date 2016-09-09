<?php
namespace Logic;
use Logic\BaseLogic;

class FamilyLoginLogic extends BaseLogic{

    public function loginVerify($name,$pwd,$verifyImg) {
        if(empty($name) || empty($pwd) || empty($verifyImg)){
            $this->errorMessage = '用户名|密码|验证码都必须！';
            return false;
        }
        if(session('verify') != md5($verifyImg)) {
            $this->errorMessage = '验证码错误！';
            return false;
        }
        $model = model('FamilyLogin');
        $data = $model->where(array('account'=>$name))->find();
        $allow_try_error_times = C('ALLOW_TRY_ERROR_TIMES',null,5);
        if($data['try_times']>=$allow_try_error_times) {
            $this->errorMessage = '登录失败次数过多，帐号已被禁用，请与管理员联系！';
            return false;
        }
        if(empty($data)) {
            $this->errorMessage = '账号不存在';
            return false;
        }
        $pwd = md5(md5($pwd)+$data['code']);
        if($pwd != $data['password']) {
            $this->errorMessage = '密码错误！你还有'.($allow_try_error_times-$data['try_times']-1).'次尝试机会。';
            //��¼��½����
            $model->where(array('id'=>$data['id']))->save(array('try_times'=>array('exp','`try_times`+1')));
            return false;
        }
        $sql_data = array(
            'last_login_time' =>time(),
            'last_login_ip'=>get_client_ip(),
            'try_times'=>0,
        );
        $res = $model->where(array('id'=>$data['id']))->save($sql_data);
        if($res === false) {
            $this->errorMessage = '账号信息获取失败';
            return false;
        }
        session('user_id',$data['id']);
        session('user_name',$data['account']);
        return true;
    }

}