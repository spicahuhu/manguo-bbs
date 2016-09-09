<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2016/8/23
 * Time: 19:40
 */
namespace Home\Model;
use Home\Model\BaseModel;

class CoinsModel extends BaseModel{
    protected $tableName = 'coins';

    public function getUserCoinsByIds($user_id,$field=true) {
        if(empty($user_id)) return false;
        return $this->field($field)->where(array('user_id'=>array('IN',$user_id)))->select();
    }

}