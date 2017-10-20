<?php
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=640,user-scalable=no, minimum-scale=0.5,target-densitydpi=320" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection"content="telephone=no" />
	<link href="<?php echo Yii::getAlias('@web');?>/css/fund.css" rel="stylesheet">
	<!-- <script src="<?php echo Yii::getAlias('@web');?>/js/jquery.min.js"></script> -->
	<script src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <title><?= Html::encode($this->title) ?></title>
</head>
<body class="app_body">
<?= $content ?>
</body>
</html>
<?php $this->endPage() ?>