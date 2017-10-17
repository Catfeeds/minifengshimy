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

<div class="aaa_pts_show_1">【 医学百科管理 】</div>

<div class="aaa_pts_show_2">
    <div>
       <div class="aaa_pts_4"><a href="<?php echo U('Medical/index');?>">全部内容</a></div>
       <div class="aaa_pts_4"><a href="<?php echo U('Medical/add');?>">添加内容</a></div>
    </div>
    <div class="aaa_pts_3">
		<form action="<?php echo U('Medical/add');?>?id=<?php echo ($id); ?>" method="post" onsubmit="return ac_from();" enctype="multipart/form-data">
		<ul class="aaa_pts_5">
			<li>
				<div class="d1">内容名称:</div>
				<div>
					<input class="inp_1" name="name" id="name" value="<?php echo ($v["name"]); ?>"/>
				</div>
			</li>
      <li>
        <div class="d1">内容分类:</div>
        <div>
          <select name="cid" id="cid">
            <option value="1" <?php if($v["cid"] == 1): ?>selected="selected"<?php endif; ?>>大家讲堂</option>
            <option value="2" <?php if($v["cid"] == 2): ?>selected="selected"<?php endif; ?>>学术资讯</option>
            <option value="3" <?php if($v["cid"] == 3): ?>selected="selected"<?php endif; ?>>会议动态</option>
          </select>
        </div>
      </li>
      <li>
        <div class="d1">描述:</div>
        <div>
          <input class="inp_1" name="digest" style="width:350px" id="digest" value="<?php echo ($v["digest"]); ?>"/>
        </div>
      </li>
      <li>
          <div class="d1">缩略图:</div>
           <div>
            <?php if ($v['photo']) { ?>
                  <img src="/minifengshimy/Data/<?php echo $v['photo']; ?>" width="80" height="80" style="margin-bottom: 3px;" />
                  <br />
              <?php } ?>
              <input type="file" name="photo" id="photo" />
            </div>
        </li>
      <!-- <li>
        <div class="d1">产品id:</div>
        <div>
          <input class="inp_1" name="pro_id" style="width:350px" id="pro_id" value="<?php echo ($v["pro_id"]); ?>"/>
        </div>
      </li> -->
      <li>
        <div class="d1">视频:</div>
        <div>
          <input type="radio" name="type" value="0" onclick="show('link')" <?php echo $v['type']===0 ? "checked=''" : NULL;?>/>链接&nbsp&nbsp&nbsp
          <input type="radio" name="type" value="1" onclick="show('upload')"<?php echo $v['type']==1 ? "checked=''" : NULL;?>/>自行上传
        </div>
      </li>
      <!-- 链接 -->
      <li id="link" style="display:<?php echo $v['type']===0 ? 'block' : 'none';?>">
        <div class="d1">链接:</div>
        <div>
          <input class="inp_1" name="video" style="width:400px" id="url"  <?php echo $v['type']===0 ? "" : "disabled='true'";?>/>
          <div>链接请填完整视频链接,带http:// | https://</div>
        </div>
      </li>
      <!-- 自行上传 -->
        
        
        <li id="upload" style="display:<?php echo $v['type']==1 ? 'block' : 'none';?>">
          <div class="d1">上传视频:</div>
           <div>
              <input type="file" name="video" id="video"  title="点我上传"  <?php echo $v['type']==1 ? "" : "disabled='true'";?>/>
           </div>
           <br>
         </li>
         <li>
            <div style="color:#c00; font-size:14px; padding-left:20px;">注意: 自行上传的视频大小请低于50M，格式支持MP4/WMV</div>
        </li>
        <?php if($v["video"] != ''): ?><li>
              <div class="d1">视频链接:</div>
              <div>
                <input class="inp_1" name="oldurl" style="width:400px" value="<?php echo ($v["video"]); ?>"/>
              </div>
            </li><?php endif; ?>
      <li>
          <div class="d1">内容详情:</div>
          <div>
            <textarea class="inp_1 inp_2" name="content" id="content"/><?php echo ($v["content"]); ?></textarea>
          </div>
       </li>
      <!--  <li>
          <div class="d1">浏览量:</div>
          <div>
            <input class="inp_1" style="width:150px;" name="visit" id="visit" value="<?php echo ($v["visit"]); ?>"/> 
          </div>
       </li>
       <li>
          <div class="d1">人气:</div>
          <div>
            <input class="inp_1" style="width:150px;" name="renqi" id="renqi" value="<?php echo ($v["renqi"]); ?>"/> 
          </div>
       </li>
      <li>
          <div class="d1">排序:</div>
          <div>
            <input class="inp_1" style="width:150px;" name="sort" id="sort" value="<?php echo ($v["sort"]); ?>"/> &nbsp;&nbsp;排序数量越大，排名越靠前
          </div>
       </li> -->
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