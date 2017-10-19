<?php
namespace Ht\Controller;
use Think\Controller;
class MedicalController extends PublicController{

	/*
	*
	* 构造函数，用于导入外部文件和公共方法
	*/
	public function _initialize(){
		$this->medical = M('medical');
	}


	/*
	*
	* 获取、查询所有订单数据
	*/
	public function index(){
		//搜索
		$name = trim($_REQUEST['name']);
		$type=I('get.type');
		if($type){
			$this->assign("type",$type);
		}
		//构建搜索条件
		$condition = '1=1';
		$condition .= ' AND del = 0'; 
		//根据支付类型搜索
		if ($name) {
			$condition .= " AND name like '%$name%'";
			$this->assign('name',$name);
		}
		//分页
		$count   = $this->medical->where($condition)->count();// 查询满足要求的总记录数
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
		$list = $this->medical->where($condition)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		//echo $where;
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板

	}

	/**
	 * [add 添加/编辑视频]
	 */
	public function add(){
		ini_set('max_execution_time',0);
		$id=I('get.id');		
		if(IS_POST){
			$array=array();
			$post=I('post.');
			$array['name']=$post['name'];
			$array['cid']=$post['cid'];
			$array['digest']=$post['digest'];
			$array['addtime']=time();
			$array['content']=$post['content'];
			$array['video']=$post['video'] ? $post['video'] : '';
			$array['type']=$post['type'];
			// dump($_REQUEST);
			// dump($_FILES);
			// exit;
			//上传产品大图$file 文件数据流 $exts 文件类型 $path 子目录名称
			if (!empty($_FILES["photo"]["tmp_name"])) {
				//文件上传
				$info = $this->upload_images($_FILES["photo"],array('jpg','png','jpeg'),"medicalimg/".date(Ymd));
			    if(!is_array($info)) {// 上传错误提示错误信息
			        $this->error($info);
			        exit();
			    }else{// 上传成功 获取上传文件信息
				    $array['photo'] = 'UploadFiles/'.$info['savepath'].$info['savename'];
			    }
			}
			//上传产品大图$file 文件数据流 $exts 文件类型 $path 子目录名称
			if (!empty($_FILES['video']['tmp_name'])) {
				//文件上传
				$video = $this->upload_images($_FILES["video"],array('mp4','wmv'),"videofiles/".date(Ymd));
			    if(!is_array($video)) {// 上传错误提示错误信息
			        $this->error($video);
			        exit();
			    }else{// 上传成功 获取上传文件信息
				    $array['video'] = 'UploadFiles/'.$video['savepath'].$video['savename'];
			    }
			}
			if($post['id']>0){
				$re=M("medical")->where("id=".$post['id'])->save($array);
			}else{
				$re=M("medical")->add($array);
			}
			if($re>0){
				$this->success("提交成功！");
			}else{
				$this->error("提交失败！");
			}

		}else{
			if($id>0){
				$medical=M("medical")->where("id=$id")->find();
				$this->assign("v",$medical);
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
		$check_info = $this->video->where('id='.intval($id))->find();
		if (!$check_info) {
			$this->error('参数错误！');
			die();
		}

		//修改对应的显示状态
		$up = $this->video->where('id='.intval($id))->delete();
		if ($up) {
			$this->success('操作成功.','index');
		}else{
			$this->error('操作失败.');
		}
	}



}