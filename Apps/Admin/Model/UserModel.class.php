<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2016/8/23
 * Time: 19:29
 */
namespace Home\Model;
use Home\Model\BaseModel;

class UserModel extends BaseModel{
    protected $tableName = 'user';

    public function searchUserInfoByNickName($nickname,$field=true){
        if(empty($nickname)) return false;
        return $this->field($field)->where(array('nickname'=>array('like',$nickname.'%')))->select();
    }


    public function getUserInfoByIds($ids,$field=true) {
        if(empty($ids)) return false;
        return $this->field($field)->where(array('id'=>array('in',$ids)))->select();
    }

}