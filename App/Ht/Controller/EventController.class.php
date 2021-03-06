<?php
namespace Ht\Controller;
use Think\Controller;
class EventController extends PublicController{

	/*
	*
	* 构造函数，用于导入外部文件和公共方法
	*/
	public function _initialize(){
		$this->event = M('event_type');
	}


	/*
	*
	* 获取、查询所有订单数据
	*/
	public function index(){
		//搜索
		$name = trim($_REQUEST['name']);
		//构建搜索条件
		$condition = '1=1';
		//根据支付类型搜索
		if ($name) {
			$condition .= " AND name like '%$name%'";
			$this->assign('name',$name);
		}
		//分页
		$count   = $this->event->where($condition)->count();// 查询满足要求的总记录数
		$Page    = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)

		//头部描述信息，默认值 “共 %TOTAL_ROW% 条记录”
		$Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
		//上一页描述信息
	    $Page->setConfig('prev', '上一页');
	    //下一页描述信息
	    $Page->setConfig('next', '下一页');
	    //首页描述信息
	    $Page->setConfig('first', '首页');
	    //末页描述信息
	    $Page->setConfig('last', '末页');
	    /*
	    * 分页主题描述信息 
	    * %FIRST%  表示第一页的链接显示  
	    * %UP_PAGE%  表示上一页的链接显示   
	    * %LINK_PAGE%  表示分页的链接显示
	    * %DOWN_PAGE% 	表示下一页的链接显示
	    * %END%   表示最后一页的链接显示
	    */
	    $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');

		$show    = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = array();
		$list = $this->event->where($condition)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		//echo $where;
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板

	}

	/**
	 * [add 添加/编辑视频]
	 */
	public function add(){
		$id=I('get.id');		
		if(IS_POST){
			$array=array();
			$post=I('post.');
			$array['name']=$post['name'];
			if($post['id']>0){
				$re=M("event_type")->where("id=".$post['id'])->save($array);
			}else{
				$re=M("event_type")->add($array);
			}
			if($re>0){
				$this->success("提交成功！",'index');
			}else{
				$this->error("提交失败！");
			}

		}else{
			if($id>0){
				$event=M("event_type")->where("id=$id")->find();
				$this->assign("v",$event);
			}
			$this->display();
		}
		

	}
	/*
	*
	* 删除
	*/
	public function del(){
		//获取广告id，查询数据库是否有这条数据
		$id = intval($_REQUEST['id']);
		$check_info = $this->event->where('id='.intval($id))->find();
		if (!$check_info) {
			$this->error('参数错误！');
			die();
		}

		//修改对应的显示状态
		$up = $this->event->where('id='.intval($id))->delete();
		if ($up) {
			$this->success('操作成功.','index');
		}else{
			$this->error('操作失败.');
		}
	}



}