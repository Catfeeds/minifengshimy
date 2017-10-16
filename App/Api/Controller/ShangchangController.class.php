<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class ShangchangController extends PublicController {

	//***************************
	//  获取所有商场的数据
	//***************************
    public function index(){
    	//查询条件
    	//根据店铺分类id查询
    	$condition = array();
    	$condition['status']=1;
    	$cid = intval($_REQUEST['cid']);
    	if ($cid) {
    		$condition['cid']=intval($cid);
    	}

    	//根据店铺名称查询
    	$keyword = trim($_REQUEST['keyword']);
    	if ($keyword) {
    		$condition['name']=array('LIKE','%'.$keyword.'%');
    	}

    	//获取页面显示条数
    	$page = intval($_REQUEST['page']);
    	if (!$page) {
    		$page = 1;
    	}
        $limit = intval($page*8)-8;

        //获取所有企业分类
        $catlist = M('pro_cat')->where('1=1')->order('addtime desc')->field('id,name')->select();

    	//获取所有的商家数据
    	$store_list = M('product')->where($condition)->order('sort asc,addtime desc')->field('id,name,photo_x,shop_name')->limit(8)->select();
    	foreach ($store_list as $k => $v) {
    		$store_list[$k]['photo_x'] = __DATAURL__.$v['photo_x'];
    	}

    	echo json_encode(array('status'=>1,'store_list'=>$store_list,'catlist'=>$catlist));
    	exit();
    }

    //***************************
    //  获取 所有医院的数据
    //***************************
    public function getlists() {
        //查询条件
        //根据店铺分类id查询
        $condition = array();
        $condition['status']=1;
        $cid = intval($_REQUEST['cid']);
        if ($cid) {
            $condition['cid']=intval($cid);
        }

        //根据店铺名称查询
        $keyword = trim($_REQUEST['keyword']);
        if ($keyword) {
            $condition['name']=array('LIKE','%'.$keyword.'%');
        }

        //获取页面显示条数
        $page = intval($_REQUEST['page']);
        if (!$page) {
            $page = 1;
        }
        $limit = intval($page*8)-8;

        $field = 'id,name,cid,city,main_hy,logo';
        $hlist = M('hospital')->where($condition)->order('sort asc,addtime desc')->field($field)->limit($limit.',8')->select();
        foreach ($hlist as $k => $v) {
            $hlist[$k]['logo'] = __DATAURL__.$v['logo'];
            $hlist[$k]['cname'] = M('sccat')->where('id='.intval($v['cid']))->getField('name');
            $hlist[$k]['cityname'] = M('china_city')->where('id='.intval($v['city']))->getField('name');
        }

        echo json_encode(array('status'=>1,'hlist'=>$hlist));
        exit();
    }

    //***************************
    //  商家列表获取更多
    //***************************
    public function get_more(){
        //查询条件
        //根据店铺分类id查询
        $condition = array();
        $condition['status']=1;

        //根据店铺名称查询
        $keyword = trim($_REQUEST['keyword']);
        if ($keyword) {
            $condition['name']=array('LIKE','%'.$keyword.'%');
        }

        $cid = intval($_REQUEST['cid']);
        if ($cid) {
            $condition['cid']=intval($cid);
        }

        //获取页面显示条数
        $page = intval($_REQUEST['page']);
        if (!$page) {
            $page = 1;
        }
        $limit = intval($page*8)-8;

        //获取所有的商家数据
        $store_list = M('product')->where($condition)->order('sort asc,addtime desc')->field('id,name,photo_x,shop_name')->limit(8)->select();
        foreach ($store_list as $k => $v) {
            $store_list[$k]['photo_x'] = __DATAURL__.$v['photo_x'];
        }

        echo json_encode(array('status'=>1,'store_list'=>$store_list));
        exit();
    }

    //***************************
	//  获取医院详情信息接口
	//***************************
    public function details(){

    	$hosid = intval($_REQUEST['hosid']);
        $field = 'id,name,cid,city,main_hy,content,vip_char,tel';
    	$info = M('hospital')->where('id='.intval($hosid).' AND status=1')->field($field)->find();
    	if (!$info) {
    		echo json_encode(array('status'=>0,'err'=>'数据信息异常.'));
    		exit();
    	}

        //处理图片信息
        $imgarr = explode(',', trim($info['vip_char'],','));
        $img = array();
        foreach ($imgarr as $k => $v) {
            $img[] = __DATAURL__.$v;
        }

    	$content = str_replace('/minifengshimianyi/Data/', __DATAURL__, $info['content']);
        $info['content']=html_entity_decode($content, ENT_QUOTES , 'utf-8');

    	echo json_encode(array('status'=>1,'info'=>$info,'img'=>$img));
    	exit();
    }


	//***************************
	//  会员店铺收藏接口
	//***************************
	public function shop_collect(){
		$uid = intval($_REQUEST['uid']);
		$shop_id = intval($_REQUEST['shop_id']);
		if (!$uid || !$shop_id) {
			echo json_encode(array('status'=>0,'err'=>'系统错误，请稍后再试.'));
			exit();
		}

		$check = M('shangchang_sc')->where('uid='.intval($uid).' AND shop_id='.intval($shop_id))->getField('id');
		if ($check) {
			echo json_encode(array('status'=>1,'succ'=>'您已收藏该店铺.'));
			exit();
		}
		$data = array();
		$data['uid'] = intval($uid);
		$data['shop_id'] = intval($shop_id);
		$res = M('shangchang_sc')->add($data);
		if ($res) {
			echo json_encode(array('status'=>1,'succ'=>'收藏成功！'));
			exit();
		}else{
			echo json_encode(array('status'=>0,'err'=>'网络错误..'));
			exit();
		}
	}

}