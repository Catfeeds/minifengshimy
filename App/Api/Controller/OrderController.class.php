<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class OrderController extends PublicController {
	//***************************
	//  用户获取订单信息接口
	//***************************
	public function index(){
		$uid = intval($_REQUEST['uid']);
		if (!$uid) {
			echo json_encode(array('status'=>0,'err'=>'登录状态异常'));
			exit();
		}

		//分页
		$pages=intval($_REQUEST['page']);
		if (!$pages) {
			$pages=1;
		}
		$limit = intval($pages*7)-7;

		$orders=M("order");
		$orderp=M("order_product");
		//按条件查询
		$condition = array();
		$condition['del'] = 0;
		$condition['order_type'] = 1;
		$condition['back'] = '0';
		$condition['uid'] = intval($uid);
		$condition['status'] = 10;
		$order_type = trim($_REQUEST['order_type']);
		if ($order_type) {
			switch ($order_type) {
				case 'pay':
					$condition['status'] = 10;
					break;
				case 'huida':
					$condition['status'] = 20;
					break;
				case 'ping':
					$condition['status'] = 30;
					break;
				case 'finish':
					$condition['status'] = 40;
					break;	
				default:
					$condition['status'] = 10;
					break;
			}
		}

		$order_status = array('0'=>'已取消','10'=>'待付款','20'=>'待回答','30'=>'待评价','40'=>'已完成');

        $order = $orders->where($condition)->order('id desc')->field('id,order_sn,status,price,docid,sid,addtime')->limit($limit.',7')->select();
		foreach ($order as $n=>$v){
			$order[$n]['dname'] = M('doctor')->where('id='.intval($v['docid']))->getField('name');
			$order[$n]['photo_x'] = __DATAURL__.(M('doctor')->where('id='.intval($v['docid']))->getField('photo_x'));
			$order[$n]['addtime'] = date("Y-m-d H:i",$v['addtime']);
			$supply = M('supply')->where('id='.intval($v['sid']))->find();
			$order[$n]['casename'] = $supply['casename'];
		}

        echo json_encode(array('status'=>1,'ord'=>$order));
        exit();
	}

	//***************************
	//  用户订单详情接口
	//***************************
	public function order_details () {

		$order_id = intval($_REQUEST['order_id']);
		//订单详情
		$orders = M("order");
		$order_info = $orders->where('id='.intval($order_id).' AND del=0')->field('id,order_sn,docid,sid,uid,amount,addtime')->find();
		if (!$order_info) {
			echo json_encode(array('status'=>0,'err'=>'咨询订单信息错误.'));
			exit();
		}

		$info = array();
		//医生信息
		$doc_info = M('doctor')->where('id='.intval($order_info['docid']))->find();
		$info['dname'] = $doc_info['name'];
		//咨询信息
		$supply = M('supply')->where('id='.intval($order_info['sid']))->find();
		$info['uname'] = trim($supply['uname']);
		$info['casename'] = trim($supply['casename']);
		$info['content'] = $supply['content'];
		$order_info['addtime'] = date("Y-m-d H:i", $order_info['addtime']);
         
        echo json_encode(array('status'=>1,'info'=>$info,'order_info'=>$order_info));
        exit();
	}

	//***************************
	//  用户退款退货接口
	//***************************
	public function order_refund(){
		$uid = intval($_REQUEST['uid']);
		if (!$uid) {
			echo json_encode(array('status'=>0,'err'=>'登录状态异常'));
			exit();
		}

		//分页
		$pages=intval($_REQUEST['page']);
		if (!$pages) {
			$pages=0;
		}

		$orders=M("order");
		$orderp=M("order_product");
		$shangchang = M('shangchang');

		$condition = array();
		$condition['back']=array('gt','0');
		//获取总页数
        $count = $orders->where($condition)->count();
        $the_page = ceil($count/6);

		$refund_status = array('1'=>'退款申请中','2'=>'已退款','3'=>'处理中','4'=>'已拒绝');

        $order = $orders->where($condition)->order('back_addtime desc')->field('id,price,order_sn,product_num,back,back_addtime')->limit($pages.',6')->select();
		foreach ($order as $n=>$v) {
			$order[$n]['desc'] = $refund_status[$v['back']];
			$prolist = $orderp->where('order_id='.intval($v['id']))->find();
			$order[$n]['photo_x'] = __DATAURL__.$prolist['photo_x'];
			$order[$n]['pid'] = $prolist['pid'];
			$order[$n]['name'] = $prolist['name'];
			$order[$n]['price_yh'] = $prolist['price'];
			$order[$n]['back_addtime'] = date("Y-m-d H:i",$v['back_addtime']);
			$order[$n]['pro_count'] = $orderp->where('order_id='.intval($v['id']))->getField('COUNT(id)');
		}

        echo json_encode(array('status'=>1,'ord'=>$order));
        exit();
	}


	//***************************
	//  用户订单编辑接口
	//***************************
	public function orders_edit(){
		
	    $orders=M("order");
	    $order_id=intval($_REQUEST['id']);
	    $type=$_REQUEST['type'];

	    $check_id = $orders->where('id='.intval($order_id).' AND del=0')->getField('id');
	    if (!$check_id || !$type) {
	    	echo json_encode(array('status'=>0,'err'=>'订单信息错误.'.__LINE__));
	    	exit();
	    }

	    $data = array();
	    if ($type==='cancel') {
	    	$data['status'] = 0;
	    }elseif ($type==='receive') {
	    	$data['status'] = 30;
	    }elseif ($type==='refund') {
	    	$data['back'] = 1;
	    	$data['back_remark'] = $_REQUEST['back_remark'];
	    }

	    if ($data) {
	    	$result = $orders->where('id='.intval($order_id))->save($data);
	    	if($result !== false){
				echo json_encode(array('status'=>1));
	    		exit();
			}else{
				echo json_encode(array('status'=>0,'err'=>'操作失败.'.__LINE__));
	    	exit();
			}
	    }else{
	    	echo json_encode(array('status'=>0,'err'=>'订单信息错误.'.__LINE__));
	    	exit();
	    }
	}

	//***************************
	//  用户订单详情接口
	//***************************
	public function details(){

		$order_id = intval($_REQUEST['order_id']);
		//订单详情
		$orders = M("order");
		$order_info = $orders->where('id='.intval($order_id).' AND del=0')->find();
		if (!$order_info) {
			echo json_encode(array('status'=>0,'err'=>'咨询订单信息错误.'));
			exit();
		}

		$info = array();
		//医生信息
		$doc_info = M('doctor')->where('id='.intval($order_info['docid']))->find();
		$info['order_id'] = intval($order_info['id']);
		$info['docid'] = intval($doc_info['id']);
		$info['dname'] = $doc_info['name'];
		$info['photo_x'] = __DATAURL__.$doc_info['photo_x'];

		//咨询信息
		$supply = M('supply')->where('id='.intval($order_info['sid']))->find();
		$info['casename'] = trim($supply['casename']);
		$info['addtime'] = date("Y-m-d H:i", $order_info['addtime']);
         
        echo json_encode(array('status'=>1,'info'=>$info));
        exit();
	}


    //***************************
	//  用户医生评论接口
	//***************************
    public function addMessage(){
        //获取订单ID，医生ID
        $order_id = intval($_POST['order_id']);
        $docid = intval($_POST['docid']);
        $uid = intval($_REQUEST['uid']);
        if (!$uid || !$docid || !$order_id) {
        	echo json_encode(array('status'=>0,'err'=>'参数错误.'));
        	exit();
        }

        $content = $_POST['content'];
        $nums = intval($_POST['nums']);
        if (!$nums) {
        	echo json_encode(array('status'=>0,'err'=>'您还没有打分哦！'));
        	exit();
        }

        $data = array();
        $data['uid'] = $uid;
        $data['order_id'] = $order_id;
        $data['docid'] = $docid;
        $data['num'] = $nums;
        $data['content'] = $content;
        $data['addtime'] = time();
        $result = M('doctor_dp')->add($data);
        if($result){
            M('order')->where('id='.intval($order_id))->save(array('status'=>40));
            echo json_encode(array('status'=>1));
            exit();
        }else{
        	echo json_encode(array('status'=>0,'err'=>'操作失败.'));
        	exit();
        }

    }

}