<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=640,user-scalable=no, minimum-scale=0.5,target-densitydpi=320" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection"content="telephone=no" />
	<link href="<?php echo Yii::getAlias('@web');?>/css/fund.css" rel="stylesheet">
	<title>登陆</title>
</head>
<body class="app_body">
	<div class="app_page loginPage">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:void(0);">返回</a></div>
				<!--<div class="app_title">首页</div>-->
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
			</div>
		</div>
		<div class="banner">
			<div class="logo"></div>
		</div>
		<form id="login">
		<div class="form_section loginForm">
			<div class="appItem loginUser">
				<label class="appLabel">帐号</label>
				<div class="appTextItem">
					<input type="text" class="appTextInput showRst telephone" name="text" placeholder="手机号/身份证号">
					<a href="javascript:void(0)" class="appRst">清空</a>
				</div>
			</div>
			<div class="appItem loginPsw">
				<label class="appLabel">密码</label>
				<div class="appTextItem">
					<input type="password" class="appTextInput showRst telephone" name="password" placeholder="交易密码">
					<a href="javascript:void(0)" class="appRst">清空</a>
				</div>
			</div>
		</div>	
		<div class="button_Two">
			<div class="buttonL">
				<a href="javascript:void(0)" class="buttonB">十秒开户</a>
			</div>
			<div class="buttonR">
				<a href="javascript:void(0)" class="buttonA">登陆</a>
			</div>
		</div>
		<div class="tar fzS20 mLR30">
			<a class="colA" href="javascript:void(0);">忘记密码？</a>
		</div>
		</form>
		<!--app_content end-->
	</div>
<script src="<?php echo Yii::getAlias('@web');?>/js/jquery.min.js"></script>  
<script src="<?php echo Yii::getAlias('@web');?>/js/mine.js"></script>
<script type="text/javascript">
$(document).ready(function(e){
	$('.buttonA').click(function(){
		var text = $('input[type="text"]').val();
		var password = $('input[type="password"]').val();
		// if((text.length == 11 && (text.substr(0,2) != '12' || text.substr(0,2) != '13')) ||
			// text.length != 16){
			// hintPop('手机号/身份证号不正确!'); return;
		// }
		if(password == ''){
			hintPop('密码不能为空!'); return;
		}
		$.post("<?=Url::to('login')?>", $("#login").serialize(), function(data){
			if(data.error){
				window.location.href = data.url; return;
			}
			hintPop(data.msg); 
		})
	})
})
</script>	
</body>
</html>


