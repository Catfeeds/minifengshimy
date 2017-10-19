<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理</title>
<link href="/minifengshimy/Public/ht/css/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/minifengshimy/Public/ht/js/jquery.js"></script>
<script type="text/javascript" src="/minifengshimy/Public/ht/js/action.js"></script>
<script type="text/javascript" src="/minifengshimy/Public/plugins/xheditor/xheditor-1.2.1.min.js"></script>
<script type="text/javascript" src="/minifengshimy/Public/plugins/xheditor/xheditor_lang/zh-cn.js"></script>
<script type="text/javascript" src="/minifengshimy/Public/ht/js/jCalendar.js"></script>
<style>
.dx1{float:left; margin-left: 17px; margin-bottom:10px; }
.dx2{color:#090; font-size:16px;  border-bottom:1px solid #CCC; width:100% !important; padding-bottom:8px;}
.dx3{width:120px; margin:5px auto; border-radius: 2px; border: 1px solid #b9c9d6; display:block;}
.dx4{border-bottom:1px solid #eee; padding-top:5px; width:100%;}
.img-err {
    position: relative;
    top: 2px;
    left: 82%;
    color: white;
    font-size: 20px;
    border-radius: 16px;
    background: #c00;
    height: 21px;
    width: 21px;
    text-align: center;
    line-height: 20px;
    cursor:pointer;
}
.btn{
            height: 25px;
            width: 60px;
            line-height: 24px;
            padding: 0 8px;
            background: #24a49f;
            border: 1px #26bbdb solid;
            border-radius: 3px;
            color: #fff;
            display: inline-block;
            text-decoration: none;
            font-size: 13px;
            outline: none;
            -webkit-box-shadow: #666 0px 0px 6px;
            -moz-box-shadow: #666 0px 0px 6px;
        }
        .btn:hover{
          border: 1px #0080FF solid;
          background:#D2E9FF;
          color: red;
          -webkit-box-shadow: rgba(81, 203, 238, 1) 0px 0px 6px;
          -moz-box-shadow: rgba(81, 203, 238, 1) 0px 0px 6px;
        }
        .cls{
            background: #24a49f;
        }
</style>

</head>
<body>

<div class="aaa_pts_show_1">【 不良事件类型管理 】</div>

<div class="aaa_pts_show_2">
   <div>
       <div class="aaa_pts_4"><a href="<?php echo U('Event/index');?>">全部类型</a></div>
       <div class="aaa_pts_4"><a href="<?php echo U('Event/add');?>">添加类型</a></div>
    </div>
    <div class="aaa_pts_3">
		<form action="<?php echo U('Event/add');?>?id=<?php echo ($id); ?>" method="post" onsubmit="return ac_from();" enctype="multipart/form-data">
		<ul class="aaa_pts_5">
			<li>
				<div class="d1">类型名称:</div>
				<div>
					<input class="inp_1" name="name" id="name" value="<?php echo ($v["name"]); ?>"/>
				</div>
			</li>
      <li>
		  <input type="hidden" name="id" value="<?php echo ($v["id"]); ?>">
          <input type="submit" name="submit" value="提交" class="aaa_pts_web_3" border="0" id="aaa_pts_web_s">  
      </li>
      </ul>
      </form>
         
    </div>
    
</div>
<script type="text/javascript" src="/minifengshimy/Public/ht/js/product.js"></script>
<script>

function ac_from(){

  var name=document.getElementById('name').value;
  if(name.length<1){
	  alert('名称不能为空');
	  return false;
	} 
  
  
}
function show(type){
  if(type=="link"){
    document.getElementById('link').style.display="block";
    document.getElementById('upload').style.display="none";
    document.getElementById('video').disabled=true;
    document.getElementById('url').disabled=false;
  }else{
    document.getElementById('link').style.display="none";
    document.getElementById('url').disabled=true;
    document.getElementById('video').disabled=false;
    document.getElementById('upload').style.display="block";
  }
}
//初始化编辑器
$('#content').xheditor({
  skin:'nostyle' ,
  upImgUrl:'<?php echo U("Upload/xheditor");?>'
});
</script>
</body>
</html>