<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理</title>
<link href="/minifengshimy/Public/ht/css/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/minifengshimy/Public/ht/js/jquery.js"></script>
<script type="text/javascript" src="/minifengshimy/Public/ht/js/action.js"></script>
</head>
<body>

<div class="aaa_pts_show_1">【 医生管理 】</div>

<div class="aaa_pts_show_2">
    <div>
       <div class="aaa_pts_4"><a href="<?php echo U('Product/index');?>">全部医生</a></div>
       <div class="aaa_pts_4"><a href="<?php echo U('Product/add');?>">添加医生</a></div>
    </div>
    <div class="aaa_pts_3">
      
      <div class="pro_4 bord_1">
        <div class="pro_5">医生姓名：<input type="text" class="inp_1 inp_6" id="name" value="<?php echo ($name); ?>"></div> 
        <div class="pro_6"><input type="button" class="aaa_pts_web_3" value="搜 索" style="margin:0;" onclick="product_option(0);"></div>
      </div>
      
      <table class="pro_3">
         <tr class="tr_1">
           <td style="width:80px;">ID</td>
           <td style="width:90px;">头像</td>
           <td style="width:90px;">姓名</td>
           <td>任职医院</td>
           <td style="width:120px;">职称</td>
           <td style="width:110px;">属性</td>
           <td style="width:100px;">推荐</td>
           <td style="width:100px;">排序</td>
           <td style="width:250px;">操作</td>
         </tr>
         <tbody id="news_option">
         <!-- 遍历 -->
          <?php if(is_array($productlist)): $i = 0; $__LIST__ = $productlist;if( count($__LIST__)==0 ) : echo "暂时没有数据" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
             <td><?php echo ($v["id"]); ?></td>
             <td style="padding:3px 0;"><img src="/minifengshimy/Data/<?php echo ($v["photo_x"]); ?>" width="70px" height="70px"/></td>
             <td><?php echo ($v["name"]); ?></td>
             <td><?php echo ($v["hospital"]); ?></td>
             <td><?php echo ($v["position"]); ?></td>
             <td><p id="new_<?php echo ($v["id"]); ?>"><?php if($v["is_yu"] == 1): ?><a class="label blue" >在线预约<?php else: ?><a class="label err">在线预约<?php endif; ?></a></p>
              <p id="hot_<?php echo ($v["id"]); ?>" style="margin-top:5px;"><?php if($v["is_online"] == 1): ?><a class="label succ" >在线出诊<?php else: ?><a class="label err">在线出诊<?php endif; ?></a></p>
             </td>
             <td><?php if($v["type"] == 1): ?><label style="color:green;">推荐</label><?php endif; ?></td>
             <td><?php echo ($v["sort"]); ?></td>
            <td>
              <a href="<?php echo U('set_tj');?>?pro_id=<?php echo ($v["id"]); ?>&page=<?php echo ($page); ?>&name=<?php echo ($name); ?>&shop_id=<?php echo ($shop_id); ?>&tuijian=<?php echo ($tuijian); ?>">推荐</a> |
              <a href="<?php echo U('add');?>?id=<?php echo ($v["id"]); ?>&page=<?php echo ($page); ?>&name=<?php echo ($name); ?>&shop_id=<?php echo ($shop_id); ?>&tuijian=<?php echo ($tuijian); ?>">修改</a> |
              <a onclick="del_id_urls(<?php echo ($v["id"]); ?>)">删除</a>
             </td>
           </tr><?php endforeach; endif; else: echo "暂时没有数据" ;endif; ?>
         <!-- 遍历 -->
         </tbody>
         <tr>
            <td colspan="10" class="td_2">
                  <?php echo ($page_index); ?>  
             </td>
         </tr>
      </table>      
    </div>
    
</div>
<script>
function product_option(page){
	
	var pid = $('#pid').val();
	if(pid == ''){
		pid = $('#ppid').val();
	}
  var obj={
	   "name":$("#name").val(),
	   //"tuijian":$("#tuijian").val()
	  }
	  //alert(obj);exit();
  var url='?page='+page;
  $.each(obj,function(a,b){
	  url+='&'+a+'='+b;
	 });
  location=url;
}

function del_id_urls (pro_id) {
  if (confirm('您确定要删除吗？')) {
    location.href="<?php echo U('del');?>?did="+pro_id;
  };
}
</script>
</body>
</html>