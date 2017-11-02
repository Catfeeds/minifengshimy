<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class DoctorController extends PublicController {
	//***************************
	//  获取商品详情信息接口
	//***************************
    public function index(){
		$doctor = M("doctor");
	}

	//***************************
	//  获取商品详情接口
	//***************************
	public function details(){
		header('Content-type:text/html; Charset=utf8');
		$docid = intval($_REQUEST['docid']);
		$pro = M('doctor')->where('id='.intval($docid).' AND del=0')->find();
		if(!$pro){
			echo json_encode(array('status'=>0,'err'=>'数据信息异常！'));
			exit();
		}
		// $content = str_replace('/minigzbdrent/Data/', __DATAURL__, $pro['content']);
		// $content = html_entity_decode($content, ENT_QUOTES , 'utf-8');
		//计算综合评分
		$plnum = intval(M('doctor_dp')->where('docid='.intval($docid))->getField('COUNT(id)'));
		if ($plnum>0) {
			$plsum = intval(M('doctor_dp')->where('docid='.intval($docid))->getField('SUM(num)'));
			$pro['grade'] = sprintf('%.1f', ($plsum/$plnum));
		}else{
			$pro['grade'] = sprintf('%.1f', 5);
		}

		$pro['photo_x'] = __DATAURL__.$pro['photo_x'];
		$pro['addtime'] = date("Y-m-d",$pro['addtime']);
        $pro['cname'] = M('doctor_cat')->where('id='.intval($pro['cid']))->getField('name');
		$up = array();
		$up['renqi'] = intval($pro['renqi'])+1;
		M('doctor')->where('id='.intval($pro_id))->save($up);

		echo json_encode(array('status'=>1,'info'=>$pro));
		exit();
	}

	//***************************
	//  获取所有医生列表
	//***************************
   	public function lists(){
 		$json="";
 		$id=intval($_POST['cat_id']);//获得分类id 这里的id是pro表里的cid

 		$keyword=I('post.keyword');
 		//排序
 		$order="sort asc,addtime desc";//默认按添加时间排序

 		//条件
 		$where="1=1 AND del=0";
 		if(intval($id)){
 			$where.=" AND cid=".intval($id);
 		}

 		if($keyword && $keyword!='undefined') {
            $where.=' AND name LIKE "%'.$keyword.'%"';
        }

 		$doctor = M('doctor')->where($where)->order($order)->select();
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

 		//获取所有对应分类专科
 		$htype = M('doctor_cat')->where('del=0')->order('sort asc,id desc')->field('id,name')->select();
 		$hlist = array();$h_list = array();
 		$h_list[0]['id'] = 0;
 		$h_list[0]['name'] = '不限';
 		foreach ($htype as $k => $v) {
 			$hlist['id'] = intval($v['id']);
 			$hlist['name'] = $v['name'];
 			$h_list[] = $hlist;
 		}

 		//自定义排序
 		$sortlists = array();
 		$sortlists[0]['name'] = '不限';
 		$sortlists[0]['sorttype'] = '';
 		$sortlists[1]['name'] = '电话预约';
 		$sortlists[1]['sorttype'] = 'yu';
 		$sortlists[2]['name'] = '在线接诊';
 		$sortlists[2]['sorttype'] = 'online';

 		echo json_encode(array('status'=>1,'pro'=>$json_arr,'cat'=>$h_list,'sortlist'=>$sortlists));
 		exit();
    }

    //***************************
    //  获取所有已咨询医生列表
    //***************************
    public function mylists(){
        $json="";
        //条件
        $uid = intval($_REQUEST['uid']);
        $where="1=1 AND docid>0 AND uid=".intval($uid);

        //分页
        $page= intval($_POST['page']);
        if (!$page) {
            $page=1;
        }
        $limit = intval($page*8)-8;

        $list = M('supply')->where($where)->order('addtime desc')->field('id,docid,casename,addtime')->limit($limit.',8')->select();
        $json = array();$json_arr = array();
        foreach ($list as $k => $i) {
            $json['docid'] = intval($i['docid']); //咨询医生ID
            $json['casename'] = $i['casename']; //咨询病历名称
            $json['addtime'] = date('Y-m-d',$i['addtime']);  //咨询时间
            $v = M('doctor')->where('id='.intval($i['docid']))->find();
            $json['name'] = $v['name'];
            $json['photo_x'] = __DATAURL__.$v['photo_x'];
            $json['is_yu'] = $v['is_yu'];
            $json['is_online'] = $v['is_online'];
            $json['hospital'] = $v['hospital'];
            $json['position'] = $v['position'];
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

        echo json_encode(array('pro'=>$json_arr));
        exit();
    }

    //*******************************
	//  商品列表页面 获取更多接口
	//*******************************
    public function get_more(){
 		$json="";
 		$id=intval($_POST['cat_id']);//获得分类id 这里的id是pro表里的cid

 		$page= intval($_POST['page']);
 		if (!$page) {
 			$page=2;
 		}
 		$limit = intval($page*8)-8;

 		$keyword=I('post.keyword');
 		//排序
 		$order="sort asc,addtime desc";//默认按添加时间排序
 		//条件
 		$where="1=1 AND del=0";
 		if(intval($id)) {
 			$where.=" AND cid=".intval($id);
 		}

	 	//排序
 		$sorttype = trim($_REQUEST['sorttype']);
 		if ($sorttype) {
 			if ($sorttype=='yu') {
 				//在线预约
 				$where.=" AND is_yu=1";
 			}elseif ($sorttype=='online') {
 				//在线接诊
 				$where.=" AND is_online=1";
 			}
 		}

 		if($keyword && $keyword!='undefined') {
            $where.=' AND name LIKE "%'.$keyword.'%"';
        }

 		$doctor=M('doctor')->where($where)->order($order)->limit($limit.',8')->select();

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
            $plnum = intval(M('doctor_dp')->where('docid='.intval($v['id']).' AND audit=1')->getField('COUNT(id)'));
			if ($plnum>0) {
				$plsum = intval(M('doctor_dp')->where('docid='.intval($v['id']).' AND audit=1')->getField('SUM(num)'));
				$json['grade'] = sprintf('%.1f', ($plsum/$plnum));
			}else{
				$json['grade'] = sprintf('%.1f', 5);
			}
 			$json_arr[] = $json;
 		}

 		echo json_encode(array('pro'=>$json_arr));
 		exit();
    }

    //*******************************
	//  商品列表页面 获取更多接口
	//*******************************
    public function sortlist(){
 		$json="";
 		$id=intval($_REQUEST['cat_id']);//获得分类id 这里的id是pro表里的cid

 		$keyword=I('post.keyword');
 		//排序
 		$order="sort asc,addtime desc";//默认按添加时间排序
 		//条件
 		$where="1=1 AND del=0";
 		if(intval($id)) {
 			$where.=" AND cid=".intval($id);
 		}

 		$sorttype = trim($_REQUEST['sorttype']);
 		if ($sorttype) {
 			if ($sorttype=='yu') {
 				//在线预约
 				$where.=" AND is_yu=1";
 			}elseif ($sorttype=='online') {
 				//在线接诊
 				$where.=" AND is_online=1";
 			}
 		}

 		if($keyword && $keyword!='undefined') {
            $where.=' AND name LIKE "%'.$keyword.'%"';
        }

 		$doctor=M('doctor')->where($where)->order($order)->limit(8)->select();

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
            $plnum = intval(M('doctor_dp')->where('docid='.intval($v['id']).' AND audit=1')->getField('COUNT(id)'));
			if ($plnum>0) {
				$plsum = intval(M('doctor_dp')->where('docid='.intval($v['id']).' AND audit=1')->getField('SUM(num)'));
				$json['grade'] = sprintf('%.1f', ($plsum/$plnum));
			}else{
				$json['grade'] = sprintf('%.1f', 5);
			}
 			$json_arr[] = $json;
 		}

 		echo json_encode(array('pro'=>$json_arr));
 		exit();
    }

    //*******************************
    //   获取所有 医生分科
    //*******************************
    public function getcatlist () {
        $uid = intval($_REQUEST['uid']);
        $user = M('user')->where('id='.intval($uid))->find();
        $info = M('doctor')->where('id='.intval($user['docid']))->find();

        $list = M('doctor_cat')->where('del=0')->order('sort asc')->field('id,name')->select();

        echo json_encode(array('status'=>1,'list'=>$list,'info'=>$info));
        exit();
    }

    //***************************
    //  获取商品详情接口
    //***************************
    public function detail(){
        header('Content-type:text/html; Charset=utf8');
        $uid = intval($_REQUEST['uid']);
        $docid = M('doctor')->where('uid='.$uid)->getField('id');
        $pro = M('doctor')->where('id='.intval($docid).' AND del=0')->find();
        if(!$pro){
            echo json_encode(array('status'=>0,'err'=>'数据信息异常！'));
            exit();
        }
        // $content = str_replace('/minigzbdrent/Data/', __DATAURL__, $pro['content']);
        // $content = html_entity_decode($content, ENT_QUOTES , 'utf-8');
        //计算综合评分
        $plnum = intval(M('doctor_dp')->where('docid='.intval($docid))->getField('COUNT(id)'));
        if ($plnum>0) {
            $plsum = intval(M('doctor_dp')->where('docid='.intval($docid))->getField('SUM(num)'));
            $pro['grade'] = sprintf('%.1f', ($plsum/$plnum));
        }else{
            $pro['grade'] = sprintf('%.1f', 5);
        }

        $pro['photo_x'] = __DATAURL__.$pro['photo_x'];
        $pro['erweima'] = __DATAURL__.$pro['erweima'];
        $pro['addtime'] = date("Y-m-d",$pro['addtime']);
        $pro['cname'] = M('doctor_cat')->where('id='.intval($pro['cid']))->getField('name');
        $up = array();
        $up['renqi'] = intval($pro['renqi'])+1;
        M('doctor')->where('id='.intval($pro_id))->save($up);

        echo json_encode(array('status'=>1,'info'=>$pro));
        exit();
    }

     //***************************
    //   上传图片
    //***************************
    public function uploadimg(){
        $info = $this->upload_images($_FILES['data'],array('jpg','png','jpeg'),"doctor/".date(Ymd));
        if(is_array($info)) {// 上传错误提示错误信息
            $url = 'UploadFiles/'.$info['savepath'].$info['savename'];
            if ($_REQUEST['imgurl']) {
                $img_url = "Data/".$_REQUEST['imgurl'];
                if(file_exists($img_url)) {
                    @unlink($img_url);
                }
            }
            echo $url;
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>$info));
            exit();
        }
    }

    //更新数据库
    public function saveImg(){
        $uid = intval($_REQUEST['uid']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $photo_x = $_REQUEST['photo_x'];
        $data['photo_x'] = $photo_x;
        $res = M('doctor')->where('uid='.$uid)->save($data);
        if($res){
            echo json_encode(array('status'=>1,'err'=>'修改成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'修改失败！'));
            exit();
        }
    }

     //更新数据库
    public function saveScan(){
        $uid = intval($_REQUEST['uid']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $erweima = $_REQUEST['erweima'];
        $data['erweima'] = $erweima;
        $res = M('doctor')->where('uid='.$uid)->save($data);
        if($res){
            echo json_encode(array('status'=>1,'err'=>'修改成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'修改失败！'));
            exit();
        }
    }

    /*
    *
    * 图片上传的公共方法
    *  $file 文件数据流 $exts 文件类型 $path 子目录名称
    */
    public function upload_images($file,$exts,$path){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =  2097152 ;// 设置附件上传大小2M
        $upload->exts      =  $exts;// 设置附件上传类型
        $upload->rootPath  =  './Data/UploadFiles/'; // 设置附件上传根目录
        $upload->savePath  =  ''; // 设置附件上传（子）目录
        $upload->saveName = time().mt_rand(100000,999999); //文件名称创建时间戳+随机数
        $upload->autoSub  = true; //自动使用子目录保存上传文件 默认为true
        $upload->subName  = $path; //子目录创建方式，采用数组或者字符串方式定义
        // 上传文件 
        $info = $upload->uploadOne($file);
        if(!$info) {// 上传错误提示错误信息
            return $upload->getError();
        }else{// 上传成功 获取上传文件信息
            //return 'UploadFiles/'.$file['savepath'].$file['savename'];
            return $info;
        }
    }

    //修改医生信息
    public function update(){
        $uid = intval($_REQUEST['uid']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $data = array();
        $data['digest'] = $_REQUEST['digest'];
        $data['zyjy'] = $_REQUEST['zyjy'];
        $res = M('doctor')->where('uid='.$uid)->save($data);
        if($res){
            echo json_encode(array('status'=>1,'err'=>'保存成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'保存成功！'));
            exit();
        }
    }

    //提现
    public function tixian(){
        $uid = intval($_REQUEST['uid']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $temp_total = M('doctor')->where('uid='.$uid)->getField('total');
        if(floatval($temp_total) < floatval($_REQUEST['total'])){
            echo json_encode(array('status'=>0,'err'=>'提现金额不能大于剩余金额！'));
            exit();
        }
        $type = intval($_REQUEST['ttype']);
        $data = array();
        $data['uid'] = $uid;
        $data['total'] = floatval($_REQUEST['total']);
        $data['addtime'] = time();
        if($type == 1){
            $data['bankname'] = $_REQUEST['bankname'];
            $data['bankCart'] = $_REQUEST['bankCart'];
            $data['type'] = $type;
        }else if($type == 2){
            $data['type'] = $type;
            $data['zhanghao'] = $_REQUEST['zhanghao'];
        }else{
            echo json_encode(array('status'=>0,'err'=>'信息有误！'));
            exit();
        }
        $res = M('tixian')->add($data);
        if($res){
            $temp['total'] = floatval($temp_total) - floatval($_REQUEST['total']);
            M('doctor')->where('uid='.$uid)->save($temp);
            echo json_encode(array('status'=>1,'err'=>'提现成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'提现失败！'));
            exit();
        }
    }

    //医生的咨询信息
    public function content_list(){
        $uid = intval($_REQUEST['uid']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $docid = M('doctor')->where('uid='.$uid)->getField('id');
        $list = M('content')->where('reply_content is null')->select();
        if($list){
            foreach($list as $k => $v){
                $list[$k]['addtime'] = date('Y-m-d H:i',$v['addtime']);
                $list[$k]['name'] = M('user')->where('id='.intval($v['uid']))->getField('truename');
                $photo_x = M('user')->where('id='.intval($v['uid']))->getField('photo_x');
                if($photo_x){
                    $list[$k]['photo_x'] = __DATAURL__.$photo_x;
                }
                if(!$list[$k]['name']){
                     $list[$k]['name'] = M('user')->where('id='.intval($v['uid']))->getField('uname');
                }
            }
        }
        $list2 = M('content')->where('reply_content!=""')->select();
        $num = 0;
        if($list2){
            foreach($list2 as $k => $v){
                $num++;
                $list2[$k]['addtime'] = date('Y-m-d H:i',$v['addtime']);
                 $list2[$k]['name'] = M('user')->where('id='.intval($v['uid']))->getField('truename');
                $photo_x = M('user')->where('id='.intval($v['uid']))->getField('photo_x');
                if($photo_x){
                    $list2[$k]['photo_x'] = __DATAURL__.$photo_x;
                }
                if(!$list2[$k]['name']){
                     $list2[$k]['name'] = M('user')->where('id='.intval($v['uid']))->getField('uname');
                }
            }
        }
        echo json_encode(array('status'=>1,'list'=>$list,'list2'=>$list2,'num'=>$num));
        exit();
    }

    //咨询内容详情
    public function content_detail(){
        $id = intval($_REQUEST['id']);
        if(!$id){
            echo json_encode(array('status'=>0,'err'=>'数据异常！'));
            exit();
        }
        $info = M('content')->where('id='.$id)->find();
        $info['addtime'] = date("Y-m-d H:i",$info['addtime']);
        $info['reply_addtime'] = date("Y-m-d H:i",$info['reply_addtime']);
        $info['doc_photo'] = __DATAURL__.M('doctor')->where('id='.intval($info['docid']))->getField('photo_x');
        $info['name'] = M('user')->where('id='.intval($info['uid']))->getField('truename');
        if(!$info['name']){
             $info['name'] = M('user')->where('id='.intval($info['uid']))->getField('uname');
        }
        $user_photo = M('user')->where('id='.intval($info['uid']))->getField('photo_x');
        if($user_photo){
            $info['user_photo'] = __DATAURL__.$user_photo;
        }
        
        echo json_encode(array('status'=>1,'info'=>$info));
        exit();
    }

    //回复咨询
    public function reply_content(){
        $id = intval($_REQUEST['id']);
        if(!$id){
            echo json_encode(array('status'=>0,'err'=>'数据异常！'));
            exit();
        }
        $data['reply_addtime'] = time();
        $data['reply_content'] = $_REQUEST['reply_content'];
        $res = M('content')->where('id='.$id)->save($data);
        if($res){
            echo json_encode(array('status'=>1,'err'=>'回复成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'回复失败！'));
            exit();
        }
    }

    //患者列表
    public function patient_list(){
        $uid = intval($_REQUEST['uid']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $docid = M('doctor')->where('uid='.$uid)->getField('id');
        $user_id = M('order')->where('docid='.intval($docid))->field('uid')->select();
        if(!$user_id){
            echo json_encode(array('status'=>0,'err'=>'暂无数据！'));
            exit();
        }
        $list = array();
        foreach($user_id as $k => $v){
            $temp = M('user')->where('id='.intval($v))->find();
            if($temp['photo_x']){
                $temp['photo_x'] = __DATAURL__.$temp['photo_x'];
            }
            if($temp['sex']==0){
                $temp['sex_name'] = '未知';
            }else if($temp['sex']==1){
                $temp['sex_name'] = '男';
            }else if($temp['sex']==2){
                $temp['sex_name'] = '女';
            }
            $temp['age'] = intval(date('Y',time()) - substr($temp['birthday'],0,4));
            $city = explode(',',$temp['city']);
            $temp['city'] = $city[1];
            $temp['bingcheng'] = intval(date('Y',time()) - substr($temp['que_zhen'],0,4));
            if(!$temp['bingcheng']){
                $temp['bingcheng'] = 1;
            }
            array_push($list,$temp);
        }
        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
    }

     //患者详情
    public function patient_detail(){
        $id = intval($_REQUEST['id']);
        if(!$id){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $info = M('user')->where('id='.intval($id))->find();
        if($info['photo_x']){
            $info['photo_x'] = __DATAURL__.$info['photo_x'];
        }
        if($info['sex']==0){
            $info['sex_name'] = '未知';
        }else if($info['sex']==1){
            $info['sex_name'] = '男';
        }else if($info['sex']==2){
            $info['sex_name'] = '女';
        }
        $info['age'] = intval(date('Y',time()) - substr($info['birthday'],0,4));
        $city = explode(',',$info['city']);
        $info['city'] = $city[1];
        $info['bingcheng'] = intval(date('Y',time()) - substr($info['que_zhen'],0,4));
        if(!$info['bingcheng']){
            $info['bingcheng'] = 1;
        }
        echo json_encode(array('status'=>1,'info'=>$info));
        exit();
    }

    //医生给患者发信息
    public function sendMessage(){
        $docid = intval($_REQUEST['docid']);
        if(!$docid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $docid = intval(M('doctor')->where('uid='.$docid)->getField('id'));
        $uid = intval($_REQUEST['uid']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $data['docid'] = $docid;
        $data['uid'] = $uid;
        $data['reply_content'] = $_REQUEST['reply_content'];
        $data['reply_addtime'] = time();
        $res = M('content')->add($data);
        if($res){
            echo json_encode(array('status'=>1,'err'=>'发送成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'发送失败！'));
            exit();
        }
    }

     //获取医生给患者发的信息
    public function getMessage(){
        $docid = intval($_REQUEST['docid']);
        if(!$docid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $docid = intval(M('doctor')->where('uid='.$docid)->getField('id'));
        $uid = intval($_REQUEST['uid']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $list = M('content')->where('uid='.$uid.' AND docid='.$docid.' AND content=""')->select();
        if($list){
            foreach($list as $k => $v){
                $list[$k]['reply_addtime'] = date("Y-m-d H:i:s",$v['reply_addtime']);
            }
        }
        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
    }

    //获取患者用药管理列表
    public function getMedicineList(){
        $uid = intval($_REQUEST['id']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $list = M('medicine')->where('uid='.$uid)->select();
        if(!$list){
            echo json_encode(array('status'=>0,'err'=>'暂无用药管理！'));
            exit(); 
        }
        $photo = array();
        foreach($list as $k => $v){
            $photo[$k] = '';
            if($v['photo']){
                $photo[$k] = explode(',',trim($v['photo'],','));
                foreach($photo[$k] as $k2 => $v2){
                    $photo[$k][$k2] = __DATAURL__.$v2;
                }
            }  
        }
        echo json_encode(array('status'=>1,'list'=>$list,'photo'=>$photo));
        exit();
    }

    //患者检验报告列表
    public function report_list(){
        $uid = intval($_REQUEST['id']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $list = M('report')->where('uid='.$uid)->select();
        if(!$list){
            echo json_encode(array('status'=>0,'err'=>'暂无检验报告！'));
            exit(); 
        }
        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
    }

    //患者检验报告详情
    public function report_detail(){
        $rid = intval($_REQUEST['rid']);
        if(!$rid){
            echo json_encode(array('status'=>0,'err'=>'数据异常！'));
            exit();
        }
        $info = M('report')->where('id='.$rid)->find();
        echo json_encode(array('status'=>1,'info'=>$info));
        exit();
    }

    //获取医生收入
    public function getInput(){
        $uid = intval($_REQUEST['uid']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $docid = intval(M('doctor')->where('uid='.$uid)->getField('id'));
        $list = M('order')->where('docid='.$docid)->select();
        if($list){
            foreach($list as $k => $v){
                $list[$k]['addtime'] = date("Y-m-d",$v['addtime']);
                $list[$k]['username'] = M('user')->where('id='.intval($v['uid']))->getField('uname');
            }
        }
        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
    }

}