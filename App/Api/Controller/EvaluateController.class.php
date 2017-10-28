<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class EvaluateController extends PublicController {
	//***************************
	// 评估详情介绍
	//***************************
    public function detail(){
        $type = intval($_REQUEST['etype']);
    	$info = M('evaluate')->where('type='.$type)->find();
        if(!$info){
            echo json_encode(array('status'=>0,'err'=>'网络异常！'));
            exit();
        }
        if($info['photo']){
            $info['photo'] = __DATAURL__.$info['photo'];
        }
        
        echo json_encode(array('status'=>1,'info'=>$info));
        exit();
    }

    //评估关节炎
    public function guan_send(){
        $uid = intval($_REQUEST['uid']);
        if (!$uid) {
            echo json_encode(array('status'=>0,'err'=>'网络错误！'));
            exit();
        }
        $data['uid'] = $uid;

        $data['ya_tong'] = intval($_REQUEST['ya_left_1']).','.intval($_REQUEST['ya_left_2']).','.intval($_REQUEST['ya_left_3']).','.intval($_REQUEST['ya_left_4']).','.intval($_REQUEST['ya_left_5']).','.intval($_REQUEST['ya_right_1']).','.intval($_REQUEST['ya_right_2']).','.intval($_REQUEST['ya_right_3']).','.intval($_REQUEST['ya_right_4']).','.intval($_REQUEST['ya_right_5']);

        $data['zhong'] = intval($_REQUEST['zhong_left_1']).','.intval($_REQUEST['zhong_left_2']).','.intval($_REQUEST['zhong_left_3']).','.intval($_REQUEST['zhong_left_4']).','.intval($_REQUEST['zhong_left_5']).','.intval($_REQUEST['zhong_right_1']).','.intval($_REQUEST['zhong_right_2']).','.intval($_REQUEST['zhong_right_3']).','.intval($_REQUEST['zhong_right_4']).','.intval($_REQUEST['zhong_right_5']);

        $data['xue_ya'] = floatval($_REQUEST['xue_ya']);
        $data['dan_bai'] = floatval($_REQUEST['dan_bai']);
        $data['zhengti'] = intval($_REQUEST['zhengti']);
        $data['jiangchen'] = intval($_REQUEST['jiangchen']);

        $data['wenjuan'] = intval($_REQUEST['h1']).','.intval($_REQUEST['h2']).','.intval($_REQUEST['h3']).','.intval($_REQUEST['h4']).','.intval($_REQUEST['h5']).','.intval($_REQUEST['h6']).','.intval($_REQUEST['h7']).','.intval($_REQUEST['h8']);
        $data['addtime'] = time();
        $res = M('guan_evaluate')->add($data);
        if($res){
            echo json_encode(array('status'=>1,'id'=>$res));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'数据异常！'));
            exit();
        }
    }

    //关节炎评估结果
    public function guan_detail(){
        $id = intval($_REQUEST['id']);
        $info = M('guan_evaluate')->where('id='.$id)->find();
        $ya_tong = explode(',',$info['ya_tong']);
        $ya_num = 0;
        foreach($ya_tong as $k => $v){
            if(intval($v) == 1){
                $ya_num++;
            }
        }
        $zhong = explode(',',$info['zhong']);
        $zhong_num = 0;
        foreach($zhong as $k => $v){
            if(intval($v) == 1){
                $zhong_num++;
            }
        }
        $haq = explode(',',$info['wenjuan']);
        echo json_encode(array('status'=>1,'info'=>$info,'ya_num'=>$ya_num,'zhong_num'=>$zhong_num,'haq'=>$haq));
        exit();
    }

    //评估列表
    public function lists(){
        $uid = intval($_REQUEST['uid']);
        if (!$uid) {
            echo json_encode(array('status'=>0,'err'=>'网络错误！'));
            exit();
        }
        $type = intval($_REQUEST['etype']);
        $list = array();
        if($type == 1){
            $list = M('guan_evaluate')->where('uid='.$uid)->select();
        }
        if($list){
            foreach($list as $k => $v){
                $list[$k]['addtime'] = date('Y-m-d',$v['addtime']);
            }
        }
        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
    }

   

   

   

}