<?php
namespace Ht\Controller;
use Think\Controller;
class TixianController extends PublicController{

	/*
	*
	* 构造函数，用于导入外部文件和公共方法
	*/
	public function _initialize(){
		$this->tixian = M('tixian');
	}

	/*
	*
	* 获取、查询栏目表数据
	*/
	public function index(){
		$tixian = M('tixian')->select();
		foreach ($tixian as $k => $v) {
			$tixian[$k]['addtime'] = date('Y-m-d H-i-s',$v['addtime']);
			$tixian[$k]['uname'] = M('user')->where('id='.intval($v['uid']))->getField('uname');
		}
		$this->assign('tixian',$tixian);
		$this->display();

	}


	/*
	*
	* 跳转添加或修改栏目页面
	*/
	public function add(){
		//如果是修改，则查询对应分类信息
		if (intval($_GET['cid'])) {
			$cate_id = intval($_GET['cid']);
		
			$cate_info = $this->doctor_cat->where('id='.intval($cate_id).' AND del=0')->find();
			if (!$cate_info) {
				$this->error('没有找到相关信息.');
				exit();
			}
			$this->assign('cate_info',$cate_info);
		}
		$this->display();
	}


	/*
	*
	*  设置状态
	*/
	public function set_cl(){
		$id = intval($_REQUEST['id']);
		$info = $this->tixian->where('id='.intval($id))->find();
		if (!$info) {
			$this->error('操作失败.'.__LINE__);
			exit();
		}
		$data=array();
		$data['status'] = $info['status'] == '1' ?  0 : 1;
		$up = $this->tixian->where('id='.intval($id))->save($data);
		if ($up) {
			$this->success('操作成功！');
		}else{
			$this->error('操作失败！');
		}
	}

	/*
	*
	* 栏目删除
	*/
	public function del(){
		//以后删除还要加权限登录判断
		$id = intval($_GET['id']);
		$check_info = $this->tixian->where('id='.intval($id))->find();
		if (!$check_info) {
			$this->error('操作失败.'.__LINE__);
			exit();
		}

		$res = $this->tixian->where('id='.intval($id))->delete();
		if ($res) {
			$this->success('删除成功！');
		}else{
			$this->error('操作失败！');
		}
	}

}