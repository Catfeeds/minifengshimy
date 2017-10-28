<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理</title>
<link href="/minifengshimy/Public/ht/css/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/minifengshimy/Public/ht/js/jquery.js"></script>
<script type="text/javascript" src="/minifengshimy/Public/ht/js/action.js"></script>
<script type="text/javascript">
 function openDialog(type){
		window.open("<?php echo U('Inout/expUser');?>?tel=<?php echo ($tel); ?>&name=<?php echo ($name); ?>");
}
 </script>
</head>
<body>

<div class="aaa_pts_show_1">【 会员管理 】</div>

<div class="aaa_pts_show_2">
    
    <div>
       <div class="aaa_pts_4"><a href="index">会员管理</a></div>
    </div>
    <div class="aaa_pts_3">
      
      <div class="pro_4 bord_1">
         <div class="pro_5">账号名：<input type="text" class="inp_1 inp_6" id="name" value="<?php echo ($name); ?>"></div>
         <div class="pro_5">手机号码：<input type="text" class="inp_1 inp_6" id="tel" value="<?php echo ($tel); ?>"></div>
		 <!-- <div class="pro_5"><input type="button" class="excel" value="导出会员信息" style="margin:0;" onclick="openDialog();"></div> -->
         <div class="pro_6"><input type="button" class="aaa_pts_web_3" value="搜 索" style="margin:0;" onclick="product_option(0);"></div>
      </div>

      <table class="pro_3">
         <tr class="tr_1">
           <td style="width:80px;">ID</td>
           <td style="width:100px;">头像</td>
           <td>账号名</td>
           <td style="width:150px;">注册时间</td>
           <td style="width:120px;">手机号码</td>
           <td style="width:120px;">类型</td>
           <td style="width:100px;">状态</td>
           <td style="width:180px;">操作</td>
         </tr>
         <tbody id="news_option">
          <!-- 遍历 -->
            <?php if(is_array($userlist)): $i = 0; $__LIST__ = $userlist;if( count($__LIST__)==0 ) : echo "暂时没有数据" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr data-id="<?php echo ($v["id"]); ?>" data-name="<?php echo ($v["name"]); ?>">
               <td><?php echo ($v["id"]); ?></td>
               <td><img src="<?php echo ($v["photo"]); ?>" width="60px" height="60px"></td>
               <td><?php echo ($v["name"]); ?></td>
               <td><?php echo ($v["addtime"]); ?></td>
               <td><?php echo ($v["tel"]); ?></td>
               <td><?php if($v["type"] == 2): ?>企业会员<?php else: ?>普通会员<?php endif; ?></td>
               <td><?php if($v["del"] != 0): ?><label style="color:red;">已禁用</label><?php endif; ?></td>
               <td class="obj_1">
                 <!-- <a href="<?php echo U('User/add');?>?id=<?php echo ($v["id"]); ?>">修改</a> -->
                 <a onclick='del_id_urls(<?php echo ($v["id"]); ?>,<?php echo ($page); ?>)'><?php if($v["del"] != 0): ?><label style="color:green;">恢复</label><?php else: ?>禁用<?php endif; ?></a>
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
//分页
function product_option(page){
  var obj={
     "name":$("#name").val(),
     "tel":$("#tel").val(),
    }
  var url='?page='+page;
  $.each(obj,function(a,b){
    url+='&'+a+'='+b;
   });
  location=url; 
}

//更改按钮
if(type=='xz'){
	$('.obj_1').html('<input type="button" value="选 择" class="aaa_pts_web_3" style="margin:3px 0;" onclick="window_opener(this)">');
}

function window_opener(e){
  var obj=$(e);
  window.opener.document.getElementById('uid').value=obj.parent().parent().attr('data-id');
  window.opener.document.getElementById('user_name').value=obj.parent().parent().attr('data-name');
  
  window.close();
}

function del_id_urls(id,page){
   if(confirm('你确定要执行此操作吗？')){
    location.href='<?php echo U("del");?>?did='+id+'&page='+page;
  }
}
</script>
</body>
</html>