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


}