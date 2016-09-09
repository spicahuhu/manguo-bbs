<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2016/8/22
 * Time: 16:01
 */
namespace Home\Controller;
use Home\Controller\BaseController;
use Logic\FamilyCashoutLogic;
use Think\Page as Page;

class FamilyController extends BaseController{

    Public function stars() {
        $map = array();
        $star_id = trim(I('star_id'));
        if($star_id) {
            $map['star_id'] = array('like',$star_id . '%');
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
                $map['star_id'] = array('IN', array_column($user_info, 'id'));
            }
        }
        $name = trim(I('name'));
        if($name) {
            $map['name'] = array('like',$name.'%');
        }
        $sex = trim(I('sex'));
        if($sex !='') {
            $map['sex'] = $sex;
        }
        $star_model =  model('Star');
        $forbidden = trim(I('forbidden'));
        if($forbidden == 1) { //禁播中
            $star_info = $star_model->field('id')->where(array('forbided_end_at'=>array('egt',time())))->select();
            if($star_info === false) $this->error('艺人信息获取失败');
            if(empty($star_info)) {
                $map['star_id'] = '';
            }else {
                $starinfo_ids = array_column($star_info,'id');
                $map['star_id'] = array('IN',$starinfo_ids);
            }
        }
        if($forbidden != 1 && $forbidden !='') {
            $star_info = $star_model->field('id')->where(array('forbided_end_at'=>array('lt',time())))->select();
            if($star_info === false) $this->error('艺人信息获取失败');
            if(empty($star_info)) {
                $map['star_id'] = '';
            }else {
                $starinfo_ids = array_column($star_info,'id');
                $map['star_id'] = array('IN',$starinfo_ids);
            }
        }

        $logic = logic('Family');
        $res = $logic->getStarList($map);
        if($res === false) {
            $this->error($logic->getErrorMessage());
        }
        $this->assign('datas',$res);
        $this->assign('count',$count);
        $this->assign('page',$page);
        $this->assign('cur_page',$cur_page);
        $this->display();
    }

    public function editStars() {
        $family_id = I('family_id');
        $star_id = I('star_id');
        $name = trim(I('name'));
        $phone = trim(I('phone'));
        $sex = I('sex');
        $logic = logic('Family');
        $res = $logic->editStars($family_id,$star_id,$name,$phone,$sex);
        if($res === false) {
            $this->error($logic->getErrorMessage());
        }
        $this->success('主播信息编辑成功',U('/stars'));
    }


    public function cashout() {
        $logic = logic('FamilyCashout');
        $cashout = $logic->getFamilyCashoutList();
        if($cashout === false) {
            $this->error($logic->getErrorMessage());
        }
        $this->assign('datas',$cashout['data']);
        $this->assign('leave_money',$cashout['leave_money']);
        $this->assign('page',$cashout['page']);
        $this->assign('count',$cashout['count']);
        $this->display();
    }

    /**
     * 家族提现记录导出
     */
    public function cashoutExport() {
        $logic = logic('FamilyCashout');
        $cashout = $logic->getFamilyCashoutInfo();
        if($cashout === false) {
            $this->error($logic->getErrorMessage());
        }else{
            $export_fields = FamilyCashoutLogic::$exportFields;
            $data = $logic->parseExportData($export_fields,$cashout['data']);
            \Lib\Csv::export($export_fields,$data,'FamilyCashout_info_at'.date('Y-m-d H:i:s') . '.csv');
        }
    }

    /**
     * 可用星币扣除表格下载
     */
    public function coinsDeducted() {
        $id = I('id');
        $logic = logic('FamilyCashout');
        $res = $logic->getDetailedCoinsDeducted($id);
        if($res === false){
            $this->error($logic->getErrorMessage());
        }else{
            $coins_deducted_fields = FamilyCashoutLogic::$coinsDeductedFields;
            $data = $logic->parseExportData($coins_deducted_fields,$res,'coinsdeduted');
            \Lib\Csv::export($coins_deducted_fields,$data,'FamilyCoinsDeducted_info_at'.date('Y-m-d H:i:s').'.csv');
        }
    }
}