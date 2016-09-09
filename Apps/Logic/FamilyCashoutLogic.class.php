<?php
namespace Logic;
use Logic\BaseLogic;

class FamilyCashoutLogic extends BaseLogic
{
    protected static $bankFields = array(
        '1'=>'中国工商银行','2'=>'中国建设银行','3'=>'中国农业银行','4'=>'中国银行','5'=>'招商银行','6'=>'中信银行','7'=>'浦发银行','8'=>'交通银行','9'=>'民生银行','10'=>'光大银行',
    );
    public static $exportFields = array(
        'id'=>'ID','withdraw_at'=>'提现时间','withdraw_money'=>'提现金额(元)','handler'=>'处理人','channel'=>'提现渠道','card'=>'卡号','card_user'=>'持有人',
    );

    public static $coinsDeductedFields = array(
        'id'=>'提现ID','star_id'=>'主播ID','name'=>'主播姓名','deductedCoins'=>'扣除星币','withdraw_at'=>'提现时间',
    );
    /**
     * 获取家族提现记录
     */
    public function getFamilyCashoutList() {
        $family_id = session('user_id'); if(empty($family_id)) return;
        $model = model('FamilyCashout');
        $where = array('family_id'=>$family_id);
        $data = $model->getList($where,$order='withdraw_at desc',$limit=20);
        if($data === false) {
            $this->errorMessage = '家族提现记录获取失败';
            return false;
        }
        if(empty($data['data'])) return ;
        $family_model = model('Family');
        $family_info = $family_model->field('id,bank_name,bonus,bank_user,alipay_name')->where(array('id'=>$family_id))->find();
        if($family_info === false) {
            $this->errorMessage = '家族信息查找失败';
            return false;
        }
        //家族主播ID
        $star_family_model = model('StarFamily');
        $star_info = $star_family_model->getStarByFamilyId($family_id,'star_id');
        if($star_info === false) {
            $this->errorMessage = '家族主播数据获取失败';
            return false;
        }
        if(!empty($star_info)) {
            $star_ids = array_column($star_info,'star_id');
            //可提现金额
            $coins_model = model('Coins');
            $sum_coins = $coins_model->field('SUM(income) as income,SUM(cashout) as cashout')->where(array('user_id'=>array('in',$star_ids)))->select();
            if($sum_coins === false) {
                $this->errorMessage = '星愿信息获取失败';
                return false;
            }
            $avaliable_coins = $sum_coins[0]['income'] - $sum_coins[0]['cashout'];
            $leave_money = $avaliable_coins>0 ? $avaliable_coins/10*$family_info['bonus']/100 : 0;
        }
        $banks = $this::$bankFields;
        foreach($data['data'] as $key=>&$val) {
            $val['channel'] = ($val['channel']==1) ? '支付宝' : ($banks[$family_info['bank_name']] ? $banks[$family_info['bank_name']] : $family_info['bank_name']);
            $val['card_user'] = ($val['channel']==1) ? $family_info['alipay_name'] : $family_info['bank_user'];
            $card_length = intval(strlen($val['card'])/2);
            $val['card'] = substr_replace($val['card'],'*****',$card_length-2,5);
        }
        return array(
            'data' => $data['data'],
            'leave_money' => $leave_money ? $leave_money : 0,
            'count' => $data['count'],
            'page' => $data['page'],
            'limit' => $limit,
            'cur_page' => $data['cur_page']
        );
    }

