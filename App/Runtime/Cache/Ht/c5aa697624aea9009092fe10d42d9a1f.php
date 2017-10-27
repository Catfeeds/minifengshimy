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
<?php  $width=round($img['width']*0.6+6); $height =round( $width*$img['height'] / $img['width']); ?>
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

<div class="aaa_pts_show_1">【 修改疾病评估 】</div>

<div class="aaa_pts_show_2">
    <div>
       <div class="aaa_pts_4"><a href="<?php echo U('index');?>">全部疾病评估</a></div>
       
    </div>
    <div class="aaa_pts_3">
		<form action="?id=<?php echo ($id); ?>" method="post" onsubmit="return ac_from();" enctype="multipart/form-data">
		<ul class="aaa_pts_5">
			<li>
				<div class="d1">评估名称:</div>
				<div>
					<input class="inp_1" name="name" id="name" style="width:300px;" value="<?php echo ($info["name"]); ?>"/>&nbsp;&nbsp;
				</div>
       
			</li>
      <li>
        <div class="d1">评估简介:</div>
        <div>
          <textarea class="inp_1 inp_2" name="intro" id="intro" style="height:80px; width:400px;"/><?php echo $info['intro']; ?></textarea>
        </div>
      </li>
  
        <li>
          <div class="d1">简介图:</div>
           <div>
            <?php if ($info['photo']) { ?>
                  <img src="/minifengshimy/Data/<?php echo $info['photo']; ?>" style="margin-bottom: 3px;" />
                  <br />
              <?php } ?>

              <input type="file" name="photo" id="photo" />

            </div>
            <div style="color:#c00; font-size:12px; padding-left:20px;">图片大小：355X100</div>
         </li>


      <li><input type="submit" name="submit" value="提交" class="aaa_pts_web_3" border="0" id="aaa_pts_web_s">
          <input type="hidden" name="id" id='id' value="<?php echo ($info["id"]); ?>">
      </li>
      </ul>
      </form>
         
    </div>
    
</div>
<script type="text/javascript" src="/minifengshimy/Public/ht/js/product.js"></script>
<script>
function upadd(obj){
  //alert('aaa');
  $('#imgs_add').append('<div>&nbsp;&nbsp;<input type="file" style="width:160px;" name="files[]" /><a onclick="$(this).parent().remove();" class="btn cls" style="background:#D0D0D0; width:40px; color:black;"">&nbsp;&nbsp;&nbsp;删除</a></div>');
  return false;
}

function ac_from(){

  var name=document.getElementById('name').value;
  if(name.length<1){
	  alert('请输入评估名字.');
	  return false;
	} 


  
}

</script>
</body>
</html>