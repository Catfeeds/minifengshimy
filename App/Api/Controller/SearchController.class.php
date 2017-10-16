<?php
namespace Api\Controller;
use Think\Controller;
class SearchController extends PublicController {
	//***************************
	//  获取会员 搜索记录接口
	//***************************
    public function index(){
    	$uid = intval($_REQUEST['uid']);
    	//获取热门搜索内容
        $remen = M('search_record')->group('keyword')->field('keyword')->order('addtime desc')->limit(10)->select();
        //获取历史搜索记录
        $history = array();
        if ($uid) {
            $history = M('search_record')->where('uid='.intval($uid))->order('addtime desc')->field('keyword')->limit(20)->select();
        }
        echo json_encode(array('remen'=>$remen,'history'=>$history));
        exit();
    }

    //***************************
    //  产品商家搜索接口
    //***************************
    public function searches(){
        $uid = intval($_REQUEST['uid']);

        $keyword = trim($_REQUEST['keyword']);
        if (!$keyword) {
            echo json_encode(array('status'=>0,'err'=>'请输入搜索内容.'));
            exit();
        }

        if ($uid) {
            $check = M('search_record')->where('uid='.intval($uid).' AND keyword="'.$keyword.'"')->find();
            if ($check) {
               $num = intval($check['num'])+1;
               M('search_record')->where('id='.intval($check['id']))->save(array('num'=>$num));
            }else{
               $add = array();
               $add['uid'] = $uid;
               $add['keyword'] = $keyword;
               $add['addtime'] = time();
               M('search_record')->add($add);
            }
        }

        $page=intval($_REQUEST['page']);
        if (!$page) {
            $page=1;
        }
        $limit = intval($page*8)-8;


        $where ='1=1 AND del=0';
        if ($keyword && $keyword!='undefined') {
            $where .= ' AND (name LIKE "%'.$keyword.'%" OR hospital LIKE "%'.$keyword.'%")';
        }

        $doctor = M('doctor')->where($where)->order($order)->limit($limit.',8')->select();
        $json = array();$json_arr = array();
        foreach ($doctor as $k => $v) {
            $json['id'] = $v['id'];
            $json['name'] = $v['name'];
            $json['photo_x'] = __DATAURL__.$v['photo_x'];
            $json['is_yu'] = $v['is_yu'];
            $json['is_online'] = $v['is_online'];
            $json['hospital'] = $v['hospital'];
            $json['position'] = $v['position'];
            $json['service'] = $v['service'];
            $timenum = intval(time()-$v['addtime']);
            $tian = ceil($timenum/86400);
            $nian = sprintf('%.2f', $timenum/31536000);
            if ($nian>=2) {
                $json['desc'] = '两年';
            }elseif ($nian>=1 || intval($tian)>=180) {
                $json['desc'] = '一年';
            }elseif (intval($tian)>=30) {
                $json['desc'] = '几个月';
            }elseif (intval($tian)>=15) {
                $json['desc'] = '半个月';
            } elseif (intval($tian)>=7) {
                $json['desc'] = '七天';
            }else {
                $json['desc'] = '两天';
            }

            //专科
            $json['cname'] = M('doctor_cat')->where('id='.intval($v['cid']))->getField('name');
            //评分
            $plnum = intval(M('doctor_dp')->where('docid='.intval($v['id']))->getField('COUNT(id)'));
            if ($plnum>0) {
                $plsum = intval(M('doctor_dp')->where('docid='.intval($v['id']))->getField('SUM(num)'));
                $json['grade'] = sprintf('%.1f', ($plsum/$plnum));
            }else{
                $json['grade'] = sprintf('%.1f', 5);
            }
            $json['searchtype'] = 1;
            $json_arr[] = $json;
        }

        $page2=intval($_REQUEST['page2']);
        if (!$page2) {
            $page2=1;
        }
        $limit2 = intval($page2*6)-6;

        $condition = array();
        $condition['status']=1;
        //根据店铺名称查询
        $condition['name']=array('LIKE','%'.$keyword.'%');
        //获取所有的商家数据
        $field = 'id,name,cid,city,main_hy,logo';
        $hlist = M('hospital')->where($condition)->order('sort asc,addtime desc')->field($field)->limit($limit.',8')->select();
        foreach ($hlist as $k => $v) {
            $hlist[$k]['logo'] = __DATAURL__.$v['logo'];
            $hlist[$k]['cname'] = M('sccat')->where('id='.intval($v['cid']))->getField('name');
            $hlist[$k]['cityname'] = M('china_city')->where('id='.intval($v['city']))->getField('name');
            $hlist[$k]['searchtype'] = 2;
        }

        echo json_encode(array('status'=>1,'doclist'=>$json_arr,'hlist'=>$hlist));
        exit();
    }


}