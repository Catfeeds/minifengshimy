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

<div class="aaa_pts_show_1">【 不良事件类型管理 】</div>

<div class="aaa_pts_show_2">
    <div>
       <div class="aaa_pts_4"><a href="<?php echo U('Event/index');?>">全部类型</a></div>
       <div class="aaa_pts_4"><a href="<?php echo U('Event/add');?>">添加类型</a></div>
    </div>
    <div class="aaa_pts_3">
      
      <div class="pro_4 bord_1">
         <div class="pro_5">名称：<input type="text" class="inp_1 inp_6" id="name" value="<?php echo ($name); ?>"></div> 
         <div class="pro_6"><input type="button" class="aaa_pts_web_3" value="搜 索" style="margin:0;" onclick="product_option(0);"></div>
      </div>
      
      <table class="pro_3">
         <tr class="tr_1">
           <td>ID</td>
           <td>类型名称</td>
           <td>操作</td>
         </tr>
         <tbody id="news_option">
         <!-- 遍历 -->
          <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "暂时没有数据" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
             <td><?php echo ($v["id"]); ?></td>
             <td><?php echo ($v["name"]); ?></td>
            <td class="obj_1">
              <a href="<?php echo U('Event/add');?>?id=<?php echo ($v["id"]); ?>">修改</a> |
              <a onclick="del_id_urls(<?php echo ($v["id"]); ?>)">删除</a>
             </td>
           </tr><?php endforeach; endif; else: echo "暂时没有数据" ;endif; ?>
         <!-- 遍历 -->
         </tbody>
         <tr>
            <td colspan="10" class="td_2">
                  <?php echo ($page); ?>  
             </td>
         </tr>
      </table>      
    </div>
    
</div>
<script>
//更改按钮
function del_id_urls (id) {
  if (confirm('您确定要删除吗？')) {
    location.href="<?php echo U('del');?>?id="+id;
  };
}

function product_option(page){

  var obj={
     "name":$("#name").val(),
    }
    console.log(obj);
  var url='?page='+page;
  $.each(obj,function(a,b){
    url+='&'+a+'='+b;
   });
  location=url;
}
</script>
</body>
</html>