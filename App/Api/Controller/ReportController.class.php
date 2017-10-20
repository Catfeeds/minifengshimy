<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class ReportController extends PublicController {
	//***************************
	// 产品分类
	//***************************
    public function index(){
        $uid = intval($_REQUEST['uid']);
        if (!$uid) {
            echo json_encode(array('status'=>0,'err'=>'网络错误！'));
            exit();
        }
        $list = M('report')->where('uid='.$uid)->select();
        echo json_encode(array('status'=>1,'list'=>$list));
        exit();
    }

    //***************************
    //   上传图片
    //***************************
    public function uploadimg(){
        $info = $this->upload_images($_FILES['data'],array('jpg','png','jpeg'),"event/".date(Ymd));
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

    //添加不良事件
    public function add(){
        $uid = intval($_REQUEST['uid']);
        if (!$uid) {
            echo json_encode(array('status'=>0,'err'=>'网络错误！'));
            exit();
        }
        $data['uid'] = $uid;
        if($_REQUEST['photo'] != 'undefined' ){
            $data['photo'] = $_REQUEST['photo'];
        }
        
        $data['xue'] = $_REQUEST['xue'];
        $data['hong'] = $_REQUEST['hong'];
        $data['xue_hong'] = $_REQUEST['xue_hong'];
        $data['ban'] = $_REQUEST['ban'];
        $data['baidang'] = $_REQUEST['baidang'];
        $data['bili'] = $_REQUEST['bili'];
        $data['bizhi'] = $_REQUEST['bizhi'];
        $data['lin_num'] = $_REQUEST['lin_num'];
        $data['shi_num'] = $_REQUEST['shi_num'];
        $data['xue_hong_non'] = $_REQUEST['xue_hong_non'];
        $data['xue_hong_num'] = $_REQUEST['xue_hong_num'];
        $data['hong_tiji'] = $_REQUEST['hong_tiji'];
        $data['niao_dan'] = $_REQUEST['niao_dan'];
        $data['niao_hong'] = $_REQUEST['niao_hong'];
        $data['niao_bai'] = $_REQUEST['niao_bai'];
        $data['bing'] = $_REQUEST['bing'];
        $data['yin'] = $_REQUEST['yin'];
        $data['niaotang'] = $_REQUEST['niaotang'];
        $data['gubin'] = $_REQUEST['gubin'];
        $data['gucao'] = $_REQUEST['gucao'];
        $data['zongdan'] = $_REQUEST['zongdan'];
        $data['gu_an'] = $_REQUEST['gu_an'];
        $data['zongdang'] = $_REQUEST['zongdang'];
        $data['zhijie'] = $_REQUEST['zhijie'];
        $data['ganxing'] = $_REQUEST['ganxing'];
        $data['sheng_qiu'] = $_REQUEST['sheng_qiu'];
        $data['xueniao'] = $_REQUEST['xueniao'];
        $data['xueji'] = $_REQUEST['xueji'];
        $data['xuetang'] = $_REQUEST['xuetang'];
        $data['zong_chuen'] = $_REQUEST['zong_chuen'];
        $data['ganyou'] = $_REQUEST['ganyou'];
        $data['gao_bai'] = $_REQUEST['gao_bai'];
        $data['jisuan'] = $_REQUEST['jisuan'];
        $data['ck'] = $_REQUEST['ck'];
        $data['rusuan'] = $_REQUEST['rusuan'];
        $data['jiatai'] = $_REQUEST['jiatai'];
        $data['aipei'] = $_REQUEST['aipei'];
        $data['tanglei'] = $_REQUEST['tanglei'];
        $data['tangleis'] = $_REQUEST['tangleis'];
        $data['yang'] = $_REQUEST['yang'];
        $data['gei_yang'] = $_REQUEST['gei_yang'];
        $data['dingxing'] = $_REQUEST['dingxing'];
        $data['niaosuan'] = $_REQUEST['niaosuan'];
        $data['gai'] = $_REQUEST['gai'];
        $data['jia'] = $_REQUEST['jia'];
        $data['lin'] = $_REQUEST['lin'];
        $data['lv'] = $_REQUEST['lv'];
        $data['na'] = $_REQUEST['na'];
        $data['eryang'] = $_REQUEST['eryang'];
        $data['addtime'] = $_REQUEST['addtime'];
        $rid = intval($_REQUEST['rid']);
        if($rid){
            $res = M('report')->where('id='.$rid)->save($data);
            if($res){
                echo json_encode(array('status'=>1,'err'=>'保存成功！'));
                exit();
            }else{
                echo json_encode(array('status'=>0,'err'=>'保存失败！'));
                exit();
            } 
        }
        $res = M('report')->add($data);
        if($res){
            echo json_encode(array('status'=>1,'err'=>'添加成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'添加失败！'));
            exit();
        }
    }

    //不良事件详情
    public function detail(){
        $id = $_REQUEST['rid'];
        if(!$id){
            echo json_encode(array('status'=>0,'err'=>'数据异常!'));
            exit();
        }
        $info = M('report')->where('id='.intval($id))->find();
        echo json_encode(array('status'=>1,'info'=>$info));
        exit();
    }

    //更新不良事件
    public function update(){
        $uid = intval($_REQUEST['uid']);
        if (!$uid) {
            echo json_encode(array('status'=>0,'err'=>'网络错误！'));
            exit();
        }
        $eid = intval($_REQUEST['eid']);
        if (!$eid) {
            echo json_encode(array('status'=>0,'err'=>'数据异常！'));
            exit();
        }
        $data['uid'] = $uid;
        if($_REQUEST['photo']){
            $data['photo'] = $_REQUEST['photo'];
        } 
        if(intval($_REQUEST['type_id'])){
            $data['type_id'] = $_REQUEST['type_id'];
        }    
        $data['start_time'] = $_REQUEST['start_time'];
        $data['dur_time'] = $_REQUEST['dur_time'];
        $data['descript'] = $_REQUEST['descript'];
        $res = M('event')->where('id='.$eid)->save($data);
        if($res){
            echo json_encode(array('status'=>1,'err'=>'保存成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'保存失败！'));
            exit();
        }
    }

   //获取类型
   public function getType(){
        $typelist = M('event_type')->select();
        echo json_encode(array('status'=>1,'typelist'=>$typelist));
        exit();
   }

   //删除
   public function del(){
        $uid = intval($_REQUEST['uid']);
        if (!$uid) {
            echo json_encode(array('status'=>0,'err'=>'网络错误！'));
            exit();
        }
        $id = intval($_REQUEST['rid']);
        if (!$id) {
            echo json_encode(array('status'=>0,'err'=>'数据异常！'));
            exit();
        }
        $res = M('report')->where('id='.$id)->delete();
        if($res){
            echo json_encode(array('status'=>1,'err'=>'删除成功！'));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'删除失败！'));
            exit();
        }
   }

}