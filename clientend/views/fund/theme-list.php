<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="author" content="Kid">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="format-detection" content="telephone=no">
		<title>主题推荐</title>
		<link rel="stylesheet" type="text/css" href="<?= Yii::getAlias('@web');?>/css/fundApp.css"/>
		<script src="<?= Yii::getAlias('@web');?>/js/jquery.min.js" type="text/javascript"></script>
		<script src="<?= Yii::getAlias('@web');?>/js/slider.js" type="text/javascript"></script>
		<script src="<?= Yii::getAlias('@web');?>/js/fundApp.js" type="text/javascript"></script>
	</head>
	<body>
		<div class="topBar">
			<span class="pageT">主题推荐</span>
			<a href="javascript:history.back(-1);" class="backLink">
				<span class="back"></span>
			</a>
		</div>
		<div class="container">
			<div class="main">
				<div class="theme">
					<ul>
						<?php if(isset($recommendTheme) && !empty($recommendTheme)): ?>
							<?php foreach($recommendTheme as $key=>$value): ?>
								<li><a href="<?= Url::to(['fund/theme-detail','tid'=>$value['id']]);?>"><img src="<?= $value['Image'];?>"/></a></li>
							<?php endforeach;?>
						<?php endif;?>
					</ul>
				</div>
			</div>
			<!--main end-->
		</div>
	</body>
</html>

















