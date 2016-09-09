<?php
namespace Logic;
use Logic\BaseLogic;

class FamilyIncomeLogic extends BaseLogic
{
    public static $exportFields = array('star_id' => '主播ID', 'nickname' => '主播昵称','name'=>'姓名', 'coins'=>'收获打赏星愿(月份内)', 'expected_income'=> '预期结算收入(元)','coins_created_at'=>'主播当月最后收入时间','sum_income'=>'家族累计结算收入',
    );

    public function getFamilyStarIncomeList($where,$livewhere) {
        $coins_consume_model = model('coins_consume');
        //获取家族主播id
        $star_family_model = model('StarFamily');
        $where['family_id'] = session('user_id');
        $star_info = $star_family_model->getList($where,$order='created_at asc',$limit=20);
        if($star_info === false) {
            $this->errorMessage = '家族主播信息获取失败';
            return false;
        }
        if(empty($star_info['data'])) return ;
        $star_infos = array_key_translate($star_info['data'],'star_id');
        $star_ids = array_keys($star_infos);
        //主播昵称
        $user_model = model('User');
        $user_info = $user_model->getUserInfobyIds($star_ids,'id,nickname');
        if($user_info === false) {
            $this->errorMessage = '用户信息获取失败';
            return false;
        }
        if(!empty($user_info)) {
            $user_info = array_key_translate($user_info,'id');
        }
        //主播星愿
        $star_coins = $coins_consume_model->field('id,star_id,SUM(count) as count,created_at')->where($livewhere)->where(array('star_id'=>array('in',$star_ids)))->group('star_id')->select();
        if($star_coins === false) {
            $this->errorMessage = '主播星愿获取失败';
            return false;
        }
        if(!empty($star_coins)) {
            $star_coins = array_key_translate($star_coins,'star_id');
        }
        //家族分成比例
        $family_model = model('Family');
        $family_info = $family_model ->field('id,bonus')->where(array('id'=>session('user_id')))->find();
        if($family_info === false) {
            $this->errorMessage ='家族信息获取失败';
            return false;
        }
        $family_bonus = $family_info['bonus'];
        $sum_income = array();
        foreach($star_infos as $star_id =>&$val) {
            $val['nickname'] = $user_info[$star_id]['nickname'];
            $val['coins'] = $star_coins[$star_id] ? $star_coins[$star_id]['count'] : 0;
            $sum_income[] = $val['expected_income'] = $star_coins[$star_id] ? $star_coins[$star_id]['count']/10*$family_bonus/100 : 0;
            $val['coins_created_at'] = ($star_coins[$star_id]['created_at'] == '') ? 0 : $star_coins[$star_id]['created_at'];
        }
        return array(
            'data' => $star_infos,
            'count' => $star_info['count'],
            'page' => $star_info['page'],
            'cur_page' => $star_info['cur_page'],
            'sum_income' => array_sum($sum_income)
        );
    }


    public function parseExportData($export_fields,$data,$sum_income) {
        $result = array();
        foreach ($data as $income) {
            $row = array();
            foreach ($export_fields as $field => $name) {
                $row[$field] = $this->_parseFamilyIncomeVal($field, $income,$sum_income);
            }
            $result[] = $row;
        }
        return $result;
    }

    private function _parseFamilyIncomeVal($field, $income,$sum_income){
        switch ($field) {
            case 'expected_income':
                $val = number_format($income[$field],2);
                break;
            case 'sum_income':
                $val = number_format($sum_income,2);
                break;
            case 'coins_created_at':
                $val = $income[$field] != '0000-00-00' ? date('Y-m-d', $income[$field]) : ' ';
                break;
            default:
                $val = $income[$field];
                break;
        }
        return $val;
    }
}