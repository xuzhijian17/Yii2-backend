<?php
use yii\helpers\Url;
?>
<section class="banner customerBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<li><a href="<?= Url::to(['site/call-center']);?>" class="subLink">开户指南</a></li>
			<li><a href="<?= Url::to(['site/trade-guide']);?>" class="subLink">交易指南</a></li>
			<li><a href="<?= Url::to(['site/trade-quota']);?>" class="subLink">交易限额</a></li>
			<li><a href="<?= Url::to(['site/basis']);?>" class="subLink">基础知识</a></li>
			<li><a href="<?= Url::to(['site/investor']);?>" class="subLink">投资者权益须知</a></li>
			<li><a href="<?= Url::to(['site/risk-warning']);?>" class="subLink">风险提示</a></li>
			<li><a href="<?= Url::to(['site/anti-money-laundering']);?>" class="subLink">反洗钱</a></li>
			<li><a href="<?= Url::to(['site/feedback']);?>" class="subLink cur">问题反馈</a></li>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT">问题反馈</h2>
		<div class="newsCnt feedback">
			<div class="formText">若方便，请留下您的宝贵意见，汇成基金将根据您的需求，竭诚为您打造属于您的产品。</div>
			<div class="item">
				<label class="textLabel">意见标题：</label>
				<div class="textItem"><input type="text" name="" id="title" value="" placeholder="请输入20字以内标题" /></div>
			</div>
			<div class="item">
				<label class="textLabel">联系方式：</label>
				<div class="textItem"><input type="text" name="" id="contact" value="" placeholder="根据自己的方便情况选择是否留下联系方式" /></div>
			</div>
			<div class="item">
				<label class="textLabel">内容：</label>
				<div class="areaItem"><textarea name="" id="content" rows="" cols="" placeholder="请输入100字以内问题"></textarea></div>
			</div>
			<div class="item">
				<div class="buttonItem"><a href="javascript:void(0);" class="buttonA feedback-btn">提交</a></div>
			</div>
		</div>
	</div>
</section>
<script>
	$(function(){
		$(".feedback-btn").on('click',function(){
			var title = $('#title').val();
			var contact = $('#contact').val();
			var content = $('#content').val();
			
			if(!title || !contact || !content){
				return;
			}
			
			alert('您的问题已提交！');
			window.location.reload();
		});
	});
</script>

