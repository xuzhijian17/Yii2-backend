<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=640,user-scalable=no, minimum-scale=0.5,target-densitydpi=320" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection"content="telephone=no" />
	<link href="<?= Yii::getAlias('@web');?>/css/fund.css" rel="stylesheet">
	<script src="<?= Yii::getAlias('@web');?>/js/jquery.min.js"></script>
    <title><?= \yii\helpers\Html::encode($this->title) ?></title>
</head>
<body class="app_body app_detail">
<div class="app_page touchArea pT75">
	<div class="app_top_fid">
		<div class="app_topbar">
			<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
			<div class="app_title">我的账户</div>
			<div class="app_Rlink"><a href="javascript:void(0);" class="hostory">历史</a></div><!--能否撤单分别用 cancelOrder unCancelOrder-->
		</div>
	</div>
	<div class="accountTop">
		<div class="accountInfo">
			<span class="accountName fzS20">总金额（元）</span>
			<span class="accountTotal fzS65"><?= isset($rs['totalAssets']) ? $rs['totalAssets'] : 0.00;?></span>
		</div>
		<div class="accountProfit">
			<div class="yDayProfit">
				<span class="profitName fzS20">昨日收益（元）</span>
				<span class="profitTotal fzS30"><?= isset($rs['totaldayincomesum']) ? $rs['totaldayincomesum'] : 0.00;?></span>
			</div>
			<div class="allProfit">
				<span class="profitName fzS20">累计收益（元）</span>
				<span class="profitTotal fzS30"><?= isset($rs['totaldayincomesum']) ? $rs['totaldayincomesum'] : 0.00;?></span>
			</div>
		</div>
	</div>
	<div class="app_TopFix">
		<div class="app_tab_Type tabSibling clearfix fzS24">
			<div class="appTabItem curItem">持有中</div>
			<div class="appTabItem process"><span class="status">处理中</span></div><!--有新的消息请添加样式news-->
			<span class="curLine"></span>
		</div>
		
		<div class="typeContentWarp">
			<div class="typeContent curCnt">
				<?php if(isset($rs['list']['confirmlist']) && !empty($rs['list']['confirmlist'])): ?>
					<?php foreach($rs['list']['confirmlist'] as $key=>$value): ?>
						<a class="lineLink actList" href="javascript:void(0);">
							<dl class="actListDl">
								<dt>
									<span class="actListT"><?= $value['fundname']?></span>
									<?php
										if ($value['redate']) {
											echo '<span class="actstate fhState">预计'.date("m月d日",strtotime($value['redate'])).'分红</span>';
										}
									?>
								</dt>
								<dd class="w240">
									<span class="fzS20 col9">金额</span>
									<span class="fzS26 col3"><?= $value['marketvalue'];?></span>
								</dd>
								<dd class="w120 tac mR100">
									<span class="fzS20 col9">昨日收益</span>
									<?php
										if ($value['dayincome'] > 0) {
											echo '<span class="fzS26 rise">+'.$value['dayincome'].'</span>';
										}elseif ($value['dayincome'] < 0) {
											echo '<span class="fzS26 fall">'.$value['dayincome'].'</span>';
										}else{
											echo '<span class="fzS26 flat">'.$value['dayincome'].'</span>';
										}
									?>
								</dd>
								<dd class="w120 tac">
									<span class="fzS20 col9">累计收益</span>
									<?php
										if ($value['totalincome'] > 0) {
											echo '<span class="fzS26 rise">+'.$value['totalincome'].'</span>';
										}elseif ($value['totalincome'] < 0) {
											echo '<span class="fzS26 fall">'.$value['totalincome'].'</span>';
										}else{
											echo '<span class="fzS26 flat">'.$value['totalincome'].'</span>';
										}
									?>
								</dd>
							</dl>
						</a>
					<?php endforeach;?>
				<?php else: ?>
					<div class="noCy">
						<span class="col9 tac fzS20 mB10">您还未持有基金</span>
						<a class="colA tac fzS26" href="jijinchaoshi.html">立即购买</a>
					</div><!--没有持有的时候用This-->
				<?php endif;?>
			</div>
			<!--typeContent end-->
			<div class="typeContent">
				<?php if(isset($rs['list']['tradelist']) && !empty($rs['list']['tradelist'])): ?>
					<?php foreach($rs['list']['tradelist'] as $key=>$value): ?>
						<a class="lineLink actList" href="javascript:void(0);">
							<dl class="actListDl">
								<dt>
									<span class="actListT"><?= $value['fundname']?><small>(<?= $value['fundcode']?>)</small></span>
									<span class="actstate"><?= $value['businflagStr'].$value['confirmstat']?></span>
								</dt>
								<dd class="w240">
									<span class="fzS20 col9"><?= $value['priceflagStr'];?></span>
									<span class="fzS26 col3"><?= $value['applyval'];?></span>
								</dd>
								<dd class="w120 tac mR100">
									<span class="fzS20 col9">待确认笔数</span>
									<span class="fzS26 col3"><?= $value['tradefundnum'];?></span>
								</dd>
							</dl>
							<dl class="actListInfo">
								<?php 
									if ($value['callingcode'] == '022') {
										echo '<dd>预计<span class="colA">3</span>个工作日内完成份额确认</dd>';
									}elseif ($value['callingcode'] == '024') {
										echo '<dd>将赎回至尾号为'.substr($value['bankacco'],-4).'的银行卡</dd>';
									}elseif ($value['callingcode'] == '020') {
										echo '<dd>新发基金，募集期和封闭期结束后才显示收益</dd>';
									}else{
										echo '<dd>预计<span class="colA">3</span>个工作日内完成份额确认</dd>';
									}
									// echo $value['callingcode'];
								?>
							</dl>
						</a>
					<?php endforeach;?>
				<?php else: ?>
					<div class="no_data"></div>
				<?php endif;?>					
			</div>
			<!--typeContent end-->
		</div>
		<!--typeContentWarp end-->
	</div>
	<!--app_TopFix end-->
	<div class="equalW accoutBut">
		<ul>
			<li><a href="jijinchaoshi.html" class="lineLink colA">买入</a></li>
			<li><a href="zhanghu_maichu.html" class="lineLink colA">卖出</a></li>
			<li><a href="jijinchaoshi.html" class="lineLink colA">定投</a></li>
		</ul>
	</div>
