<?php
namespace Api\Controller;
use Think\Controller;
class IndexController extends PublicController {
	//***************************
	//  首页数据接口
	//***************************
    public function index(){
    	//如果缓存首页没有数据，那么就读取数据库
    	/***********获取首页顶部轮播图************/
    	$ggtop=M('guanggao')->where('position=1')->order('sort asc,id desc')->field('id,photo')->limit(10)->select();
		foreach ($ggtop as $k => $v) {
			$ggtop[$k]['photo']=__DATAURL__.$v['photo'];
		}
    	/***********获取首页顶部轮播图 end************/

        //============================
        //首页推荐医生4个
        //============================
        $tjlist = M('doctor')->where('del=0 AND type=1')->order('sort asc,id desc')->field('id,name,photo_x,digest')->limit(4)->select();
        foreach ($tjlist as $k => $v) {
            $tjlist[$k]['photo_x'] = __DATAURL__.$v['photo_x'];
        }

    	//======================
    	//首页中部广告
    	//======================
    	$ggimgs=M('guanggao')->where('position=2')->order('id desc')->field('id,photo')->limit(1)->find();
        $imgs = __DATAURL__.$ggimgs['photo'];

        //======================
        //首页 患者病例
        //======================
        $supply = M('supply');
        $hzbl = $supply->where('1=1 AND type=2 AND docid=0')->order('addtime desc')->limit(4)->select();
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

        //======================
        //首页 接诊动态
        //======================
        $list = $supply->where('1=1 AND docid!=0')->order('addtime desc')->limit(10)->select();
        foreach ($list as $k => $v) {
            $timenum = intval(time()-$val['addtime']);
            $tian = ceil($timenum/86400);
            $xiaoshi = ceil($timenum/3600);
            $fenzhong = ceil($timenum/60);
            if (intval($tian)>1) {
                if (intval($tian)>=7) {
                    $list[$k]['desc'] = date("m-d",$v['addtime']);
                }else{
                    $list[$k]['desc'] = intval($tian).'天前';
                }
            }elseif (intval($xiaoshi)>0) {
                $list[$k]['desc'] = intval($xiaoshi).'小时前';
            }elseif (intval($fenzhong)>0) {
                $list[$k]['desc'] = intval($fenzhong).'分钟前';
            }else{
                $list[$k]['desc'] = intval($timenum).'秒前';
            }
            $list[$k]['photo'] = M('user')->where('id='.intval($v['uid']))->getField('photo');
            $list[$k]['uname'] = mb_substr($v['uname'],0,1,'utf-8').'**';

            $docinfo = M('doctor')->where('id='.intval($v['docid']))->field('name,hospital')->find();
            $list[$k]['dname'] = $docinfo['name'];
            $list[$k]['hospital'] = $docinfo['hospital'];
        }
        $uid = intval($_REQUEST['uid']);
        if (!$uid) {
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $res = M('doctor')->where('uid='.$uid)->select();
        if($res){
            $utype = 1;
        }else{
            $utype = 2;
        }
    	echo json_encode(array('status'=>1,'ggtop'=>$ggtop,'imgs'=>$imgs,'tjlist'=>$tjlist,'hzbl'=>$hzbl,'list'=>$list,'utype'=>$utype));
    	exit();
    }

    //***************************
    //  查看所有患者病历
    //***************************
    public function supply_list(){
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
    //  首页 按医生 按医院
    //***************************
    public function getdata() {
        //***************************
        //  获取 所有医生
        //***************************
        $json="";
        //排序
        $order="sort asc,addtime desc";//默认按添加时间排序
        //条件
        $where="1=1 AND del=0";
        $doctor = M('doctor')->where($where)->order($order)->limit(8)->select();
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
            $json_arr[] = $json;
        }

        //***************************
        //  获取 所有医院
        //***************************
        $field = 'id,name,cid,city,main_hy,logo';
        $hlist = M('hospital')->where('status=1')->order('sort asc,addtime desc')->field($field)->limit(8)->select();
        foreach ($hlist as $k => $v) {
            $hlist[$k]['logo'] = __DATAURL__.$v['logo'];
            $hlist[$k]['cname'] = M('sccat')->where('id='.intval($v['cid']))->getField('name');
            $hlist[$k]['cityname'] = M('china_city')->where('id='.intval($v['city']))->getField('name');
        }

        //***************************
        //  获取 所有医生 专科
        //***************************
        $dtype = M('doctor_cat')->where('del=0')->order('sort asc,id desc')->field('id,name')->select();
        $dlist = array();$doc_list = array();
        foreach ($dtype as $k => $v) {
            $dlist['id'] = intval($v['id']);
            $dlist['name'] = $v['name'];
            $doc_list[] = $dlist;
        }

        //***************************
        //  获取 所有医院 等级
        //***************************
        $htype = M('sccat')->where('1=1')->order('id desc')->field('id,name')->select();

        echo json_encode(array('pro'=>$json_arr,'hlist'=>$hlist,'dlist'=>$doc_list,'htype'=>$htype));
        exit();
    }

    public function ceshi(){
    }

}