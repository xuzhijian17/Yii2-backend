<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=640,user-scalable=no, minimum-scale=0.5,target-densitydpi=320" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection"content="telephone=no" />
	<link href="<?=Yii::getAlias('@web');?>/css/fund.css" rel="stylesheet">
	<title>基金</title>
	<script type="text/javascript">
		var BASE = <?=json_encode($this->params['base'])?>;
	</script>
</head>
<body class="app_body">
<?= $content ?>
</body>
</html>
