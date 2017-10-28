<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class UserController extends PublicController {

	//***************************
	//  获取用户订单数量
	//***************************
	public function getorder(){
		$uid = intval($_REQUEST['userId']);
		if (!$uid) {
			echo json_encode(array('status'=>0,'err'=>'非法操作.'));
			exit();
		}

		$order = array();
		$order['pay_num'] = intval(M('order')->where('uid='.intval($uid).' AND status=10 AND del=0')->getField('COUNT(id)'));
		$order['rec_num'] = intval(M('order')->where('uid='.intval($uid).' AND status=20 AND del=0 AND back="0"')->getField('COUNT(id)'));
		$order['ping_num'] = intval(M('order')->where('uid='.intval($uid).' AND status=30 AND del=0 AND back="0"')->getField('COUNT(id)'));
		$order['finish_num'] = intval(M('order')->where('uid='.intval($uid).' AND status=40 AND del=0 AND back="0"')->getField('COUNT(id)'));
		
		//获取审核状态
		$audit = M('user')->where('id='.intval($uid))->getField('audit');
		if (intval($audit)>0) {
			$order['auth_state'] = 1;
		}else{
			$order['auth_state'] = 0;
		}

		$type = M('user')->where('id='.intval($uid))->getField('type');
		if (intval($type)>1) {
			$order['usertype'] = 1;
		}else{
			$order['usertype'] = 0;
		}

		echo json_encode(array('status'=>1,'orderInfo'=>$order));
		exit();
	}


	//***************************
	//  获取用户信息
	//***************************
	public function userinfo(){
		$uid = intval($_REQUEST['uid']);
		$user = M("user")->where('id='.intval($uid).' AND del=0')->find();
		if (!$user) {
			echo json_encode(array('status'=>0,'err'=>'用户信息异常.'));
			exit();
		}

		echo json_encode(array('status'=>1,'userinfo'=>$user));
		exit();
		
	}

	//***************************
	//  修改用户信息
	//***************************
	public function user_edit(){
			$user_id=intval($_REQUEST['uid']);
			$user_info = M('user')->where('id='.intval($user_id).' AND del=0')->find();
			if (!$user_info) {
				echo json_encode(array('status'=>0,'err'=>'会员状态异常.'));
				exit();
			}

			$tel = trim($_POST['tel']);
			$usertype = intval($_POST['usertype']);
			$truename = trim($_POST['truename']);
			//$bl_number = trim($_POST['bl_number']);
			$data = array();
			if ($tel) {
				$data['tel'] = $tel;
			}
			if ($truename) {
				$data['truename'] = $truename;
			}
			//企业认证申请
			if ($usertype==1) {
				// if (!$bl_number) {
				// 	echo json_encode(array('status'=>0,'err'=>'请输入营业执照编号！'));
				// 	exit();
				// }
				if (intval($user_info['audit'])==1) {
					echo json_encode(array('status'=>0,'err'=>'您的认证资料正在审核中，请勿重复提交！'));
					exit();
				}
				if (!$truename || !$tel) {
					echo json_encode(array('status'=>0,'err'=>'参数错误！'));
					exit();
				}
				// if (!$user_info['bl_photo']) {
				// 	echo json_encode(array('status'=>0,'err'=>'您的营业执照还未上传！'));
				// 	exit();
				// }
				//$data['bl_number'] = $bl_number;
				$data['audit'] = 1;
			}

			if (!$data) {
				echo json_encode(array('status'=>0,'err'=>'没有找到要修改的信息.'.__LINE__));
				exit();
			}
			//dump($data);exit;
			$result=M("user")->where('id='.intval($user_id))->save($data);
			//echo M("aaa_pts_user")->_sql();exit;
		    if($result){
				echo json_encode(array('status'=>1));
				exit();
			}else{
				echo json_encode(array('status'=>0,'err'=>'提交失败.'));
				exit();
			}
	}

    //***************************
	//  用户 接单
	//***************************
	public function orders(){
		$uid = intval($_REQUEST['uid']);
		$sid = intval($_REQUEST['sid']);
		if (!$uid || !$sid) {
			echo json_encode(array('status'=>0,'err'=>'参数错误.'));
			exit();
		}

		$userinfo = M('user')->where('del=0 AND id='.intval($uid))->find();
		if (!$userinfo) {
			echo json_encode(array('status'=>0,'err'=>'用户信息错误.'));
			exit();
		}

		$check = M('supply')->where('id='.intval($sid))->find();
		if (intval($check['state'])!=0) {
			echo json_encode(array('status'=>0,'err'=>'供求信息异常！'));
			exit();
		}

		if (intval($check['uid'])==intval($uid)) {
			echo json_encode(array('status'=>0,'err'=>'状态异常！'));
			exit();
		}

		if (intval($userinfo['audit'])==0) {
			echo json_encode(array('status'=>0,'err'=>'只有认证企业才可以接单哦！'));
			exit();
		}

		if (intval($userinfo['audit'])!=2) {
			echo json_encode(array('status'=>0,'err'=>'企业认证审核中...'));
			exit();
		}

		$up = array();
		$up['rec_id'] = $uid;
		$up['rec_time'] = time();
		$up['state'] = 1;
		$res = M('supply')->where('id='.intval($sid))->save($up);
		if ($res) {
			echo json_encode(array('status'=>1));
			exit();
		}else{
			echo json_encode(array('status'=>0,'err'=>'接单失败，请稍后再试！'));
			exit();
		}
	}


	//***************************
	//  用户 提交病历
	//***************************
	public function supply(){
		$uid = intval($_REQUEST['uid']);
		$userinfo = M('user')->where('del=0 AND id='.intval($uid))->find();
		if (!$userinfo) {
			echo json_encode(array('status'=>0,'err'=>'用户信息异常.'));
			exit();
		}

		$uname = trim($_REQUEST['uname']);
		$content = trim($_REQUEST['content']);
		$phone = trim($_REQUEST['phone']);
		$casename = trim($_REQUEST['casename']);
		$casehospital = trim($_REQUEST['casehospital']);
		$img1 = trim($_REQUEST['img1']);
		$img2 = trim($_REQUEST['img2']);
		$img3= trim($_REQUEST['img3']);

		$add = array();
		$docid = intval($_REQUEST['docid']);
		if ($docid>0) {
			$add['type'] = 1;
			$add['docid'] = $docid;
		} else {
			$add['type'] = 2;
			$add['docid'] = 0;
		}

		$add['uid'] = $uid;
		$add['uname'] = $uname;
		$add['casename'] = $casename;
		$add['casehospital'] = $casehospital;
		$add['content'] = $content;
		$add['phone'] = $phone;
		$add['img1'] = $img1;
		$add['img2'] = $img2;
		$add['img3'] = $img3;
		$add['addtime'] = time();
		$res = M('supply')->add($add);
		if ($res) {
			//插入聊天记录表
			$log = array();
			$log['sid']= $res;
			$log['uid']= $uid;
			$log['docid']= $docid;
			$log['content']= $content;
			$log['addtime'] = time();
			$log['type'] = 1;
			M('advice_log')->add($log);

			//下订单
			if (intval($docid)>0) {
				$price = M('doctor')->where('id='.intval($docid))->getField('price');
				if (floatval($price)>0) {
					$data = array();
					$data['sid']=intval($res);
					$data['uid']=intval($uid);
					$data['addtime'] = time();
					$data['del'] = 0; 
					$data['docid'] = intval($docid);
					$data['status'] = 10;//未付款
					$data['price'] = floatval($price);
					$data['amount'] = floatval($price);

					$data['tel']=$userinfo['tel'];
					/*******解决屠涂同一订单重复支付问题 lisa**********/
					$data['order_sn']=$this->build_order_no();//生成唯一订单号
					/**************************************************/
					$data['order_type'] = 1;
					$result = M('order')->add($data);
					if (!$result) {
						M('supply')->delete($res);
						echo json_encode(array('status'=>0,'err'=>'发布失败！'));
						exit();
					}
					echo json_encode(array('status'=>1,'order_id'=>$result));
					exit();
				}	
			}

			echo json_encode(array('status'=>1));
			exit();
		}else{
			echo json_encode(array('status'=>0,'err'=>'发布失败！'));
			exit();
		}
	}

	//***************************
	//  用户 咨询列表
	//***************************
	public function mysupply(){
		$uid = intval($_REQUEST['uid']);
		$userinfo = M('user')->where('del=0 AND id='.intval($uid))->find();
		if (!$userinfo) {
			echo json_encode(array('status'=>0,'err'=>'用户信息异常.'));
			exit();
		}

		$page = intval($_REQUEST['page']);
		if (!$page) {
			$page = 1;
		}
		$limit = intval($page*10)-10;

		$list = M('supply')->where('1=1 AND uid='.intval($uid))->order('addtime desc')->limit($limit.',10')->select();
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

            $list[$k]['dname'] = M('doctor')->where('id='.intval($v['docid']))->getField('name');
        }

        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
	}

	//***************************
	//  会员 求助咨询列表
	//***************************
	public function helpsupply(){
		$uid = intval($_REQUEST['uid']);
		$userinfo = M('user')->where('del=0 AND id='.intval($uid))->find();
		if (!$userinfo) {
			echo json_encode(array('status'=>0,'err'=>'用户信息异常.'));
			exit();
		}

		$docid = intval($userinfo['docid']);
		if (!$docid) {
			echo json_encode(array('status'=>0,'err'=>'您还没有通过医师认证审核.'));
			exit();
		}

		$page = intval($_REQUEST['page']);
		if (!$page) {
			$page = 1;
		}
		$limit = intval($page*10)-10;

		$list = M('supply')->where('type=1 AND docid='.intval($docid))->order('addtime desc')->limit($limit.',10')->select();
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
        }

        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
	}


	//***************************
	//  用户 诊后评价列表
	//***************************
	public function myping(){
		$uid = intval($_REQUEST['uid']);
		$userinfo = M('user')->where('del=0 AND id='.intval($uid))->find();
		if (!$userinfo) {
			echo json_encode(array('status'=>0,'err'=>'用户信息异常.'));
			exit();
		}

		$page = intval($_REQUEST['page']);
		if (!$page) {
			$page = 1;
		}
		$limit = intval($page*10)-10;

		if (intval($userinfo['type'])==2) {
			//认证医生查询数据
			$list = M('doctor_dp')->where('1=1 AND docid='.intval($userinfo['docid']))->order('addtime desc')->limit($limit.',10')->select();
		} else {
			//普通会员查询数据
			$list = M('doctor_dp')->where('1=1 AND uid='.intval($uid))->order('addtime desc')->limit($limit.',10')->select();
		}
        foreach ($list as $k => $v) {
            $list[$k]['addtime'] = date("Y-m-d H:i",$v['addtime']);
            $userinfo = M('user')->where('id='.intval($v['uid']))->find();
            $docinfo = M('doctor')->where('id='.intval($v['docid']))->find();
            $list[$k]['uname'] = $userinfo['name'];
            $list[$k]['uphoto'] = __DATAURL__.$userinfo['photo'];

            $list[$k]['dname'] = $docinfo['name'];
            $list[$k]['dphoto'] = __DATAURL__.$docinfo['photo_x'];
            $sid = M('order')->where('id='.intval($v['order_id']))->getField('sid');
            $list[$k]['casename'] = M('supply')->where('id='.intval($sid))->getField('casename');
        }

        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
	}


	//***************************
	//  用户 提交医生认证
	//***************************
	public function userAuth(){
		$uid = intval($_REQUEST['uid']);
		$userinfo = M('user')->where('del=0 AND id='.intval($uid))->find();
		if (!$userinfo) {
			echo json_encode(array('status'=>0,'err'=>'用户信息异常.'));
			exit();
		}

		$truename = trim($_REQUEST['truename']);
		$tel = trim($_REQUEST['tel']);
		$idcard = trim($_REQUEST['idcard']);
		$hospital = trim($_REQUEST['hospital']);
		$img1 = trim($_REQUEST['img1']);
		$img2 = trim($_REQUEST['img2']);
		$img3= trim($_REQUEST['img3']);
		$img4= trim($_REQUEST['img4']);

		$imgs = json_encode(array('img1'=>$img1,'img2'=>$img2,'img3'=>$img3,'img4'=>$img4));

		$add = array();
		$add['uid'] = $uid;
		$add['truename'] = $truename;
		$add['bl_number'] = $idcard;
		$add['hospital'] = $hospital;
		$add['tel'] = $tel;
		$add['bl_photo'] = $imgs;
		$add['audit_time'] = time();
		$add['audit'] = 1;
		$res = M('user')->where('id='.intval($uid))->save($add);
		if ($res) {
			echo json_encode(array('status'=>1));
			exit();
		} else {
			echo json_encode(array('status'=>0,'err'=>'保存失败！'));
			exit();
		}
	}

	//***************************
	//  医生会员认证结果查询
	//***************************
	public function getauthresult(){
		$uid = intval($_REQUEST['uid']);
		$info = M('user')->where('id='.intval($uid).' AND del=0')->find();
		if (!$info) {
			echo json_encode(array('status'=>0,'err'=>'用户信息错误.'));
			exit();
		}

		if (intval($info['audit'])==0) {
			echo json_encode(array('status'=>2));
			exit();
		}

		$img = json_decode($info['bl_photo'],true);
		$info['imgs'] = $img['img4'];
		$info['imgs_url'] = __DATAURL__.$img['img4'];
		$info['bl_number'] = substr($info['bl_number'], 0, -4).'****';

		echo json_encode(array('status'=>1,'info'=>$info));
		exit();
	}

	//************************
    //  会员报名培训课程
    //************************
    public function saveinfo(){

        $uid = intval($_POST['uid']);
        $userinfo = M('user')->where('id='.intval($uid).' AND del=0')->find();
        if (!$userinfo) {
            echo json_encode(array('status'=>0,'err'=>'用户信息异常！'));
            exit();
        }

        $docid = intval($userinfo['docid']);
        $check = M('doctor')->where('id='.intval($docid).' AND del=0')->getField('id');
        if (!intval($check)) {
        	echo json_encode(array('status'=>0,'err'=>'认证信息异常！'));
            exit();
        }

        $cid = intval($_POST['cid']);
        if (!$cid) {
            echo json_encode(array('status'=>0,'err'=>'请选择专科！'));
            exit();
        }

        $data = array();
        $data['cid'] = $cid;
        $data['hospital'] = trim($_POST['hospital']);
        $data['position'] = trim($_POST['position']);
        $data['cztime'] = trim($_POST['cztime']);
        $data['service'] = trim($_POST['service']);
        $data['zyjy'] = trim($_POST['zyjy']);
        $data['digest'] = trim($_POST['digest']);
        $data['is_yu'] = intval($_POST['is_yu']);
        $data['is_online'] = intval($_POST['is_online']);
        $data['addtime'] = time();
        $res = M('doctor')->where('id='.intval($docid))->save($data);
        if ($res) {
            echo json_encode(array('status'=>1));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'保存失败，请稍后再试！'));
            exit();
        }
    }

    //************************
    //  会员 调研中心
    //************************
    public function research(){

        $uid = intval($_POST['uid']);
        $check = M('research')->where('uid='.intval($uid))->getField('id');
        if ($check) {
        	echo json_encode(array('status'=>0,'err'=>'您已经参与了这次调研活动哦！'));
        	exit();
        }

        $data = array();
        $data['uid'] = $uid;
        $data['first'] = intval($_POST['first']);
        $data['hours'] = intval($_POST['hours']);
        $data['one'] = intval($_POST['one']);
        $data['two'] = intval($_POST['two']);
        $data['three'] = intval($_POST['three']);
        $data['four'] = intval($_POST['four']);
        $data['five'] = intval($_POST['five']);
        $data['six'] = intval($_POST['six']);
        $data['seven'] = intval($_POST['seven']);
        $data['eight'] = intval($_POST['eight']);
        $data['nine'] = intval($_POST['nine']);
        $data['ten'] = intval($_POST['ten']);
        $data['addtime'] = time();
        $res = M('research')->add($data);
        if ($res) {
            echo json_encode(array('status'=>1));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'保存失败，请稍后再试！'));
            exit();
        }
    }

	//***************************
	//  患者 上传检查资料
	//***************************
	public function uploadimg(){
		$info = $this->upload_images($_FILES['img'],array('jpg','png','jpeg'),"case/".date(Ymd));
		if(is_array($info)) {// 上传错误提示错误信息
			$url = 'UploadFiles/'.$info['savepath'].$info['savename'];
			$xt = $_REQUEST['imgs'];
			if ($xt) {
				$img_url = "Data/".$xt;
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

	//***************************
	//  医生 上传认证资料
	//***************************
	public function uploadphoto(){
		$info = $this->upload_images($_FILES['img'],array('jpg','png','jpeg'),"auth/".date(Ymd));
		if(is_array($info)) {// 上传错误提示错误信息
			$url = 'UploadFiles/'.$info['savepath'].$info['savename'];
			$xt = $_REQUEST['imgs'];
			if ($xt) {
				$img_url = "Data/".$xt;
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

	/**针对涂屠生成唯一订单号
	*@return int 返回16位的唯一订单号
	*/
	public function build_order_no(){
		return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
	}

	//会员登录
	public function login(){
		$uid = intval($_REQUEST['uid']);
		if (!$uid) {
			echo json_encode(array('status'=>0,'err'=>'网络异常！'));
			exit();
		}
		$name = strtolower($_REQUEST['name']);
		$password = $_REQUEST['password'];
		$utype = intval($_REQUEST['utype']);

		if($utype == 1){
			$name2 = strtolower(M('doctor')->where('uid='.$uid)->getField('name'));
			if($name != $name2){
				echo json_encode(array('status'=>0,'err'=>'该医生账号不存在！'));
				exit();
			}
			$password2 = M('doctor')->where('uid='.$uid)->getField('password');
			if($password != $password2){
				echo json_encode(array('status'=>0,'err'=>'账号与密码不匹配！'));
				exit();
			}
			
			echo json_encode(array('status'=>1));
			exit();
		
		}

		if($utype == 2){
			$name2 = strtolower(M('user')->where('id='.$uid)->getField('uname'));
			if($name != $name2){
				echo json_encode(array('status'=>0,'err'=>'该账号不存在！'));
				exit();
			}
			$password2 = M('user')->where('id='.$uid)->getField('pwd');
			if($password != $password2){
				echo json_encode(array('status'=>0,'err'=>'账号与密码不匹配！'));
				exit();
			}
			if($name != $name2 && $password != $password2){
				echo json_encode(array('status'=>1));
				exit();
			}
		}
		echo json_encode(array('status'=>0,'err'=>'网络异常！'));
		exit();
		
	}

	//验证是否已经购买医生咨询
	public function getcode(){
		$uid = intval($_REQUEST['uid']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $docid = intval($_REQUEST['docid']);
        $res = M('order')->where('uid='.$uid.' AND docid='.$docid.' AND status=40')->find();
        $zi_list = M('content')->where('uid='.$uid.' AND docid='.$docid)->select();
        if($zi_list){
        	foreach($zi_list as $k => $v){
        		$zi_list[$k]['addtime'] = date("Y-m-d H:i",$v['addtime']);
        	}
        }
        if($res){
        	echo json_encode(array('status'=>1,'is_buy'=>1,'zi_list'=>$zi_list));
			exit();
        }else{
        	echo json_encode(array('status'=>0,'is_buy'=>0));
			exit();
        }
	}

	//我的订单
	public function myOrder(){
		$uid = intval($_REQUEST['uid']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $list = M('order')->where('uid='.$uid)->select();
        if(!$list){
        	 echo json_encode(array('status'=>0,'err'=>'暂无订单！'));
            exit();
        }
        foreach($list as $k => $v){
        	$list[$k]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
        	if(intval($v['status']) == 40){
        		$list[$k]['status'] = '已支付';
        	}else{
        		$list[$k]['status'] = '未支付';
        	}
        	$list[$k]['doctor'] = M('doctor')->where('id='.intval($v['docid']))->getField('name');
        }
        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
	}

	//检测患者是否有评估和检验报告
	public function check_user(){
		$uid = intval($_REQUEST['uid']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $res = M('guan_evaluate')->where('uid='.$uid)->select();
        $evaluate = 0;
        if($res){
        	$evaluate = 1;
        }
        $res2 = M('report')->where('uid='.$uid)->select();
        $report = 0;
        if($res2){
        	$report = 1;
        }
        $res3 = M('medicine')->where('uid='.$uid)->select();
        $medicine = 0;
        if($res3){
        	$medicine = 1;
        }
        echo json_encode(array('status'=>1,'evaluate'=>$evaluate,'report'=>$report,'medicine'=>$medicine));
        exit();
	}

	//患者咨询
	public function send_content(){
		$uid = intval($_REQUEST['uid']);
        if(!$uid){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        $docid = intval($_REQUEST['docid']);
        if(!$docid){
            echo json_encode(array('status'=>0,'err'=>'医生信息异常！'));
            exit();
        }
        $content = $_REQUEST['content'];
        if(!$content){
            echo json_encode(array('status'=>0,'err'=>'请填写咨询内容！'));
            exit();
        }
        $data['uid'] = $uid;
        $data['docid'] = $docid;
        $data['content'] = $content;
        $data['addtime'] = time();
        $res = M('content')->add($data);
        if($res){
        	echo json_encode(array('status'=>1,'err'=>'提交成功！'));
            exit();
        }else{
        	echo json_encode(array('status'=>0,'err'=>'提交失败！'));
            exit();
        }
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
        echo json_encode(array('status'=>1,'info'=>$info));
        exit();
	}

}