<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=0.7,maximum-scale=0.7, minimum-scale=0.7,target-densitydpi=320" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection"content="telephone=no" />
	<link href="<?= \yii\helpers\Url::base()//Yii::getAlias('@web');?>/css/admin.css" rel="stylesheet">
	<title>登陆</title>
</head>
<body>
	<header class="header">
		<h1 class="logo"><a href="<?= \yii\helpers\Url::base();?>" class="logoLink">基金汇</a></h1>
		<div class="userInfo">
			<div class="rText">
				欢迎致电：400-619-9059
			</div>
		</div>
	</header>
	<div class="main">
		<div class="colT loginTab">
			<form name="login" class="form-signin" action="<?= \yii\helpers\Url::to(['site/login'])?>" method="post" autocomplete="on" accept-charset="UTF-8">
				<ul>
					<li><img src="<?= \yii\helpers\Url::base();?>/images/admin_banner.jpg"/></li>
					<li>
						<section class="login_section">
							<h2>商户登录</h2>
							<!-- <div class="item item_ico shName">
								<div class="itemText">
									<input type="text" name="instid" id="" value="" class="textInput" placeholder="您的商户号" />
								</div>
							</div> -->
							<!--item over-->
							<div class="item item_ico userName">
								<div class="itemText">
									<input type="text" name="username" id="" value="" class="textInput" placeholder="您的用户名" />
								</div>
							</div>
							<!--item over-->
							<div class="item item_ico passWord">
								<div class="itemText">
									<input type="password" name="password" id="" value="" class="textInput" placeholder="您的密码" />
								</div>
							</div>
							<!--item over-->
							<div class="button">
								<button class="buleAbut" type="submit">登录</button>
							</div>
							<div class="errors" style="color: red">
								<?php
									if (isset($errors['list']) && !empty($errors['list'])) {
										foreach ($errors['list'] as $key => $value) {
											foreach ($value as $k => $v) {
												echo "<span>* ".$v."</span><br>";
											}
										}
									}
								?>
							</div>
							<div class="rText">
								<a href="#">忘记密码</a>
							</div>
						</section>					
					</li>
				</ul>
			</form>
		</div>
	</div>
	<footer>
		<a href="javascript:void(0);">公司介绍</a><a href="javascript:void(0);">联系我们</a>北京汇成基金销售有限公司版权所有©2005-2014   [京ICP备15048098号-1] 
	</footer>
</body>
</html>
<script type="text/javascript" src="<?= \yii\helpers\Url::base();?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?= \yii\helpers\Url::base();?>/js/admin.js"></script>
<script>
$(document).ready(function(){
	name = $('.name').val();
	phone = $('.phone').val();
	card = $('.card').val();

	if (!name && !phone && !card) {
		$('form').submit(function () { return false; });
	}
});
</script>