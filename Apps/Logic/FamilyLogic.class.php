<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2016/8/23
 * Time: 17:41
 */
namespace Logic;
use Logic\BaseLogic;

class FamilyLogic extends BaseLogic{
    public static $bankFields = array(
        '1'=>'中国工商银行','2'=>'中国建设银行','3'=>'中国农业银行','4'=>'中国银行','5'=>'招商银行','6'=>'中信银行','7'=>'浦发银行','8'=>'交通银行','9'=>'民生银行','10'=>'光大银行',
    );

    Public function getFamilyInfo($id) {
        if(empty(id)) {
            $this->errorMessage = '参数不合法';
            return false;
        }
        $model = model('Family');
        $data = $model->where(array('id'=>$id))->find();
        if($data === false) {
            $this->errorMessage = '家族数据获取失败';
            return false;
        }
        //家族主播数
        if(!empty($data)){
            $star_family_model = model('StarFamily');
            $star_info = $star_family_model->getStarByFamilyId($id,'star_id');
            if($star_info === false) {
                $this->errorMessage = '家族主播数据获取失败';
                return false;
            }
            $data['count'] = (empty($star_info)) ? 0 : count(array_column($star_info,'star_id'));
        }
        $data['bonus'] = $data['bonus'].'%';
        return $data;
    }

    public function editFamily($info) {
        if(empty($info['id'])|| empty($info['name'])|| empty($info['patriarch_name'])|| empty($info['patriarch_phone'])|| empty($info['bonus'])|| empty($info['bank_account'])|| empty($info['bank_user'])|| empty($info['bank_name'])|| empty($info['alipay_id'])|| empty($info['alipay_name'])) {
            $this->errorMessage = '参数不合法';
            return false;
        }
        $info['updated_at'] = time();
        $info['operator'] = session('user_name');
        $model = model('Family');
        $res = $model->where(array('id'=>$info['id']))->save($info);
        if($res === false) {
            $this->errorMessage = '编辑失败';
            return false;
        }
        return true;
    }

    /**
     * 获取家族主播信息
     * @param $where
     * @return bool|void
     */
    public function getStarList($where){
        $star_family_model = model('StarFamily');
        $where['family_id'] = session('user_id');
        $star_info = $star_family_model->getStarInfo($where);
        if($star_info === false) {
            $this->errorMessage = '家族主播信息获取失败';
            return false;
        }
        if(empty($star_info)) return ;
        $star_info = array_key_translate($star_info,'star_id');
        $star_ids = array_column($star_info,'star_id');
        //昵称电话
        $user_model = model('User');
        $user_info = $user_model->getUserInfobyIds($star_ids,'id,nickname,phone');
        if($user_info === false) {
            $this->errorMessage = '用户信息获取失败';
            return false;
        }
        if(!empty($user_info)) {
            $user_info = array_key_translate($user_info,'id');
        }
        //星愿值
        $coins_model = model('Coins');
        $coins = $coins_model->getUserCoinsByIds($star_ids);
        if($coins === false) {
            $this->errorMessage = '用户星愿获取失败';
            return false;
        }
        if(!empty($coins)) {
            $coins = array_key_translate($coins,'user_id');
        }
        //粉丝数
        $user_fans_model = model('user_fans');
        $user_fans = $user_fans_model->field('user_id,count(fans_id) as count_fans')->where(array('user_id'=>array('in',$star_ids)))->group('user_id')->select();
        if($user_fans ===false) {
            $this->errorMessage = '粉丝数获取失败';
            return false;
        }
        if(!empty($user_fans)) {
            $user_fans = array_key_translate($user_fans,'user_id');
        }
        //当月有效直播时长
        $live_model = model('Live');
        $date = date('Y-m-01',time());
        $live_data = $live_model->field('star_id,SUM(start_time) as start,SUM(end_time) as end')->where(array('star_id'=>array('in',$star_ids),'is_deleted'=>0,'start_time'=>array('egt',strtotime($date))))->group('star_id')->select();
        if($live_data === false) {
            $this->errorMessage = '当月有效直播时长获取失败';
            return false;
        }
        if(!empty($live_data)) {
            $live_data = array_key_translate($live_data,'star_id');
        }
        //等级
        $user_level_model = model('user_level');
        $user_level = $user_level_model->field('user_id,experiences,level_id')->where(array('user_id'=>array('in',$star_ids)))->select();
        if($user_level === false) {
            $this->errorMessage = '等级经验获取失败';
            return false;
        }
        if(!empty($user_level)) {
            $user_level = array_key_translate($user_level,'user_id');
        }
        if(!empty($user_level)) {
            $level_ids = array_column($user_level,'level_id');
            foreach($level_ids as $val) {
                if($val == 0) continue;
                $level_model = model('user_level_rule');
                $level = $level_model->field('id,title')->where(array('id'=>$val))->select();
                if($level == false) {
                    $this->errorMessage = '等级获取失败';
                    return false;
                }
                $level = array_key_translate($level);
            }

        }
        //是否禁播
        $star_model = model('star');
        $star_data = $star_model ->field('id,status,forbided_end_at')->where(array('id'=>array('in',$star_ids)))->select();
        if($star_data === false) {
            $this->errorMessage = '禁播状态获取失败';
            return false;
        }
        if(!empty($star_data)) {
            $star_data = array_key_translate($star_data,'id');
        }
        foreach($star_info as $star_id=>&$val) {
            $val['nickname'] = $user_info[$star_id]['nickname'];
            $val['phone'] = $user_info[$star_id]['phone'];
            $val['coins'] = $coins[$star_id]['income'] -$coins[$star_id]['cashout'];
            $val['fans'] = ($user_fans[$star_id]['count_fans']) ? $user_fans[$star_id]['count_fans'] : 0;
            $val['level'] = $level[$user_level[$star_id]['level_id']]['title'];
            $val['live_time'] = gmstrftime('%H:%M:%S',($live_data[$star_id]['end'] - $live_data[$star_id]['start']));
            $val['status'] = ($star_data[$star_id]['status'] == 0) ? '-1' : (time()>$star_data[$star_id]['forbided_end_at']) ? 0 : round(($star_data[$star_id]['forbided_end_at']-time())/86400);
        }
        return $star_info;
    }

    /**
     * 编辑主播信息
     * @param $star_id
     * @param $name
     * @param $phone
     * @param $sex
     * @return bool
     */
    public function editStars($family_id,$star_id,$name,$phone,$sex) {
        if(empty($star_id) || empty($family_id) || empty($name) || empty($phone) ||!in_array($sex,array(0,1,2))) {
            $this->errorMessage = '参数不合法';
            return false;
        }
        $model = model('StarFamily');
        $sql_data = array(
            'name'=>$name,
            'sex' =>$sex,
            'updated_at' => time(),
            'operator' => session('user_name')
        );
        $res = $model->where(array('family_id'=>$family_id,'star_id'=>$star_id))->save($sql_data);
        if($res === false){
            $this->errorMessage = '主播编辑失败！';
            return false;
        }
        //电话是否更改

        return true;
    }
}