<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class EvaluateController extends PublicController {
	//***************************
	// 产品分类
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

    //***************************
    //   上传图片
    //***************************
    public function uploadimg(){
        $info = $this->upload_images($_FILES['data'],array('jpg','png','jpeg'),"publish/".date(Ymd));
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

    //***************************
    //   删除图片
    //***************************
    public function delimg(){
       
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

    //发布病例讨论和社区生活
    public function publish(){
        $uid = intval($_REQUEST['uid']);
        if (!$uid) {
            echo json_encode(array('status'=>0,'err'=>'网络错误！'));
            exit();
        }
        $data['type'] = intval($_REQUEST['ctype']);
        $data['content'] = $_REQUEST['content'];
        $data['uid'] = $uid;
        $data['addtime'] = time();
        $data['photo'] = $_REQUEST['photo'];
        $res = M('community')->add($data);
        if($res){
            echo json_encode(array('status'=>1,'err'=>'发布成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'发布失败！'));
            exit();
        }
    }

    //用户评论
    public function send(){
        $uid = intval($_REQUEST['uid']);
        if (!$uid) {
            echo json_encode(array('status'=>0,'err'=>'网络错误！'));
            exit();
        } 
        $data['uid'] = $uid;
        $data['cid'] = intval($_REQUEST['cid']);
        $data['content'] = $_REQUEST['content'];
        $res = M('community_content')->add($data);
        if($res){
            echo json_encode(array('status'=>1,'err'=>'提交成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'提交失败！'));
            exit();
        }
    }

    //查找
    public function search(){
        $type = intval($_REQUEST['ctype']);
        $search = $_REQUEST['type2'];
        $uid = intval($_REQUEST['uid']);
        $where = '';
        if($search == 'quanbu'){
            $where = '1=1';
        }else if($search == 'my'){
            $where = 'uid='.$uid;
        }
        $where .= ' AND type='.$type;
        $list = M('community')->where($where)->order('id desc')->select();
        if(!$list){
            echo json_encode(array('status'=>0,'err'=>'暂无数据！'));
            exit();
        }
        $photo = array();
        foreach($list as $k => $v){
            $list[$k]['time'] = date('Y-m-d',$v['addtime']);
            $list[$k]['photo_x'] = __DATAURL__.M('doctor')->where('uid='.intval($v['uid']))->getField('photo_x');
            $list[$k]['name'] = M('doctor')->where('uid='.intval($v['uid']))->getField('name');
            $list[$k]['hospital'] = M('doctor')->where('uid='.intval($v['uid']))->getField('hospital');
            $list[$k]['photo'] = explode(',', $v['photo']);
            foreach($list[$k]['photo'] as $k2 => $v2){
                if($v2){
                    $list[$k]['img'][$k2] = __DATAURL__.$v2;
                }
                
            }
            $temp = M('community_content')->where('cid='.$v['id'])->select();
            if($temp){
                $list[$k]['ping'] = array();
                $list[$k]['author'] = array();
                foreach($temp as $k3 => $v3){ 
                    array_push($list[$k]['ping'],$v3['content']);
                    $temp_author = M('doctor')->where('uid='.intval($v['uid']))->getField('name');

                    array_push($list[$k]['author'],$temp_author);

                }
            }
        }
        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
    }

}