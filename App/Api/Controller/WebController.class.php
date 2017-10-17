<?php
namespace Api\Controller;
use Think\Controller;
class WebController extends PublicController {
	//***************************
	//  所有单页数据接口
	//***************************
    public function web(){
    	$web_id = intval($_REQUEST['web_id']);
    	$content = M('web')->where('id='.intval($web_id))->getField('concent');
        $content = str_replace('/minifengshimy/Data/', __DATAURL__, $content);
    	$content = html_entity_decode($content, ENT_QUOTES, "utf-8");
		echo urldecode(json_encode(array('status'=>1,'content'=>$content)));
        exit();
    }

    //***************************
    //  用户 反馈
    //***************************
    public function feedback() {
        $uid = intval($_REQUEST['uid']);
        if (!$uid) {
            echo json_encode(array('status'=>0,'err'=>'登录状态异常！'));
            exit();
        }

        $content = trim($_REQUEST['content']);
        if (!$content) {
            echo json_encode(array('status'=>0,'err'=>'请输入反馈内容！'));
            exit();
        }

        $data = array();
        $data['uid'] = $uid;
        $data['content'] = $content;
        $data['phone'] = $_REQUEST['phone'];
        $data['addtime'] = time();
        $res = M('fankui')->add($data);
        if ($res) {
            echo json_encode(array('status'=>1,'err'=>'提交成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'提交失败！'));
            exit();
        }
    }

    //***************************
    //  获取 平台联系电话
    //***************************
    public function getconfig(){
        $config = M('program')->where('id=1')->find();
        echo json_encode(array('status'=>1,'config'=>$config));
    }

    //关于我们
    public function aboutUs(){
        $content = M('web')->where('id=1')->getField('concent');
        $content = str_replace(C('content.dir'), __DATAURL__ , $content);
        $content = html_entity_decode($content, ENT_QUOTES ,'utf-8');
        echo json_encode(array('status'=>1,'content'=>$content));
        exit();
    }
}