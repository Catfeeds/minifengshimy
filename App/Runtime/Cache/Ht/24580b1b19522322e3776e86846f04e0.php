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

<div class="aaa_pts_show_1">【 提现管理 】</div>

<div class="aaa_pts_show_2">
    <div>
       <div class="aaa_pts_4"><a href="<?php echo U('Tixian/index');?>">提现管理</a></div>
    </div>
    <div class="aaa_pts_3">
      
      <table class="pro_3">
         <tr class="tr_1">
           <td style="width:80px;">ID</td>
           <td>用户</td>
           <td>提现银行</td>
           <td>银行卡号</td>
           <td>微信账号</td>
           <td>提现金额（元）</td>
           <td>提现时间</td>
           <td>状态</td>
           <td>操作</td>
         </tr>
         <tbody id="news_option">
         <!-- 遍历 -->
          <?php if(is_array($tixian)): $i = 0; $__LIST__ = $tixian;if( count($__LIST__)==0 ) : echo "暂时没有数据" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
             <td><?php echo ($v["id"]); ?></td>
             <td><?php echo ($v["uname"]); ?></td>
             <td><?php echo ($v["bankname"]); ?></td>
             <td><?php echo ($v["bankcart"]); ?></td>
             <td><?php echo ($v["zhanghao"]); ?></td>
             <td><?php echo ($v["total"]); ?></td>
             <td><?php echo ($v["addtime"]); ?></td>
             <td><?php if($v["status"] == 0): ?><span class="label err" >未处理</span><?php else: ?><span class="label blue">已处理<?php endif; ?></spam>
             </td>
             <td>
              <a href="<?php echo U('set_cl');?>?id=<?php echo ($v["id"]); ?>&page=<?php echo ($page); ?>">已/未处理</a> |

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

function del_id_urls (id) {
  if (confirm('您确定要删除吗？')) {
    location.href="<?php echo U('del');?>?id="+id;
  };
}
</script>
</body>
</html>