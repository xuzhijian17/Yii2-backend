<?php
use yii\helpers\Url;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>汇成基金接口测试工具</title>
<script type="text/javascript" src="<?php echo Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<style type="text/css">
	#title{text-align:center;font-size:24px;}
	#paraInput{float:left;margin:0px 0px 20px 20px;}
	#console {float:right;margin:0px 0px 20px 20px;}
</style>
</head>
<body>

<div id="title">汇成基金接口测试工具 &nbsp;&nbsp;&nbsp;<a href="<?=Url::to('/sandbox/dologinout')?>">退出</a></div>
<hr />
<div id="paraInput">
请选择模块：
<form name="moduleform" id="moduleform" method="post" action='<?=Url::to('/sandbox/choseapi')?>'>
<select name="module">
<option value="account" <?php if($module =='account'){echo "selected='selected'";} ?>>账户类</option>
<option value="fund" <?php if($module =='fund'){echo "selected='selected'";} ?>>基金超市类</option>
<option value="trade" <?php if($module =='trade'){echo "selected='selected'";} ?>>交易类</option>
<option value="query" <?php if($module =='query'){echo "selected='selected'";} ?>>查询类</option>
<option value="bao" <?php if($module =='bao'){echo "selected='selected'";} ?>>现金宝类</option>
<option value="bankcard" <?php if($module =='bankcard'){echo "selected='selected'";} ?>>银行卡类</option>
<option value="portfolio" <?php if($module =='portfolio'){echo "selected='selected'";} ?>>组合交易类</option>
<option value="fund-v2" <?php if($module =='fund-v2'){echo "selected='selected'";} ?>>自定义基金列表</option>
</select>
<input type='submit' value="提交"/>
</form><br />
请选择功能：
<form name="apiform" id="apiform" method="post" action='<?=Url::to('/sandbox/getparam')?>'>
<select name="api">
<?php if (!empty($apidata) && is_array($apidata)){ 
    foreach ($apidata as $key =>$val){
?>
<option value="<?php echo $key; ?>" <?php if(isset($api) && $api==$key){echo "selected='selected'";} ?> ><?php echo $key; ?></option>
<?php }} unset($val,$key); ?>
</select>
<input type='hidden' name='module' value="<?php echo $module; ?>"/>
<input type='submit' value="提交"/>
</form>
<br>
<form name="paramform" id="paramform" method="post" action="<?=Url::to('/sandbox/apipost')?>">
<?php if (!empty($param) && is_array($param)){ 
    echo '参数填写';
    foreach ($param as $key =>$val){
?>

<?php echo $val; ?>:<input id="<?php echo $val; ?>" name="<?php echo $val; ?>" /><br />
<?php } ?>
<input type='hidden' name='url' value='<?php if (!empty($module) && !empty($api)) echo $module.'/'.$api; ?>' />
<input type='button' id="submit" value="提交"/>
<?php } ?>
</form>
</div>
<div id="console">
	提交参数：<br />
						<textarea name="param" id="param" cols="50" rows="12" style='overflow-x:scroll' ></textarea><br><br>
						<br />
                    	返回结果：<br />
						<textarea name="resultShow" id="resultShow" cols="50" rows="18" style='overflow-x:scroll' readonly></textarea>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$("#signmsg").click(function(){
		var signStr = "";
		inputs = $("form[name='paramform'] > input");
		inputs.each(function(){
			var value = $(this).val();
			var name_attr = $(this).attr('name');
			if(""!=value && "undefined" != typeof(value) && "undefined" != typeof(name_attr))
				signStr += "&" + name_attr +"="+value;
		});
		$.ajax({
			type: "post",
			url: "<?=Url::to('/sandbox/getsignature')?>",
			data:"signmsg=1"+signStr,
			dataType:'json',
// 			beforeSend: function(XMLHttpRequest){
// 				$("#sing_status").html("计算中...");
// 			},
			success: function(data, textStatus){
				if(data.code == '0'){
					$("#signmsg").val(data.data);
				}else{
					alert(data.data);
				}
			},
// 			complete: function(XMLHttpRequest, textStatus){
// 				$("#sing_status").html("点击重新计算");
// 			},
// 			error: function(){
// 				//请求出错处理
// 			}
		});
	});
	$("#submit").click(function(){
		var signStr = "";
		inputs = $("form[name='paramform'] > input");
		inputs.each(function(){
			var value = $(this).val();
			var name_attr = $(this).attr('name');
			if(""!=value && "undefined" != typeof(value) && "undefined" != typeof(name_attr))
				signStr += "&" + name_attr +"="+value;
		});
		postdata(signStr);
		return false;
	});
	//生成订单号
	$("#orderno").click(function(){
		$.post("<?=Url::to('/sandbox/getorderno')?>", function(data){
			$("#orderno").val(data);
	    });
	});
});

function postdata(signStr){ //提交数据函数
	$.ajax({
		type: "post",
		url: $("#paramform").attr("action"),
		data:"signmsg=1"+signStr,
		beforeSend: function(XMLHttpRequest){
			$("#param").val(signStr);
		},
		success: function(data, textStatus){
			$("#resultShow").val(data);
		},
		complete: function(XMLHttpRequest, textStatus){
			//alert("HideLoading()");
		},
		error: function(){
			//alert("error");
			//请求出错处理
		}
	});
}

function trim(str){ //删除左右两端的空格
	return str.replace(/(^\s*)|(\s*$)/g, "");
}
</script>
</body>
</html>