</div>
<script src="<?php \yii\helpers\Url::base()?>/js/mine.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var fixTop;
	$(".touchArea").on("touchmove",function(){
		fixTop = $(document).scrollTop();
		if(fixTop>280){
			$(".tabSibling").addClass("output_app_TopFix")
		}else{
			$(".tabSibling").removeClass("output_app_TopFix")
		}
	})

	$(document).scroll(function(){
		fixTop = $(document).scrollTop();
		if(fixTop>280){
			$(".tabSibling").addClass("output_app_TopFix")
		}else{
			$(".tabSibling").removeClass("output_app_TopFix")
		}
	})


	/*$('.process').on('click',function(e){
		var uid = getCookie('zp');
		$.ajax({
	        type: 'GET',
	        async: true,
	        url: '<?php //echo \yii\helpers\Url::to(['fund-account/remove-process']);?>',
	        data: {uid:uid, status:0},
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){
	        },
	        complete: function(XMLHttpRequest, textStatus){
	        },
	        success: function(rs){
	        	if (rs !== false) {
	        		$('.status').removeClass('news');
	        	}
	        	console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
	});*/


	/*var ws = new WebSocket("ws://192.168.1.9:3000/");
	ws.onmessage = function (event) {    
		// console.log(event.data);
		if(event.data){
			var data = JSON.parse(event.data);
			var tradeStatus = JSON.parse(data.tradeStatus);
			console.log(tradeStatus);
			if (tradeStatus["status"] > 0) {
				$('.status').addClass('news');
			}
		}
	};*/ 
	
})

function getCookie(c_name)
{
if (document.cookie.length>0)
  {
  c_start=document.cookie.indexOf(c_name + "=")
  if (c_start!=-1)
    { 
    c_start=c_start + c_name.length+1 
    c_end=document.cookie.indexOf(";",c_start)
    if (c_end==-1) c_end=document.cookie.length
    return unescape(document.cookie.substring(c_start,c_end))
    } 
  }
return ""
}
</script>
</body>
</html>