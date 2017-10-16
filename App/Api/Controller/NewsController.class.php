<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class NewsController extends PublicController {
    //*****************************
    //  新闻列表
    //*****************************
    public function index(){
        //查询条件
        //根据店铺分类id查询
        $condition = array();
        //根据店铺名称查询
        $keyword = trim($_REQUEST['keyword']);
        if ($keyword && $keyword!='undefined') {
            $condition['name']=array('LIKE','%'.$keyword.'%');
        }

        //获取页面显示条数
        $page = intval($_REQUEST['page']);
        if (!$page) {
            $page = 1;
        }
        $limit = intval($page*8)-8;

        $list = M('news')->where($condition)->field('id,name,digest,source,editer,addtime')->order('sort desc,addtime desc')->limit($limit.',8')->select();
        foreach ($list as $k => $v) {
            $list[$k]['addtime'] = date("m-d" , $v['addtime']);
        }

        echo json_encode(array('list'=>$list));
        exit();
    }

    //*****************************
    //  新闻详情
    //*****************************
    public function detail(){
        $newid=intval($_REQUEST['news_id']);
        $detail=M('news')->where('id='.intval($newid))->find();
        if (!$detail) {
            echo json_encode(array('status'=>0,'err'=>'没有找到相关信息.'));
            exit();
        }

        $detail['addtime'] = date("m-d",$detail['addtime']);
        $content = str_replace('/minifengshimianyi/Data/', __DATAURL__, $detail['content']);
        $detail['content']=html_entity_decode($content, ENT_QUOTES, "utf-8");
        //人气加一
        $up = array();
        $up['renqi'] = intval($detail['renqi'])+1;
        M('news')->where('id='.intval($newid))->save($up);
        //json加密输出
        echo json_encode(array('status'=>1,'info'=>$detail));
        exit();
    }
    
}