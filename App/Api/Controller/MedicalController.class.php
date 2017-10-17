<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class MedicalController extends PublicController {
	//***************************
	// 产品分类
	//***************************
    public function index(){
        $cid = 1;
    	$list = M('medical')->where('cid='.$cid)->order('id desc')->select();
        if(!$list){
            echo json_encode(array('status'=>0,'err'=>'暂无数据！'));
            exit();
        }
        foreach($list as $k => $v){
            $list[$k]['addtime'] = date("Y-m-d",$v['addtime']);
            $list[$k]['photo'] = __DATAURL__.$v['photo'];
        }
        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
    }

    //查找
    public function search(){
        $cid = intval($_REQUEST['cid']) + 1;
        $list = M('medical')->where('cid='.$cid)->order('id desc')->select();
        if(!$list){
            echo json_encode(array('status'=>0,'err'=>'暂无数据！'));
            exit();
        }
        foreach($list as $k => $v){
            $list[$k]['addtime'] = date("Y-m-d",$v['addtime']);
            $list[$k]['photo'] = __DATAURL__.$v['photo'];
        }
        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
    }

    //详情
    public function detail(){
        $id = intval($_REQUEST['id']);
        $medical = M('medical')->where('id='.$id)->find();
        if($medical){
            $medical['addtime'] = date("Y-m-d", $medical['addtime']);
            if($medical['video']){
                $medical['video'] = __DATAURL__.$medical['video'];
            }
            
            $content = str_replace(C('content.dir'), __DATAURL__ , $medical['content']);
            $medical['content']= html_entity_decode($content, ENT_QUOTES ,'utf-8');
            echo json_encode(array('status'=>1,'medical'=>$medical));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'数据异常！'));
            exit();
        }
    }

}