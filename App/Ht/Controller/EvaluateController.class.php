<?php
namespace Ht\Controller;
use Think\Controller;
class EvaluateController extends PublicController{
	public function index(){
		$evaluate = M('evaluate')->select();
		$this->assign('evaluate',$evaluate);
		$this->display();
	}
	
	public function add(){	

		$id=(int)$_GET['id'];

		if($_POST['submit']==true){
		try{	
			//如果不是管理员则查询商家会员的店铺ID
			$id = intval($_POST['id']);
			$array=array(
				'name'=>$_POST['name'] ,
				'intro'=>$_POST['intro'] ,
			);
		
		  
			//上传产品小图
			if (!empty($_FILES["photo"]["tmp_name"])) {
					//文件上传
					$info = $this->upload_images($_FILES["photo"],array('jpg','png','jpeg'),"evaluate/".date(Ymd));
				    if(!is_array($info)) {// 上传错误提示错误信息
				        $this->error($info);
				        exit();
				    }else{// 上传成功 获取上传文件信息
					    $array['photo'] = 'UploadFiles/'.$info['savepath'].$info['savename'];
					    $xt = M('evaluate')->where('id='.intval($id))->field('photo')->find();
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

				$sql = M('evaluate')->where('id='.intval($id))->save($array);
			}else{
				
				$sql = M('evaluate')->add($array);
				$id=$sql;
			}

			//规格操作
			if($sql){//name="guige_name[]
				$this->success('操作成功!','index');
				exit();
			}else{
				throw new \Exception('操作失败.');
			}
			  
			}catch(\Exception $e){
				echo "<script>alert('".$e->getMessage()."');location='{:U('index')}?shop_id=".$shop_id."';</script>";
			}
		}

		
		//=============
		// 将变量输出
		//=============	
		$info= $id>0 ? M('evaluate')->where('id='.$id)->find() : array();
		$this->assign('info',$info);
		$this->display();

	}

}