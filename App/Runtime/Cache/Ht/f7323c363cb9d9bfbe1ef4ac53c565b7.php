<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理</title>
<link href="/minifengshimy/Public/ht/css/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/minifengshimy/Public/ht/js/jquery.js"></script>
<script type="text/javascript" src="/minifengshimy/Public/ht/js/action.js"></script>
</head>
<body>
<div class="aaa_pts_show_1">【 反馈管理 】</div>
<div class="aaa_pts_show_2">
    
    <div class="aaa_pts_3">     
      <div class="pro_4 bord_1">
         <div class="pro_5">反馈内容：<input type="text" class="inp_1 inp_6" id="message" value="<?php echo ($message); ?>"></div>
         <div class="pro_6"><input type="button" class="aaa_pts_web_3" value="搜 索" style="margin:0;" onclick="product_option(0);"></div>
      </div>
      
      <table class="pro_3">
         <tr class="tr_1">
            <td>会员昵称</td>
            <td>反馈内容</td>
            <td>联系电话</td>
            <td style="width:180px;">操作</td>
         </tr>
         <tbody id="news_option">
           <!-- 遍历 -->
           <?php if(is_array($fankui)): $i = 0; $__LIST__ = $fankui;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
              <td><?php echo ($v["uname"]); ?></td>
               <td><?php echo ($v["content"]); ?></td>
               <td><?php echo ($v["phone"]); ?></td>
               <td class="obj_1">
                    <!-- 此方法在action.js -->
                   <a onclick="del_id_url2(<?php echo ($v["id"]); ?>)">删除</a>
               </td>
             </tr><?php endforeach; endif; else: echo "" ;endif; ?>
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
//搜索的方法
function product_option(page){
  window.location.href='?page='+page+'&message='+$("#message").val()
}

function del_id_url2(id) {
  if (confirm('您确定要删除吗？')) {
    location.href="<?php echo U('delfk');?>?did="+id;
  };
}
</script>
</body>
</html>