    /**
     * 家族提现记录导出
     * @return array|bool|void
     */
    public function getFamilyCashoutInfo() {
        $family_id = session('user_id');
        $model = model('FamilyCashout');
        $where = array('family_id'=>$family_id);
        $data = $model->where($where)->order('withdraw_at desc')->select();
        if($data === false) {
            $this->errorMessage = '家族提现记录获取失败';
            return false;
        }
        if(empty($data)) return ;
        $family_model = model('Family');
        $family_info = $family_model->field('id,bank_name,bonus,bank_user,alipay_name')->where(array('id'=>$family_id))->find();
        if($family_info === false) {
            $this->errorMessage = '家族信息查找失败';
            return false;
        }
        //家族主播ID
        $star_family_model = model('StarFamily');
        $star_info = $star_family_model->getStarByFamilyId($family_id,'star_id');
        if($star_info === false) {
            $this->errorMessage = '家族主播数据获取失败';
            return false;
        }
        if(!empty($star_info)) {
            $star_ids = array_column($star_info,'star_id');
            //可提现金额
            $coins_model = model('Coins');
            $sum_coins = $coins_model->field('SUM(income) as income,SUM(cashout) as cashout')->where(array('user_id'=>array('in',$star_ids)))->select();
            if($sum_coins === false) {
                $this->errorMessage = '星愿信息获取失败';
                return false;
            }
            $avaliable_coins = $sum_coins[0]['income'] - $sum_coins[0]['cashout'];
            $leave_money = $avaliable_coins>0 ? $avaliable_coins/10*$family_info['bonus']/100 : 0;
        }
        $banks = $this::$bankFields;
        foreach($data as $key=>&$val) {
            $val['channel'] = ($val['channel']==1) ? '支付宝' : ($banks[$family_info['bank_name']] ? $banks[$family_info['bank_name']] : $family_info['bank_name']);
            $val['card_user'] = ($val['channel']==1) ? $family_info['alipay_name'] : $family_info['bank_user'];
            $card_length = intval(strlen($val['card'])/2);
            $val['card'] = substr_replace($val['card'],'*****',$card_length-2,5);
        }
        return array(
            'data' => $data,
            'leave_money' => $leave_money ? $leave_money : 0,
        );
    }

    /**
     * 提现扣除星币
     * @param $id 订单ID
     */
    public function getDetailedCoinsDeducted($id) {
        if(empty($id)) {
            $this->errorMessage = '参数不合法';
            return false;
        }
        $model = model('FamilyCashout');
        $data = $model->where(array('id'=>$id))->find();
        if($data === false) {
            $this->errorMessage = '提现记录获取失败';
            return false;
        }
        if(empty($data)) return;
        //获取家族主播星愿
        $star_family_model = model('StarFamily');
        $stars = $star_family_model->field('family_id,star_id,name')->where(array('family_id'=>$data['family_id']))->select();
        if($stars === false) {
            $this->errorMessage = '家族主播信息获取失败';
            return false;
        }
        if(empty($stars)) return;
        $stars = array_key_translate($stars,'star_id');
//        $star_ids = array_column($stars,'star_id');
//        $coins_model = model('Coins');
//        $coins = $coins_model->getUserCoinsByIds($star_ids,'user_id,income,cashout');
//        if($coins === false) {
//            $this->errorMessage = '主播星愿获取失败';
//            return false;
//        }var_dump($coins);exit;
//        $coins = array_key_translate($coins,'user_id');
        $coins_cashout_model = model('coins_cashout');
        $star_cashout_info = $coins_cashout_model->field('id,target_id,star_id,coins,withdraw_at')->where(array('target_id'=>$data['family_id'],'withdraw_at'=>$data['withdraw_at']))->select();
        if($star_cashout_info ===false) {
            $this->errorMessage = '星愿提现记录获取失败';
            return false;
        }
        if(empty($star_cashout_info)) {
            $this->errorMessage = '主播提现记录为空';
            return false;
        }
        $star_cashout_info = array_key_translate($star_cashout_info,'star_id');
        foreach($star_cashout_info as $key=>&$val) {
            $val['deductedCoins'] = $val['coins'];
            $val['name'] = $stars[$key]['name'];
        }
        return $star_cashout_info;
    }

    public function parseExportData($export_fields,$data,$type='coinsdeduted') {
        $result = array();
        foreach ($data as $cashout) {
            $row = array();
            foreach ($export_fields as $field => $name) {
                $row[$field] = ($type == 'coinsdeduted') ? $this->_parseCoinsDedutedVal($field,$cashout) : $this->_parseFamilyCashoutVal($field, $cashout);
            }
            $result[] = $row;
        }
        return $result;
    }

    private function _parseCoinsDedutedVal($field,$cashout) {
        switch ($field) {
            case 'withdraw_at':
                $val = $cashout[$field] != '0000-00-00' ? date('Y-m-d H:i',$cashout[$field]) : '';
                break;
            default:
                $val = $cashout[$field];
                break;
        }
        return $val;
    }

    private function _parseFamilyCashoutVal($field, $cashout){
        switch ($field) {
            case 'withdraw_money':
                $val = number_format($cashout[$field],2);
                break;
            case 'withdraw_at':
                $val = $cashout[$field] != '0000-00-00' ? date('Y-m-d H:i', $cashout[$field]) : '';
                break;
            default:
                $val = $cashout[$field];
                break;
        }
        return $val;
    }


}