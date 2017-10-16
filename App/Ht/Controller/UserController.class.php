<?php
namespace Ht\Controller;
use Think\Controller;
class UserController extends PublicController{

	//*************************
	// 普通会员的管理
	//*************************
	public function index(){
		$aaa_pts_qx=1;
		$type=$_GET['type'];
		$id=(int)$_GET['id'];
		$tel = trim($_REQUEST['tel']);
		$name = trim($_REQUEST['name']);

		$names=$this->htmlentities_u8($_GET['name']);
		//搜索
		$where="1=1";
		$name!='' ? $where.=" and name like '%$name%'" : null;
		$tel!='' ? $where.=" and tel like '%$tel%'" : null;

		define('rows',20);
		$count=M('user')->where($where)->count();
		$rows=ceil($count/rows);

		$page=(int)$_GET['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$userlist=M('user')->where($where)->order('id desc')->limit($limit,rows)->select();
		$page_index=$this->page_index($count,$rows,$page);
		foreach ($userlist as $k => $v) {
			$userlist[$k]['addtime']=date("Y-m-d H:i",$v['addtime']);
		}
		//====================
		// 将GET到的参数输出
		//=====================
		$this->assign('name',$name);
		$this->assign('tel',$tel);

		//=============
		//将变量输出
		//=============
		$this->assign('page_index',$page_index);
		$this->assign('page',$page);
		$this->assign('userlist',$userlist);
		$this->display();	
	}

	//*************************
	// 医生会员 审核管理
	//*************************
	public function audit(){
		$tel = trim($_REQUEST['tel']);
		$name = trim($_REQUEST['name']);
		$truename = trim($_REQUEST['truename']);
		//搜索
		$where="1=1 AND audit=1";
		$name!='' ? $where.=" and name like '%$name%'" : null;
		$tel!='' ? $where.=" and tel like '%$tel%'" : null;
		$truename!='' ? $where.=" and truename like '%$truename%'" : null;

		define('rows',20);
		$count=M('user')->where($where)->count();
		$rows=ceil($count/rows);

		$page=(int)$_GET['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$userlist=M('user')->where($where)->order('id desc')->limit($limit,rows)->select();
		$page_index=$this->page_index($count,$rows,$page);
		foreach ($userlist as $k => $v) {
			$userlist[$k]['audit_time']=date("Y-m-d H:i",$v['audit_time']);
		}

		//====================
		// 将GET到的参数输出
		//=====================
		$this->assign('name',$name);
		$this->assign('tel',$tel);
		$this->assign('truename',$truename);

		//=============
		//将变量输出
		//=============
		$this->assign('page_index',$page_index);
		$this->assign('page',$page);
		$this->assign('userlist',$userlist);
		$this->display();	
	}

	//*************************
	//  已审核管理
	//*************************
	public function audited(){
		$tel = trim($_REQUEST['tel']);
		$name = trim($_REQUEST['name']);
		$truename = trim($_REQUEST['truename']);
		//搜索
		$where="1=1 AND audit>1";
		$name!='' ? $where.=" and name like '%$name%'" : null;
		$tel!='' ? $where.=" and tel like '%$tel%'" : null;
		$truename!='' ? $where.=" and truename like '%$truename%'" : null;

		define('rows',20);
		$count=M('user')->where($where)->count();
		$rows=ceil($count/rows);

		$page=(int)$_GET['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$userlist=M('user')->where($where)->order('id desc')->limit($limit,rows)->select();
		$page_index=$this->page_index($count,$rows,$page);
		foreach ($userlist as $k => $v) {
			$imgs = json_decode($v['bl_photo'],true);
			$userlist[$k]['img4'] = $imgs['img4'];
			$userlist[$k]['audit_time']=date("Y-m-d H:i",$v['audit_time']);
		}

		//====================
		// 将GET到的参数输出
		//=====================
		$this->assign('name',$name);
		$this->assign('tel',$tel);
		$this->assign('truename',$truename);

		//=============
		//将变量输出
		//=============
		$this->assign('page_index',$page_index);
		$this->assign('page',$page);
		$this->assign('userlist',$userlist);
		$this->display();	
	}

	//*************************
	// 企业会员审核页面
	//*************************
	public function user_audit() {
		$aaa_pts_qx=1;
		$type=$_GET['type'];
		$id=(int)$_GET['id'];
		$check = M('user')->where('id='.intval($id).' AND del=0')->find();
		if (!$check) {
			$this->error('用户信息异常！');
			exit();
		}
		if (intval($check['audit'])!=1) {
			$this->error('会员审核状态异常！');
			exit();
		}

		$img = json_decode($check['bl_photo'],true);
		$check['img1'] = $img['img1'];
		$check['img2'] = $img['img2'];
		$check['img3'] = $img['img3'];
		$check['img4'] = $img['img4'];

		$this->assign('info',$check);
		$this->display();
	}

	//*************************
	// 医生会员 审核
	//*************************
	public function shenhe(){
		$id = intval($_POST['id']);
		$check = M('user')->where('id='.intval($id).' AND del=0')->find();
		if (!$check) {
			$this->error('用户信息异常！');
			exit();
		}

		$audit = intval($_POST['audit']);
		$reason = trim($_POST['reason']);

		$up = array();
		$up['audit'] = $audit;
		$up['reason'] = $reason;
		if ($audit==2) {
			$up['type'] = 2;
		}

		$res = M('user')->where('id='.intval($id))->save($up);
		if ($res) {
			if ($audit == 2) {
				//新增一条医生数据
				$img = json_decode($check['bl_photo'],true);
				$add = array();
				$add['name'] = $check['truename'];
				$add['hospital'] = $check['hospital'];
				$add['photo'] = $img['img3'];
				$add['photo_x'] = $img['img4'];
				$add['addtime'] = time();
				$add['updatetime'] = time();
				$add['cid'] = 10;
				$add['tel'] = $check['tel'];
				$docid = M('doctor')->add($add);
				if (!$docid) {
					M('user')->where('id='.intval($id))->save(array('audit'=>1,'reason'=>'','type'=>1));
					$this->error('操作失败！'.__LINE__);
					exit();
				}
				M('user')->where('id='.intval($id))->save(array('docid'=>$docid));
			}
			$this->success('操作成功！','audited');
			exit();
		}else{
			$this->error('操作失败！');
			exit();
		}
	}

	//*************************
	// 医生会员 解除认证
	//*************************
	public function remove () {
		$id = intval($_REQUEST['id']);
		$check = M('user')->where('id='.intval($id).' AND del=0')->find();
		if (!$check) {
			$this->error('用户信息异常！');
			exit();
		}

		$up = array();
		$up['audit'] = 0;
		$up['reason'] = '';
		$up['type'] = 1;
		$up['audit_time'] = time();
		$res = M('user')->where('id='.intval($id))->save($up);
		if ($res) {
			if (intval($res['docid'])>0) {
				M('doctor')->where('id='.intval($res['docid']))->save(array('del'=>1,'del_time'=>time()));
			}
			
			$this->success('解除成功！');
			exit();
		}else{
			$this->error('解除失败！');
			exit();
		}
	}

	/*
	*
	*  点击查看图片
	*/
	public function getimg(){
		$imgs = $_REQUEST['imgs'];
		$this->assign('img',$imgs);
		$this->display();
	}


	public function del()
	{
		$id = intval($_REQUEST['did']);
		$info = M('user')->where('id='.intval($id))->find();
		if (!$info) {
			$this->error('会员信息错误.'.__LINE__);
			exit();
		}

		$data=array();
		$data['del'] = $info['del'] == '1' ?  0 : 1;
		$up = M('user')->where('id='.intval($id))->save($data);
		if ($up) {
			$this->redirect('User/index',array('page'=>intval($_REQUEST['page'])));
			exit();
		}else{
			$this->error('操作失败.');
			exit();
		}
	}	
}