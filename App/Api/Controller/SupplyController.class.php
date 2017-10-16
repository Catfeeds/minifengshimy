<?php
namespace Api\Controller;
use Think\Controller;
class SupplyController extends PublicController {
    //***************************
    //  查看所有患者病历
    //***************************
    public function index(){
        $page = intval($_REQUEST['page']);
        if (!$page) {
           $page=1;
        }
        $limit = intval($page*8)-8;

        $supply = M('supply');
        $hzbl = $supply->where('1=1 AND type=2 AND docid=0')->order('addtime desc')->limit($limit.',8')->select();
        foreach ($hzbl as $k => $v) {
            $timenum = intval(time()-$val['addtime']);
            $tian = ceil($timenum/86400);
            $xiaoshi = ceil($timenum/3600);
            $fenzhong = ceil($timenum/60);
            if (intval($tian)>1) {
                if (intval($tian)>=7) {
                    $hzbl[$k]['desc'] = date("m-d",$v['addtime']);
                }else{
                    $hzbl[$k]['desc'] = intval($tian).'天前';
                }
            }elseif (intval($xiaoshi)>0) {
                $hzbl[$k]['desc'] = intval($xiaoshi).'小时前';
            }elseif (intval($fenzhong)>0) {
                $hzbl[$k]['desc'] = intval($fenzhong).'分钟前';
            }else{
                $hzbl[$k]['desc'] = intval($timenum).'秒前';
            }
            $hzbl[$k]['photo'] = M('user')->where('id='.intval($v['uid']))->getField('photo');
            if ($v['img1']) {
                $hzbl[$k]['adv1'] = __DATAURL__.$v['img1'];
            }
            if ($v['img2']) {
                $hzbl[$k]['adv2'] = __DATAURL__.$v['img2'];
            }
            if ($v['img3']) {
                $hzbl[$k]['adv3'] = __DATAURL__.$v['img3'];
            }
        }

        echo json_encode(array('list'=>$hzbl));
        exit();
    }

    //***************************
    //  查看病历
    //***************************
    public function details() {
        $sid = intval($_REQUEST['sid']);
        $info = M('supply')->where('id='.intval($sid))->find();
        if (!$info) {
            echo json_encode(array('status'=>0,'err'=>'没有找到相关信息.'));
            exit();
        }

        $timenum = intval(time()-$info['addtime']);
        $tian = ceil($timenum/86400);
        $xiaoshi = ceil($timenum/3600);
        $fenzhong = ceil($timenum/60);
        if (intval($tian)>1) {
            if (intval($tian)>=7) {
                $info['desc'] = date("m-d",$info['addtime']);
            }else{
                $info['desc'] = intval($tian).'天前';
            }
        }elseif (intval($xiaoshi)>0) {
            $info['desc'] = intval($xiaoshi).'小时前';
        }elseif (intval($fenzhong)>0) {
            $info['desc'] = intval($fenzhong).'分钟前';
        }else{
            $info['desc'] = intval($timenum).'秒前';
        }

        if ($info['img1']) {
            $info['adv1'] = __DATAURL__.$info['img1'];
        }
        if ($info['img2']) {
            $info['adv2'] = __DATAURL__.$info['img2'];
        }
        if ($info['img3']) {
            $info['adv3'] = __DATAURL__.$info['img3'];
        }
        $info['photo'] = M('user')->where('id='.intval($info['uid']))->getField('photo');

        echo json_encode(array('status'=>1,'info'=>$info));
        exit();
    }

    //***************************
    //  保存 医生回复记录
    //***************************
    public function savedata () {
        $uid = intval($_REQUEST['uid']);
        $info = M('user')->where('id='.intval($uid).' AND del=0')->find();
        if (!$info) {
            echo json_encode(array('status'=>0,'err'=>'登录信息异常.'));
            exit();
        }

        if (intval($info['type'])!=2 || intval($info['docid'])==0) {
            echo json_encode(array('status'=>0,'err'=>'您的账号还没有通过认证审核.'));
            exit();
        }

        $content = $_POST['content'];
        $sid = intval($_REQUEST['sid']);
        $supply = M('supply')->where('id='.intval($sid))->find();
        if (!$supply) {
            echo json_encode(array('status'=>0,'err'=>'数据信息异常.'));
            exit();
        }

        $data = array();
        $data['sid'] = $sid;
        $data['docid'] = intval($info['docid']);
        $data['content'] = $content;
        $data['type'] = 2;
        $data['addtime'] = time();
        $res = M('advice_log')->add($data);
        if ($res) {
            echo json_encode(array('status'=>1));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'保存失败.'));
            exit();
        }
    }

    //***************************
    //  查看 聊天记录
    //***************************
    public function advice() {
        $sid = intval($_REQUEST['sid']);
        $info = M('supply')->where('id='.intval($sid))->field('id,uid,docid,uname,casename,content')->find();
        $info['photo'] = M('user')->where('id='.intval($info['uid']))->getField('photo');

        $advice = M('advice_log')->where('sid='.intval($sid).' AND type=1')->order('addtime asc')->select();
        foreach ($advice as $k => $v) {
            $advice[$k]['dname'] = M('doctor')->where('id='.intval($v['docid']))->getField('name');
        }

        $docinfo = M('doctor')->where('id='.intval($info['docid']))->field('id,name,hospital,position,photo_x')->find();
        $docinfo['photo_x'] = __DATAURL__.$docinfo['photo_x'];
        //计算综合评分
        $plnum = intval(M('doctor_dp')->where('docid='.intval($docinfo['id']))->getField('COUNT(id)'));
        if ($plnum>0) {
            $plsum = intval(M('doctor_dp')->where('docid='.intval($docinfo['id']))->getField('SUM(num)'));
            $docinfo['grade'] = sprintf('%.1f', ($plsum/$plnum));
        }else{
            $docinfo['grade'] = sprintf('%.1f', 5);
        }

        echo json_encode(array('status'=>1,'info'=>$info,'advice'=>$advice,'docinfo'=>$docinfo));
        exit();
    }

    //***************************
    //  保存 医生患者聊天记录
    //***************************
    public function saveadvice () {
        $uid = intval($_REQUEST['uid']);
        $info = M('user')->where('id='.intval($uid).' AND del=0')->find();
        if (!$info) {
            echo json_encode(array('status'=>0,'err'=>'登录信息异常.'));
            exit();
        }

        $thetype = intval($_REQUEST['thetype']);

        $content = $_POST['content'];
        $sid = intval($_REQUEST['sid']);
        $supply = M('supply')->where('id='.intval($sid))->find();
        if (!$supply) {
            echo json_encode(array('status'=>0,'err'=>'数据信息异常.'));
            exit();
        }

        $data = array();
        $data['sid'] = $sid;
        $data['uid'] = intval($supply['uid']);
        $data['docid'] = intval($supply['docid']);
        if ($thetype==1) {
            $data['content'] = $content;
        }else {
            $data['reply_time'] = time();
            $data['reply_con'] = $content;
        }
        $data['type'] = 1;
        $data['addtime'] = time();
        $res = M('advice_log')->add($data);
        if ($res) {
            echo json_encode(array('status'=>1));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'保存失败.'));
            exit();
        }
    }

}