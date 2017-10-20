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
	<link href="<?=Yii::getAlias('@web');?>/css/fund.css" rel="stylesheet">
	<title>风险测评</title>
</head>
<body class="app_body">
	<div class="app_page pT75 fixBut">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:void(0);">返回</a></div>
				<div class="app_title">风险测评</div>
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
			</div>
		</div>
		<form id="form">
		<?php foreach($data as $key=>$val){ ?>
		<div class="app_section">
			<h2 class="block_title"><?=$val['qtitle']?></h2>
			<input type="hidden" name="answer<?=$key?>[]" value="<?=$val['qno']?>"/> 
			<div class="radioWarp">
			<?php foreach($val['qitem'] as $k=>$v){ ?>
				<div class="radioItem">
					<label class="radioLabel"><input type="radio" class="radioInput" name="answer<?=$key?>[]" value="<?=$v['itemvalue']?>" /><?=$v['itemtitle']?></label>
				</div>
			<?php } ?>
			</div>
		</div>
		<?php } ?>
		<!--app_section end-->
		<div class="afundBut app_section riskBut">
			<a href="javascript:void(0);" class="lineLink riskSubmit">提交</a>
			<a href="fengxianpingce_jieguo.html" class="lineLink riskGolink">不做了，我是保守型</a>
		</div>
		</form>
	</div>
    
</body>
</html>
<script src="<?=Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<script src="<?=Yii::getAlias('@web');?>/js/mine.js"></script>
<script src="<?=Yii::getAlias('@web');?>/js/template.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var count = <?=count($data)?>;
	var sub = true;
	$('.riskSubmit').on('click', function(){
		$.post('<?=Url::to('queryrisk')?>', $("#form").serialize(), function(data){
			if(data.error == 1){
				window.location.href="<?=Url::to('riskshow')?>"
			}else{
				hintPop(data.msg);
			}
		})
	})
})
</script>
