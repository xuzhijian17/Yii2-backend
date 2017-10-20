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
	<body class="app_body app_detail">
	<div class="app_page pT145">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">
					<div class="appTitle">交易记录</div>
				</div>
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>-->
			</div>
			<div class="app_tab_Type clearfix fzS24">
				<div class="appTabItem recordAll curItem">全部</div>
				<div class="appTabItem recordGo">进行中</div>
				<div class="appTabItem subNav"><span class="submenuTxt">分类</span><span class="sortIco"></span></div>
				<span class="curLine"></span>
			</div>
		</div>
		<div class="drop_down">
			下拉刷新
		</div>
		<div class="drop_content">
			<div class="recordList">
				
			</div>
		</div>
		<!--app_content end-->
		<div class="drop_up">
			上拉加载
		</div>
	</div>
<div class="subNav_Warp"></div>
<div class="subNavlist">
	<ul>
		<li class="record_Buy_Tag" type=2>买入</li>
		<li class="record_Sell_Tag" type=3>卖出</li>
		<li class="record_FH" type=4>分红</li>
		<li class="record_DT_Tag" type=5>定投</li>
		<li class="record_Other_Tag" type=6>其他</li>
	</ul>
</div>	
<script id="orderlist" type="text/html">
{{each list as value i}}
	{{if value['TradeType'] == 1}}
	<a class="lineLink record recordGoing record_QRZ record_Sell" href="#">
	{{else}}
	<a class="lineLink record recordGoing record_QRZ record_Buy" href="#">
	{{/if}}
		<div class="rstWarp">
			<div class="payIco">
				{{if value['TradeType'] == 0}}
				<span class="icoBuy">
					买入
				</span>
				{{else if value['TradeType'] == 1}}
				<span class="icoSell">
					卖出
				</span>
				{{else if value['TradeType'] == 2}}
				<span class="icoBuy">
					定投
				</span>
				{{else if value['TradeType'] == 3}}	
				<span class="icoBuy">
					分红
				</span>
				{{/if}}				
			</div>
			<div class="payName">
				<span class="payNameT fzS28 mB10">{{value['InfoJson']['fname']}}</span>
				<span class="payNameC">{{value['SysTime']}}</span>
			</div>
			<div class="payInfo">
				<span class="buyMoney">
				{{if value['TradeStatus'] == 2}}
					{{value['ConfirmShare']}}元
				{{else}}
				{{value['ApplyAmount']}}元
				{{/if}}
				</span>
				{{if value['TradeStatus'] == -1}}
					<span class="buyState">已失效</span>
				{{else if value['TradeStatus'] == 0}}
					<span class="buyState">未付款</span>
				{{else if value['TradeStatus'] == 1}}
					<span class="buyState">未确认</span>
				{{else if value['TradeStatus'] == 2}}	
					<span class="buyState">已确认</span>
				{{else if value['TradeStatus'] == 3}}
					<span class="buyState">已撤单</span>
				{{else}}
					<span class="buyState">111</span>
				{{/if}}
			</div>
		</div>
	</a>
{{/each}}
</script>
<script src="<?php echo Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/mine.js"></script>
<script src="<?php echo Yii::getAlias('@web');?>/js/template.js"></script>
<script type="text/javascript">
function loadMore(t) {
	$.get("<?=Url::to('record')?>", {type:t}, function(data) {
		var html = template('orderlist', data);
		$(".recordList").empty().append(html);
	}, 'json');
}
$(document).ready(function(){
	loadMore(0)
	$('body').delegate('.recordGo', 'click', function(){
		loadMore(1)
	})
	$('body').delegate('.recordAll', 'click', function(){
		loadMore(0)
	})
	$('.subNavlist li').click(function(){
		loadMore($(this).attr('type'));
	})
	var fixTop;
	$(".touchArea").on("touchmove",function(){
		fixTop = $(document).scrollTop();
		if(fixTop>380){
			$(".tabSibling").addClass("output_app_TopFix")
		}else{
			$(".tabSibling").removeClass("output_app_TopFix")
		}
	})

	$(document).scroll(function(){
		fixTop = $(document).scrollTop();
		if(fixTop>380){
			$(".tabSibling").addClass("output_app_TopFix")
		}else{
			$(".tabSibling").removeClass("output_app_TopFix")
		}
	})
})
</script>
</body>
</html>