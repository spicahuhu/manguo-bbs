<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2016/8/23
 * Time: 19:13
 */
namespace Home\Model;
use Home\Model\BaseModel;

class StarFamilyModel extends BaseModel{
    protected $tableName = 'admin_star_family';

    public function getStarByFamilyId($id,$field=true) {
        if(empty($id)) return false;
        return $this->field($field)->where(array('family_id'=>$id))->select();

    }

    public function getStarInfo($where,$field=true) {
        return $this->field($field)->where($where)->select();
    }

    public function searchStarInfoByName($name,$field=true) {
        return $this->field($field)->where(array('name'=>array('like',$name.'%')))->select();
    }

}