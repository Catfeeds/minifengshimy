<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理</title>
<link href="/minifengshimy/Public/ht/css/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/minifengshimy/Public/ht/js/jquery1.8.js"></script>
<script type="text/javascript" src="/minifengshimy/Public/ht/js/action.js"></script>
<script type="text/javascript" src="/minifengshimy/Public/ht/js/jCalendar.js"></script>
<script type="text/javascript" src="/minifengshimy/Public/ht/js/jquery.XYTipsWindow.min.2.8.js"></script>
<script type="text/javascript" src="/minifengshimy/Public/ht/js/mydate.js"></script>
<link href="/minifengshimy/Public/ht/css/order.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div class="aaa_pts_show_1">【 疾病评估管理 】</div>


<div class="aaa_pts_show_2">
    <div>
       <div class="aaa_pts_4"><a href="<?php echo U('index');?>">疾病评估管理</a></div>
    </div>
    
    <div class="aaa_pts_3">
      <table class="pro_3">
         <tr class="tr_1">  
           <td style="width:90px;">ID</td>
           <td>评估名称</td>
           <td>简介图</td>       
           <td style="width:180px;">操作</td>
         </tr>
         <?php if(is_array($evaluate)): $i = 0; $__LIST__ = $evaluate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$e): $mod = ($i % 2 );++$i;?><tr data-id="<?php echo ($e["id"]); ?>" data-name="<?php echo ($e["name"]); ?>">
		      <td><?php echo ($e["id"]); ?></td>
          <td><?php echo ($e["name"]); ?></td>
          <td><img src="/minifengshimy/Data/<?php echo ($e["photo_x"]); ?>" /></td>
		   <td>
		      <a href="<?php echo U('add');?>?id=<?php echo ($e["id"]); ?>">修改</a>
		   </td>
	     </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        <!--  <tr>
            <td colspan="10" class="td_2">
                <?php echo ($page); ?> 
             </td>
         </tr> -->
      </table>
    </div>
    
</div>

</body>
</html>