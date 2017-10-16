<?php
namespace Ht\Controller;
use Think\Controller;
class HospitalController extends PublicController{
	//**********************************************
	//说明：店铺管理 推荐 修改 删除 列表 搜索
	//**********************************************
	public function index(){
		//===================
		// GET获得的数据集合
		//===================
		$tuijian=$_GET['tuijian']!=NULL ? (int)$_GET['tuijian'] : '';
		$name=$this->htmlentities_u8($_GET['name']);
		$sheng=$_GET['sheng']!=NULL ? (int)$_GET['sheng'] : '';
		$city=$_GET['city']!=NULL ? (int)$_GET['city'] : '';
		$quyu=$_GET['quyu']!=NULL ? (int)$_GET['quyu'] : '';
		
		//===================================================
		// 查询省市区数据,先将省查出来,后面用ajax+js将市区补上
		//===================================================
		$output_sheng=$this->city_option($sheng,0,1);
		$output_city=$this->city_option($city,$sheng);
		$output_quyu=$this->city_option($quyu,$city);

		//===============================
		// 数据查询和搜索
		//===============================
		$where="status=1";
		$name!='' ? $where.=" and name like '%$name%'" : null;
		//地区搜索
		if($quyu>0){
			$where.=" and quyu=$quyu";
		}elseif($city>0){
			$where.=" and city=$city";
		}elseif($sheng>0){
			$where.=" and sheng=$sheng";
		}
		define('rows',20);
		$count=M('hospital')->where($where)->count();
		$rows=ceil($count/rows);
		$page=(int)$_GET['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$page_index=$this->page_index($count,$rows,$page);
		$hospital=M('hospital')->where($where)->order('addtime desc')->limit($limit,rows)->select();
		//组装数据
		foreach ($hospital as $k => $v) {
			$sheng=M('ChinaCity')->where('id='.intval($v['sheng']))->find();
			$hospital[$k]['logo']=$v['logo'];
			$hospital[$k]['zn-sheng']=$sheng['name'];
			$hospital[$k]['cname'] = M('sccat')->where('id='.intval($v['cid']))->getField('name');
		}
		//==========================
		// 将GET到的数据再输出
		//==========================
		$this->assign('tuijian',$tuijian);
		$this->assign('name',$name);
		$this->assign('sheng',$sheng);
		$this->assign('city',$city);
		$this->assign('quyu',$quyu);
		//=============
		// 将变量输出
		//=============	
		$this->assign('output_sheng',$output_sheng);
		$this->assign('output_city',$output_city);
		$this->assign('output_quyu',$output_quyu);
		$this->assign('page_index',$page_index);
		$this->assign('hospital',$hospital);
		$this->display();
	}

	//**********************************************
	//说明：店铺管理 添加 修改
	//**********************************************
	public function add(){	
		$id=(int)$_GET['id'];
		//==================================
		// GET获得的数据集合
		//==================================
		$page=$_GET['page'];
		$tuijian=$_GET['tuijian']!=NULL ? (int)$_GET['tuijian'] : '';
		$name=$this->htmlentities_u8($_GET['name']);

		if($_SESSION['admininfo']['qx']==4){
			$sheng=$_GET['sheng']!=NULL ? (int)$_GET['sheng'] : '';
			$city=$_GET['city']!=NULL ? (int)$_GET['city'] : '';
			$quyu=$_GET['quyu']!=NULL ? (int)$_GET['quyu'] : '';
		}else{
			$scq=M('hospital')->where('id='.(int)$_SESSION['admininfo']['shop_id'])->find();
			$sheng=$scq['sheng'];
			$city=$scq['city'];
			$quyu=$scq['quyu'];
		}
		//dump($scq);exit;
		//==========================================
		// 组装post过来的数据进行处理添加
		//==========================================
		if($_POST['submit']==true){
		   $id = intval($_POST['id']);
		   if($_POST['location']!=''){
			   $location=explode(',',$_POST['location']);
		   }
		   //组装一个省市区的名字出来
		   $post_sheng=M('ChinaCity')->where('id='.(int)$_POST['sheng'])->getField('name');
		   $post_city =M('ChinaCity')->where('id='.(int)$_POST['city'])->getField('name');
		   $post_quyu =M('ChinaCity')->where('id='.(int)$_POST['quyu'])->getField('name');
		   $array=array(
				'name' => $_POST['name'] ,
				'uname' => $_POST['uname'] ,
				'sheng' => (int)$_POST['sheng'] ,
				'city' => (int)$_POST['city'] ,
				'quyu' => (int)$_POST['quyu'] ,
				'address' => $_POST['address'] ,
				'address_xq' => $post_sheng.' '.$post_city.' '.$post_quyu.' '. $_POST['address'] ,
				'location_x' => $location[1] ,
				'location_y' => $location[0] ,
				'tel' => $_POST['tel'] ,
				'utel' => $_POST['utel'] ,
				'content' => $_POST['content'] ,
				'sort' => $_POST['sort'] ,
				'updatetime' => time() ,
				'status' =>  1 ,
				'main_hy' => $_POST['main_hy'] ,
				'intro' => $_POST['intro'] ,
				'cid'=> intval($_POST['cid'])
		    );
				  

			//logo上传处理
			if (!empty($_FILES["logo"]["tmp_name"])) {
				//文件上传
				$info2 = $this->upload_images($_FILES["logo"],array('jpg','png','jpeg'),"hospital/logo/".date(Ymd));
				if(!is_array($info2)) {// 上传错误提示错误信息
					$this->error($info2);
					exit();
				}else{// 上传成功 获取上传文件信息
					$array['logo'] = 'UploadFiles/'.$info2['savepath'].$info2['savename'];
					if (intval($id)) {
						$check_logo = M('hospital')->where('id='.intval($id))->getField('logo');
						$url = "Data/".$check_logo;
						if (file_exists($url) && $check_logo) {
							@unlink($url);
						}
					}
				}
			}

			//多张广告图上传
			$up_arr = array();
				if (!empty($_FILES["files"]["tmp_name"])) {
					foreach ($_FILES["files"]['name'] as $k => $val) {
						$up_arr[$k]['name'] = $val;
					}

					foreach ($_FILES["files"]['type'] as $k => $val) {
						$up_arr[$k]['type'] = $val;
					}

					foreach ($_FILES["files"]['tmp_name'] as $k => $val) {
						$up_arr[$k]['tmp_name'] = $val;
					}

					foreach ($_FILES["files"]['error'] as $k => $val) {
						$up_arr[$k]['error'] = $val;
					}

					foreach ($_FILES["files"]['size'] as $k => $val) {
						$up_arr[$k]['size'] = $val;
					}
				}

				$adv_str = '';
				if ($up_arr) {
					$res=array();
					foreach ($up_arr as $key => $value) {
						$res = $this->upload_images($value,array('jpg','png','jpeg'),"hospital/advimg/".date(Ymd));
					    if(is_array($res)) {
					    	// 上传成功 获取上传文件信息保存数据库
					    	$adv_str .= ','.'UploadFiles/'.$res['savepath'].$res['savename'];
					    }
					}
				}
		   
		  if($id>0){
		  	$adv_img = M('hospital')->where('id='.intval($id))->getField('vip_char');
				if ($adv_str!='') {
					if ($adv_img!='') {
						$array['vip_char'] = $adv_img.$adv_str;
					}else{
						$array['vip_char'] = $adv_str;
					}
				}
			  //将空数据排除掉，防止将原有数据空置
			foreach ($array as $k => $v) {
			  	if(empty($v)){
			  	 	unset($v);
			  	}
			}
			$partner =M('hospital')->where('id='.intval($id))->save($array);
		  }else{
		  	  $array['vip_char']=$adv_str;
			  $array['addtime']=time();
			  $partner =M('hospital')->add($array);
			  $id = $partner;
		  }
		  if($partner){			  
			   echo '
			   <script>
				  location= !confirm("操作成功！\n是否继续操作？") ? "?page='.$page.'name='.$name.'&sheng='.$sheng.'&city='.$city.'&quyu='.$quyu.'&tiaojian='.$tiaojian.'" : "?id='.$id.'&page='.$page.'&name='.$name.'&sheng='.$sheng.'&city='.$city.'&quyu='.$quyu.'&tiaojian='.$tiaojian.'";
			   </script>';
		   }else{
			   $this->error('保存失败！');
			   exit();
		  	}
		}

		//=============================
		// 查询店铺的所有信息出来
		//=============================
		$hospital= $id>0 ? M('hospital')->where('id='.$id)->find() : '';
		//=======================================================
		// 查询省市区数据,先将省查出来,后面用ajax+js将市区补上
		//=======================================================
		$output_sheng=$this->city_option($sheng,0,1);
		$output_city=$this->city_option($city,$sheng);
		$output_quyu=$this->city_option($quyu,$city);

		//获取所有轮播图
		if ($hospital['vip_char']) {
			$img_str = explode(',', trim($hospital['vip_char'],','));
			$this->assign('img_str',$img_str);
		}

		//==========================
		// 获取所有医院等级
		//==========================
		$cate_list = M('sccat')->where('1=1')->order('addtime asc')->select();
		$this->assign('cate_list',$cate_list);
		//==========================
		// 将GET到的数据再输出
		//==========================
		$this->assign('id',$id);
		$this->assign('page',$page);
		$this->assign('name',$name);
		$this->assign('tuijian',$tuijian);
		$this->assign('sheng',$sheng);
		$this->assign('city',$city);
		$this->assign('quyu',$quyu);
		//=========================
		// 将变量输出
		//=========================	
		$this->assign('output_sheng',$output_sheng);
		$this->assign('output_city',$output_city);
		$this->assign('output_quyu',$output_quyu);
		$this->assign('hospital',$hospital);
		$this->display();
		
	}

	//***************************
	//说明：产品 设置推荐
	//***************************
	public function set_tj(){
		$id = intval($_REQUEST['id']);
		$tj_update=M('hospital')->where('id='.intval($id).' AND status=1')->find();
		if (!$tj_update) {
			$this->error('医院信息错误！');
			exit();
		}

		//查推荐type
		$data = array();
		$data['type'] = $tj_update['type']==1 ? 0 : 1;
		$up = M('hospital')->where('id='.intval($id))->save($data);
		if ($up) {
			$this->redirect('index',array('page'=>intval($_REQUEST['page'])));
			exit();
		}else{
		    $this->error('操作失败！');
			exit();
		}
	}

	//***************************
	//说明：产品 删除
	//***************************
	public function del()
	{
		$id = intval($_REQUEST['did']);
		$info = M('hospital')->where('id='.intval($id))->find();
		if (!$info) {
			$this->error('商家不存在！'.__LINE__);
			exit();
		}

		$data=array();
		$data['status'] = $info['status'] == '1' ?  0 : 1;
		$up = M('hospital')->where('id='.intval($id))->save($data);
		if ($up) {
			$this->redirect('index',array('page'=>intval($_REQUEST['page'])));
			exit();
		}else{
			$this->error('操作失败.');
			exit();
		}
	}

	/*
	* 商品单张广告图片删除
	*/
	public function img_del(){
		$img_url = trim($_REQUEST['img_url']);
		$shop_id = intval($_REQUEST['shop_id']);
		$check_info = M('hospital')->where('id='.intval($shop_id))->find();
		if (!$check_info) {
			echo json_encode(array('status'=>0,'err'=>'数据信息错误！'));
			exit();
		}

		$arr = explode(',', trim($check_info['vip_char'],','));
		if (in_array($img_url, $arr)) {
			foreach ($arr as $k => $v) {
				if ($img_url===$v) {
					unset($arr[$k]);
					@unlink('Data/'.$v);
				}
			}
			$data = array();
			$data['vip_char'] = implode(',', $arr);
			$res = M('hospital')->where('id='.intval($shop_id))->save($data);
			if (!$res) {
				echo json_encode(array('status'=>0,'err'=>'操作失败！'.__LINE__));
				exit();
			}
			//删除服务器上传文件
			$url = "Data/".$img_url;
			if (file_exists($url)) {
				@unlink($url);
			}

			echo json_encode(array('status'=>1));
			exit();
		}else{
			echo json_encode(array('status'=>0,'err'=>'操作失败！'.__LINE__));
			exit();
		}
	}

	//******************************
	//说明：百度坐标捕获
	//******************************
	public function baidumap(){
		$this->display();
	}

	//**********************************************
	//说明：跳转店铺修改页面
	//**********************************************
	public function password(){
		//判断session里面是否有登录商家id
		if (!$_SESSION['admininfo']['id']) {
			$this->error('非法操作.');
			exit;
		}

		//**********************************************
		//说明：接收新密码，修改数据
		//**********************************************
		if ($_POST['submit']==true) {
			$old_pwd = $_POST['old_password'];
			$pwd = $_POST['password'];

			//**********************************************
			//说明：获取会员密码，判断是否为空，是否和新密码匹配
			//**********************************************
			$user_info = M('adminuser')->where('id='.intval($_SESSION['admininfo']['id']).' AND del=0')->find();
			if (!$user_info) {
				$this->error('系统错误，请售后再试.');
				exit;
			}

			if ($user_info['pwd']!==md5(md5($old_pwd))) {
				$this->error('原始密码错误.');
				exit;
			}

			$up=array();
			$up['pwd']=md5(md5($pwd));
			$result = M('adminuser')->where('id='.intval($_SESSION['admininfo']['id']))->save($up);
			if ($result) {
				$this->success('操作成功.');
				die();
			}else{
				$this->error('修改失败。请稍后再试.');
				die();
			}
		}

		$this->display();
	}
}