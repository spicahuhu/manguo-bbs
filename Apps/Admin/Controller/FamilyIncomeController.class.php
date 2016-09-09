<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2016/8/24
 * Time: 14:32
 */
namespace Home\Controller;
use Home\Controller\BaseController;
use Logic\FamilyIncomeLogic;
use Think\Page as Page;

class FamilyIncomeController extends BaseController{

    public function income() {
        $map = array();
        $star_id = trim(I('star_id'));
        if($star_id) {
            $map['star_id'][] = array('like',$star_id . '%');
        }
        $nickname = trim(I('nickname'));
        if($nickname) {
            $user_model = model('User');
            $user_info = $user_model->searchUserInfoByNickName($nickname,'id');
            if($user_info === false) {
                $this->error('主播名称获取失败');
            }
            if (empty($user_info)) {
                $map['star_id'] = '';
            } else {
                $map['star_id'][] = array('in',array_column($user_info,'id'));
            }
        }
        $name = trim(I('name'));
        if($name) {
            $star_family_model = model('StarFamily');
            $star_name = $star_family_model->searchStarInfoByName($name,'star_id');
            if($star_name === false) {
                $this->error('家族主播名称获取失败');
            }
            if(empty($star_name)){
                $map['star_id'] = '';
            }else {
                $map['star_id'][] = array('in',array_column($star_name,'star_id'));
            }
        }
        $month = trim(I('month'));
        $month = ($month) ? $month : date('Y-m-01',time());
        $nextmonth = strtotime('+1month',strtotime($month));
        $lives = array();
        $lives['created_at'] = array();
        $lives['created_at'][] = array('egt',strtotime($month));
        $lives['created_at'][] = array('lt',$nextmonth);

        $logic = logic('FamilyIncome');
        $data = $logic->getFamilyStarIncomeList($map,$lives);
        if($data === false) {
            $this->error($logic->getErrorMessage());
        }

        $this->assign('datas',$data['data']);
        $this->assign('sum_income',$data['sum_income']);
        $this->assign('count',$data['count']);
        $this->assign('page',$data['page']);
        $this->display();
    }


    public function export() {
        $map = array();
        $star_id = trim(I('star_id'));
        if($star_id) {
            $map['star_id'][] = array('like',$star_id . '%');
        }
        $nickname = trim(I('nickname'));
        if($nickname) {
            $user_model = model('User');
            $user_info = $user_model->searchUserInfoByNickName($nickname,'id');
            if($user_info === false) {
                $this->error('主播名称获取失败');
            }
            if (empty($user_info)) {
                $map['star_id'] = '';
            } else {
                $map['star_id'][] = array('in',array_column($user_info,'id'));
            }
        }
        $name = trim(I('name'));
        if($name) {
            $star_family_model = model('StarFamily');
            $star_name = $star_family_model->searchStarInfoByName($name,'star_id');
            if($star_name === false) {
                $this->error('家族主播名称获取失败');
            }
            if(empty($star_name)){
                $map['star_id'] = '';
            }else {
                $map['star_id'][] = array('in',array_column($star_name,'star_id'));
            }
        }
        $month = trim(I('month'));
        $month = ($month) ? $month : date('Y-m-01',time());
        $nextmonth = strtotime('+1month',strtotime($month));
        $lives = array();
        $lives['created_at'] = array();
        $lives['created_at'][] = array('egt',strtotime($month));
        $lives['created_at'][] = array('lt',$nextmonth);

        $logic = logic('FamilyIncome');
        $data = $logic->getFamilyStarIncomeList($map,$lives);
        if($data === false) {
            $this->error($logic->getErrorMessage());
        }else{
            $export_fields = FamilyIncomeLogic::$exportFields;
            $data = $logic->parseExportData($export_fields,$data['data'],$data['sum_income']);
            \Lib\Csv::export($export_fields,$data,'FamilyIncome_info_at'.date('Y-m-d H:i:s') . '.csv');
        }
    }

}