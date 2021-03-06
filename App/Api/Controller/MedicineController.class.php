<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class MedicineController extends PublicController {
	//***************************
	// 产品分类
	//***************************
    public function index(){
        $uid = intval($_REQUEST['uid']);
        if (!$uid) {
            echo json_encode(array('status'=>0,'err'=>'网络错误！'));
            exit();
        }
        $list = M('medicine')->where('uid='.$uid)->select();
        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
    }

    //***************************
    //   上传图片
    //***************************
    public function uploadimg(){
        $info = $this->upload_images($_FILES['data'],array('jpg','png','jpeg'),"medicine/".date(Ymd));
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

    //添加用药管理
    public function add(){
        $uid = intval($_REQUEST['uid']);
        if (!$uid) {
            echo json_encode(array('status'=>0,'err'=>'网络错误！'));
            exit();
        }
        $data['uid'] = $uid;
        $data['photo'] = $_REQUEST['photo'];
        $data['name'] = $_REQUEST['name'];
        $data['spec'] = $_REQUEST['spec'];
        $data['pihao'] = $_REQUEST['pihao'];
        $data['start_time'] = $_REQUEST['start_time'];
        $data['end_time'] = $_REQUEST['end_time'];
        $data['pinlv'] = $_REQUEST['pinlv'];
        $data['jiliang'] = $_REQUEST['jiliang'];
        $data['fangshi'] = $_REQUEST['fangshi'];
        $res = M('medicine')->add($data);
        if($res){
            echo json_encode(array('status'=>1,'err'=>'添加成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'添加失败！'));
            exit();
        }
    }

    //用药管理详情
    public function detail(){
        $id = $_REQUEST['mid'];
        if(!$id){
            echo json_encode(array('status'=>0,'err'=>'数据异常!'));
            exit();
        }
        $info = M('medicine')->where('id='.intval($id))->find();
        $imgSrc = array();
        if($info['photo']){
            $img = explode(',', trim($info['photo'],','));
            foreach($img as $k => $v) {
                array_push($imgSrc,__DATAURL__.$v);
            }
        }
        echo json_encode(array('status'=>1,'info'=>$info,'imgSrc'=>$imgSrc));
        exit();
    }

    //更新用药管理
    public function update(){
        $uid = intval($_REQUEST['uid']);
        if (!$uid) {
            echo json_encode(array('status'=>0,'err'=>'网络错误！'));
            exit();
        }
        $mid = intval($_REQUEST['mid']);
        if (!$mid) {
            echo json_encode(array('status'=>0,'err'=>'数据异常！'));
            exit();
        }
        $data['uid'] = $uid;
        if($_REQUEST['photo']){
            $data['photo'] = $_REQUEST['photo'];
        } 
        $data['name'] = $_REQUEST['name'];
        $data['spec'] = $_REQUEST['spec'];
        $data['pihao'] = $_REQUEST['pihao'];
        $data['start_time'] = $_REQUEST['start_time'];
        $data['end_time'] = $_REQUEST['end_time'];
        $data['pinlv'] = $_REQUEST['pinlv'];
        $data['jiliang'] = $_REQUEST['jiliang'];
        $data['fangshi'] = $_REQUEST['fangshi'];
        $res = M('medicine')->where('id='.$mid)->save($data);
        if($res){
            echo json_encode(array('status'=>1,'err'=>'保存成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'保存失败！'));
            exit();
        }
    }

   

}