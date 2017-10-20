<?php
use yii\helpers\Url;
?>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=640,user-scalable=no, minimum-scale=0.5,target-densitydpi=320" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection"content="telephone=no" />
	<link href="<?php echo Yii::getAlias('@web');?>/css/fund.css" rel="stylesheet">
	<title>基金收益</title>
</head>
<body class="app_body">
	<div class="app_page pT75">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">基金收益</div>
				<!--<div class="app_Rlink"><a href="zhanghu_fenhong.html" class="">分红</a></div>-->
			</div>
		</div>
		<div class="app_section">
			<a class="lineLink" href="javascript:void(0);">
				<div class="bonusItem <?=$data['fundtype'] == 0 ? 'bonusSelected' : ''?>" type="0">
					<span class="col3 fzS28 mB20">现金分红</span>
					<span class="col9 fzS20">分红所得资金直接存入关联银行卡</span>
				</div>
			</a>
			<a class="lineLink" href="javascript:void(0);">
				<div class="bonusItem <?=$data['fundtype'] == 1 ? 'bonusSelected' : ''?>" type="1">
					<span class="col3 fzS28 mB20">红利再投资</span>
					<span class="col9 fzS20">分红金额直接转换成基金份额，免交易手续费和所得税</span>
				</div>
			</a>
			<input type="hidden" name="type" value="<?=$data['fundtype']?>" />
		</div>
		<!--app_section end-->
		<div class="notice_section gzTips">因基金公司规则，货币、理财基金分红方式只支持红利再投资，暂不支持的修改；股票、混合、债券等基金一般默认为现金分红，投资者可修改分红方式。</div>
		<div class="button_section modifyBut"><a class="buttonA" href="javascript:void(0);">确认</a></div>
		<!--<div class="notice_section modifyTips">
			<span class="mB10">申请修改分红方式：<font class="rise">03-10 星期四</font></span>
			<span>预计确认：<font class="rise">03-11 星期五</font></span>
		</div>-->
	</div>
<script src="<?php echo Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/mine.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/template.js"></script>
<script type="text/javascript">
var code = <?=$_GET['code']?>;
$(document).ready(function(){
	$(".bonusItem").click(function(){
		if(!$(this).parent().hasClass("bonusSelected")){
			$(this).addClass("bonusSelected").parent().siblings().children(".bonusItem").removeClass("bonusSelected");
			$('input[name="type"]').val($(this).attr('type'));
			$(".gzTips").hide();
			$(".modifyBut").show();
		}
	})
	$('.buttonA').click(function(){
		var type = $('input[name="type"]').val();
		$.get("<?=Url::to('bonus')?>", {code:code, type:type}, function(data){
			hintPop(data.msg);
		})
	})
});
</script>
</body>
</html>