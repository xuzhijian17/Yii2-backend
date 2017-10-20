<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Url;
use yii\helpers\Html;

$productMenu = ['product','detail','fund-bulletin-detail','bulletin-detail'];
$newsMenu = ['news','news-detail'];
$openAccount = ['open-account','clos-account','data-change','online-trading','download'];
$callCenter = ['call-center','trade-guide','trade-quota','basis','investor','risk-warning','anti-money-laundering','feedback'];
$about = ['about','culture','practitioners','aptitude','contact','recruit'];
$keywords = "汇成,汇成基金,场外基金投资,基金销售,汇成一账户,基金一站通,FOF";
$descriptions = "汇成基金将始终秉承诚信、创新的态度，以客观、公正的视角，为投资者提供更理性、更有价值的基金理财服务，使投资者的财富实现不断增长。";

if (!empty($this->params['keywords'])) {
	$keywords = $this->params['keywords'];
}
if (!empty($this->params['descriptions'])) {
	$descriptions = $this->params['descriptions'];
}

if(Yii::$app->controller->action->id == 'index'){
	$this->title = '证监会批准的独立基金销售机构';
}else if(in_array(Yii::$app->controller->action->id, $productMenu)){
	$this->title = '基金产品';
}else if(in_array(Yii::$app->controller->action->id, $newsMenu)){
	$this->title = '信息资讯';
	if(!empty($this->params['title'])){
		$this->title = $this->params['title'];
	}
	if (!empty($this->params['keywords'])) {
		$keywords = $this->params['keywords'];
	}

	if (!empty($this->params['descriptions'])) {
		$descriptions = $this->params['descriptions'];
	}
}else if(in_array(Yii::$app->controller->action->id, $openAccount)){
	$this->title = '企业理财';
}else if(in_array(Yii::$app->controller->action->id, $callCenter)){
	$this->title = '客服中心';
}else if(in_array(Yii::$app->controller->action->id, $about)){
	$this->title = '关于我们';
}else{
	$this->title = '汇成基金';
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1, minimum-scale=1,target-densitydpi=device-width" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="keywords" content="<?=$keywords?>"/>
	<meta name="description" content="<?=$descriptions?>"/>
	<link href="<?= Url::base();?>/css/animate.min.css" rel="stylesheet">
	<link href="<?= Url::base();?>/css/fundZone.css" rel="stylesheet">
	<script type="text/javascript" src="<?= Url::base();?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?= Url::base();?>/js/vue.min.js"></script>
	<script type="text/javascript" src="<?= Url::base();?>/js/spin.min.js"></script>
	<title><?= '汇成基金-'.Html::encode($this->title) ?></title>
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?addbdd6db57fca133ed6b8300d809570";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>	
</head>
<body>
	<div class="container">
		<div class="mainWarp">
			<header class="header fixed">
				<div class="top">
					<div class="maxW">
						<span class="inline hotLine">客服热线：400-619-9059</span>
						<?php if (empty(Yii::$app->session['user_login'])) {?>
						<!--login before start-->
						<div class="notLogin">
							<span class="inline fR">
								<a href="javascript:alert('建设中')" class="openAccount outLink">开户</a>
							</span>
							<span class="inline fR userLink">
								<a href="javascript:void(0);" class="login outLink">登录</a>
								<div class="loginS">
									<ul>
										<li><a href="https://trade.fundzone.cn:8443/etrading/" class="actLink">个人账户</a></li>
										<li><a href="/company/login/" class="actLink">企业账户</a></li>
									</ul>
								</div>
							</span>
						</div>
						<!--login before end-->
						<?php } else {?>
						<!--登陆后-->
						<div class="userInfo">
							<span class="inline fR">
								<a href="<?= Url::base();?>/user/loginout/" class="openAccount outLink">退出</a>
							</span>
							<span class="inline fR userLink">
								<a href="javascript:void(0);" class="login user">我</a>
								<div class="loginS">
									<ul>
										<li><a href="<?= Url::base();?>/position/assets/" class="actLink">我的资产</a></li>
										<li><a href="<?= Url::base();?>/record/list/" class="actLink">交易记录</a></li>
										<li><a href="<?= Url::base();?>/user/changepassword/" class="actLink">修改密码</a></li>
									</ul>
								</div>
							</span>
						</div>
						<!--登陆后-->
						<?php }?>
					</div>
				</div>
				<div class="maxW">
					<h1><a href="<?= Yii::$app->params['homeHost'];?>" class="logo">汇成基金<!--<img src="<?= Url::base();?>/images/logo.png" width="100%"/>--></a></h1>
					<nav class="nav">
						<a href="<?= Yii::$app->params['homeHost'].'site/index';?>" class="navLink <?= Yii::$app->controller->action->id == 'index' ? 'cur' : ''; ?>">首页</a>
						<a href="<?= Yii::$app->params['homeHost'].'site/product';?>" class="navLink <?= in_array(Yii::$app->controller->action->id, $productMenu) ? 'cur' : ''; ?>">基金产品</a>
						<a href="<?= Yii::$app->params['homeHost'].'site/news';?>" class="navLink <?= in_array(Yii::$app->controller->action->id, $newsMenu) ? 'cur' : ''; ?>">信息资讯</a>
						<a href="<?= Yii::$app->params['homeHost'].'site/open-account';?>" class="navLink <?= in_array(Yii::$app->controller->action->id, $openAccount) ? 'cur' : ''; ?>">企业理财</a>
						<a href="<?= Yii::$app->params['homeHost'].'site/call-center';?>" class="navLink <?= in_array(Yii::$app->controller->action->id, $callCenter) ? 'cur' : ''; ?>">客服中心</a>
						<a href="<?= Yii::$app->params['homeHost'].'site/about';?>" class="navLink <?= in_array(Yii::$app->controller->action->id, $about) ? 'cur' : ''; ?>">关于我们</a>
					</nav>
				</div>
			</header>
		
			<?= $content ?>
		</div>
	</div>
	<!--block_section end-->
	<footer class="footer">
		<div class="maxW fixed">
			<div class="footerCantact">
				<div class="fs36">400-619-9059</div>
				<div class="col96">工作日 9:00-18:00</div>
				<div class="col96">邮箱：<a href="mailto:service@fundzone.cn" class="col96">service@fundzone.cn</a></div>
			</div>
			<div class="footerNav fixed">
				<ul>
					<li>
						<span class="fNavTitle">关于我们</span>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/about';?>">公司介绍</a>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/culture';?>">企业文化</a>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/practitioners';?>">基金从业信息</a>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/aptitude';?>">资质证明</a>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/contact';?>">联系方式</a>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/recruit';?>">诚聘英才</a>
					</li>
					<li>
						<span class="fNavTitle">帮助中心</span>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/call-center';?>">开户指南</a>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/trade-guide';?>">交易指南</a>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/trade-quota';?>">交易限额</a>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/basis';?>">基础知识</a>
					</li>
					<li style="width: auto;">
						<span class="fNavTitle">投资者权益</span>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/investor';?>">投资者权益</a>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/risk-warning';?>">风险提示</a>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/anti-money-laundering';?>">反洗钱</a>
						<a class="fNavLink" href="<?= Yii::$app->params['homeHost'].'site/feedback';?>">问题反馈</a>
					</li>
					<li class="snsWarp">
						<span class="fNavTitle" style="padding-left: 18px;">关注我们</span>
						<a class="snsIco snsWX" href="javascript:void(0);">微信<div class="QRcode"><img src="<?= Url::base();?>/images/qrcode.jpg"></div></a>
						<a class="snsIco snsWB" href="http://weibo.com/u/5865542702?refer_flag=1001030101_&is_all=1" target="_blank">新浪微博</a>
					</li>
				</ul>
			</div>
			<!--footerNav end-->
		</div>
		<!--container end-->
		<div class="copyRight">
			北京汇成基金销售有限公司版权所有<br />
			Copyright © 2015-2016 www.fundzone.cn All rights reserved<br />
			基金销售业务资格证书[000000328]&nbsp; [京ICP备16056442号]
		</div>
	</footer>
	<script type="text/javascript" src="<?= Url::base();?>/js/fundZone.js"></script>
</body>
</html>
