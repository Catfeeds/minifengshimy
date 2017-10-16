<?php
namespace Ht\Controller;
use Think\Controller;
class DoctorController extends PublicController{
	//***********************************************
    public static $Array;//这个给检查产品的字段用 
    public static $PRO_FENLEI; //这个给产品分类打勾用
	//**************************************************
	//**********************************************
	//说明：产品列表管理 推荐 修改 删除 列表 搜索
	//**********************************************
	public function index(){
		$aaa_pts_qx=1;
		$id=(int)$_GET['id'];
		$shop_id=(int)$_GET['shop_id'];

		//搜索变量
		$type=$this->htmlentities_u8($_GET['type']);
		$tuijian=$this->htmlentities_u8($_GET['tuijian']);
		$name=$this->htmlentities_u8($_GET['name']);
		//===============================
		// 产品列表信息 搜索
		//===============================
		//搜索
		$where="1=1 AND del=0";
		$tuijian!=='' ? $where.=" AND type=$tuijian" : null;
		$shop_id>0 ? $where.=" AND shop_id=$shop_id" : null;
		$name!='' ? $where.=" AND name like '%$name%'" : null;
		define('rows',20);
		$count=M('doctor')->where($where)->count();
		$rows=ceil($count/rows);
		$page=(int)$_GET['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$page_index=$this->page_index($count,$rows,$page);
		$productlist=M('doctor')->where($where)->order('addtime desc')->limit($limit,rows)->select();
		//dump($productlist);exit;
		foreach ($productlist as $k => $v) {
			//$productlist[$k]['shangchang'] = M('shangchang')->where('id='.intval($v['shop_id']))->getField('name');
			//$productlist[$k]['cname'] = M('pro_cat')->where('id='.intval($v['cid']))->getField('name');
		}

		//==========================
		// 将GET到的数据再输出
		//==========================
		$this->assign('id',$id);
		$this->assign('tuijian',$tuijian);
		$this->assign('name',$name);
		$this->assign('type',$type);
		$this->assign('shop_id',$shop_id);
		$this->assign('page',$page);
		//=============
		// 将变量输出
		//=============	
		$this->assign('productlist',$productlist);
		$this->assign('page_index',$page_index);
		$this->display();
	}
	//**********************************************
	//说明：产品 添加修改
	//注意：cid 分类id  shop_id店铺id
	//**********************************************
	public function add(){	

		$id=(int)$_GET['id'];
		$page=(int)$_GET['page'];
		$name=$_GET['name'];
		$type=$_GET['type'];

		if($_POST['submit']==true){
		try{	
			//如果不是管理员则查询商家会员的店铺ID
			$id = intval($_POST['pro_id']);
			$array=array(
				'name'=>$_POST['name'] ,
				'uid'=>$_POST['uid'] ,
				'password'=>$_POST['password'] ,
				'hospital'=>$_POST['hospital'] ,
				'position'=>$_POST['position'] ,
				'cztime'=>$_POST['cztime'] ,
				'price' => floatval($_POST['price']),
				'shop_id'=> intval($_POST['shop_id']) ,//所属店铺
				'cid'=> intval($_POST['cid']) ,			//产品分类ID
				'sort'=>(int)$_POST['sort'] , 
				'updatetime'=>time(),
				'digest'=>$_POST['digest'] ,
				'zyjy'=>$_POST['zyjy'] ,
				'tel' => trim($_POST['tel']),
				'is_yu' => intval($_POST['is_yu']),
				'is_online' => intval($_POST['is_online']),
				'service'=>trim($_POST['service']),
				'renqi' => intval($_POST['renqi']),
			);
			  
			//判断产品详情页图片是否有设置宽度，去掉重复的100%
			// if(strpos($array['content'],'width="100%"')){
			// 	$array['content']=str_replace(' width="100%"','',$array['content']);
			// }
			// //为img标签添加一个width
			// $array['content']=str_replace('alt=""','alt="" width="100%"',$array['content']);
		  
			//上传产品小图
			if (!empty($_FILES["photo_x"]["tmp_name"])) {
					//文件上传
					$info = $this->upload_images($_FILES["photo_x"],array('jpg','png','jpeg'),"doctor/".date(Ymd));
				    if(!is_array($info)) {// 上传错误提示错误信息
				        $this->error($info);
				        exit();
				    }else{// 上传成功 获取上传文件信息
					    $array['photo_x'] = 'UploadFiles/'.$info['savepath'].$info['savename'];
					    $xt = M('doctor')->where('id='.intval($id))->field('photo_x')->find();
					    if ($id && $xt['photo_x']) {
					    	$img_url = "Data/".$xt['photo_x'];
							if(file_exists($img_url)) {
								@unlink($img_url);
							}
					    }
				    }
			}

			if (!empty($_FILES["photo"]["tmp_name"])) {
					//文件上传
					$info = $this->upload_images($_FILES["photo"],array('jpg','png','jpeg'),"doctor/".date(Ymd));
				    if(!is_array($info)) {// 上传错误提示错误信息
				        $this->error($info);
				        exit();
				    }else{// 上传成功 获取上传文件信息
					    $array['photo'] = 'UploadFiles/'.$info['savepath'].$info['savename'];
					    $xt = M('doctor')->where('id='.intval($id))->field('photo')->find();
					    if ($id && $xt['photo']) {
					    	$img_url = "Data/".$xt['photo'];
							if(file_exists($img_url)) {
								@unlink($img_url);
							}
					    }
				    }
			}
			
			//执行添加
			if(intval($id)>0){
				//将空数据排除掉，防止将原有数据空置
				foreach ($array as $k => $v) {
					if(empty($v)){
					  	unset($v);
					}
				}

				$sql = M('doctor')->where('id='.intval($id))->save($array);
			}else{
				$array['addtime']=time();
				$sql = M('doctor')->add($array);
				$id=$sql;
			}

			//规格操作
			if($sql){//name="guige_name[]
				$this->success('操作成功.');
				exit();
			}else{
				throw new \Exception('操作失败.');
			}
			  
			}catch(\Exception $e){
				echo "<script>alert('".$e->getMessage()."');location='{:U('index')}?shop_id=".$shop_id."';</script>";
			}
		}

		//=========================
		// 查询所有分科
		//=========================
		$cate_list = M('doctor_cat')->where('del=0')->field('id,name')->select();
		$this->assign('cate_list',$cate_list);

		//=========================
		// 查询产品信息
		//=========================
		$pro_allinfo= $id>0 ? M('doctor')->where('id='.$id)->find() : array();
		if($pro_allinfo){
			$pro_allinfo['user_name'] = M('user')->where('id='.$pro_allinfo['uid'])->getField('name');
		}

		//==========================
		// 将GET到的数据再输出
		//==========================
		$this->assign('id',$id);
		$this->assign('name',$name);
		$this->assign('type',$type);
		$this->assign('shop_id',$shop_id);
		$this->assign('page',$page);
		//=============
		// 将变量输出
		//=============	
		$this->assign('pro_allinfo',$pro_allinfo);
		$this->display();

	}

	/*
	* 获取二级分类
	*/
	public function getcid(){
		$cateid = intval($_REQUEST['cateid']);
		$catelist = M('pro_cat')->where('tid='.intval($cateid).' AND del=0')->field('id,name')->select();
		echo json_encode(array('hlist'=>$catelist));
		exit();
	}

	/*
	* 商品单张图片删除
	*/
	public function img_del(){
		$img_url = trim($_REQUEST['img_url']);
		$pro_id = intval($_REQUEST['pro_id']);
		$check_info = M('doctor')->where('id='.intval($pro_id).' AND del=0')->find();
		if (!$check_info) {
			echo json_encode(array('status'=>0,'err'=>'律师信息有误！'));
			exit();
		}

		$arr = explode(',', trim($check_info['adv_img'],','));
		if (in_array($img_url, $arr)) {
			foreach ($arr as $k => $v) {
				if ($img_url===$v) {
					unset($arr[$k]);
				}
			}
			$data = array();
			$data['adv_img'] = implode(',', $arr);
			$res = M('doctor')->where('id='.intval($pro_id))->save($data);
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

	//***************************
	//说明：产品 设置推荐
	//***************************
	public function set_tj(){
		$pro_id = intval($_REQUEST['pro_id']);
		$tj_update=M('doctor')->field('shop_id,type')->where('id='.intval($pro_id).' AND del=0')->find();
		if (!$tj_update) {
			$this->error('律师信息有误！');
			exit();
		}

		// $shopinfo = M('shangchang')->where('id='.intval($tj_update['shop_id']))->find();
		// //查status,不符合条件不给通过
		// if(intval($shopinfo['status']) != 1) { 
		//     $this->error('商家未通过审核，产品不能设置推荐.');
		//     exit;
		// }

		//查推荐type
		//dump($tj_update);
		$data = array();
		$data['type'] = $tj_update['type']==1 ? 0 : 1;
		$up = M('doctor')->where('id='.intval($pro_id))->save($data);
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
		$info = M('doctor')->where('id='.intval($id))->find();
		if (!$info) {
			$this->error('律师信息有误.'.__LINE__);
			exit();
		}

		if (intval($info['del'])==1) {
			$this->success('操作成功！.'.__LINE__);
			exit();
		}

		$data=array();
		$data['del'] = $info['del'] == '1' ?  0 : 1;
		$data['del_time'] = time();
		$up = M('doctor')->where('id='.intval($id))->save($data);
		if ($up) {
			$this->redirect('index',array('page'=>intval($_REQUEST['page'])));
			exit();
		}else{
			$this->error('操作失败.');
			exit();
		}
	}

	public function user(){
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

		define('rows',20000);
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

}