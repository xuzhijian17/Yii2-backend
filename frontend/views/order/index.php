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
				<div class="app_Rlink"><a href="zhanghu_fenhong.html" class="">分红</a></div>
			</div>
		</div>
		<!--<div class="bonusTips">
			<span class="tipsText">分红日（03-08）当天基金净值中会扣除分红资金，可能会导致当日净值下跌。预计分红4.91元将在派息日（03-10）直接发放该基金份额或者以现金形式发放到银行卡。</span>
			<span class="linkIcoR2"></span>
		</div>-->
		<!--bonusTips end-->
		<div class="app_section">
			<a class="lineLink" href="jijin.html">
				<div class="singleTop">
					<span class="col3 fL fzS28"><?=$data['ChiNameAbbr']?></span>
					<span class="linkIcoR"></span>
				</div>
			</a>
			<div class="profitInfo">
				<span class="profitText">最新收益（元）  <?=$data['UpdateTime']?></span>
				<span class="profitNum"><?=$data['profit']?></span><!--负收益fall，正收益rise-->
			</div>
		</div>
		<!--app_section end-->
		<div class="app_section">
			<div class="profitList">
				<ul><!--负收益fall，正收益rise-->
					<li><span class="col9">持有金额</span><span class="col3"><?=$data['money']?></span></li>
					<li><span class="col9">累计收益</span><span class="<?=$data['profit'] < 0 ? 'fall' : 'rise'?>"><?=$data['profit']?></span></li>
					<li><span class="col9">持有份额</span><span class="col3"><?=$data['share']?></span></li>
					<li><span class="col9">最新净值</span><span class="col3"><?=$data['NVDailyGrowthRate']?></span></li>
				</ul>
			</div>
			<div class="profitTab">
				<div class="app_tab_Type tabSibling clearfix fzS24">
					<div class="appTabItem curItem"><span class="">每日收益</span></div>
					<div class="appTabItem"><span class="">基金净值</span></div><!--有新的消息请添加样式news-->
					<div class="appTabItem"><span class="">交易记录</span></div><!--有新的消息请添加样式news-->
					<span class="curLine"></span>
				</div>
				
				<div class="typeContentWarp">
					<div class="typeContent curCnt">
						<div class="bgFFF">
							<!--暂无数据时添加<div class="no_data"></div>-->
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							<a class="lineLink" href="javascript:void(0);">
								<div class="linkList">
									<div class="fzS26 fL col6">2016-04-24</div>
									<div class="fzS26 fR rise">0.92</div>
								</div>
							</a>
							
							<a class="lineLink colA tac pTB15" href="javascript:void(0);">
								查看全部
							</a>
						</div>
					</div>
					<!--typeContent end-->
					<div class="typeContent">
						<!--暂无数据时添加<div class="no_data"></div>-->
						<div class="bgFFF netval_show">
							
						</div>
						<a class="lineLink colA tac pTB15 netval_click bgFFF" href="javascript:void(0);">
							查看全部
						</a>
					</div>
					<!--typeContent end-->
					<div class="typeContent">
						<!--暂无数据时添加<div class="no_data"></div>-->
						<div class="bgFFF orderlist">
							
						</div>
					</div>
					<!--typeContent end-->
				</div>
				<!--typeContentWarp end-->				
			</div>
			<!--profitTab end-->
		</div>
		<div class="equalW accoutBut">
			<ul>
				<li><a href="jiaoyi_goumai.html" class="lineLink colA">买入</a></li>
				<li><a href="jiaoyi_maichu.html" class="lineLink colA">卖出</a></li>
			</ul>
		</div>
	</div>	
<script id="netval" type="text/html">
{{each list as value i}}
	<a class="lineLink" href="javascript:void(0);">
		<div class="linkList">
			<div class="fzS26 fL col6">{{value.EndDate}}</div>
			<div class="fzS26 fR col3">{{value.UnitNV}}</div>
		</div>
	</a>
{{/each}}
</script>
<script id="orderlist" type="text/html">
{{each list as value i}}
	<a class="lineLink" href="javascript:void(0);">
		<div class="linkList">
			<div class="fL">
				<span class="fzS26 col3">
				{{if value['TradeType'] == 0}}
					买入
				{{else if value['TradeType'] == 1}}
					卖出
				{{else if value['TradeType'] == 2}}
					定投
				{{else if value['TradeType'] == 3}}	
					分红
				{{/if}}
				</span>
				<span class="fzS20 col9">{{value['SysTime'].substr(0, 10)}}</span>
			</div>
			<div class="fR">
				<span class="fzS26 col3">
				{{if value['TradeStatus'] == 2}}
					{{value['ConfirmShare']}}元
				{{else}}
				{{value['ApplyAmount']}}元
				{{/if}}
				</span>
				<!--确认中：stateQR待支付：stateDD关闭：stateGB成功：stateCG-->
				{{if value['TradeStatus'] == -1}}
					<span class="fzS20 tar stateQR">已失效</span>
				{{else if value['TradeStatus'] == 0}}
					<span class="fzS20 tar stateQR">未付款</span>
				{{else if value['TradeStatus'] == 1}}
					<span class="fzS20 tar stateQR">未确认</span>
				{{else if value['TradeStatus'] == 2}}	
					<span class="fzS20 tar stateQR">已确认</span>
				{{else if value['TradeStatus'] == 3}}
					<span class="fzS20 tar stateQR">已撤单</span>
				{{else}}
					<span class="fzS20 tar stateQR">111</span>
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
var InnerCode = <?=$data['InnerCode']?>;
var page = 1;
var loading = false;
function loadMore() {
	if (loading === false) {
		loading = true;
		$.get("<?=Url::to('netvalue')?>", {InnerCode:InnerCode, page: page}, function(data) {
			page++;
			var html = template('netval', data);
			$(".netval_show").append(html);
			loading = false;
		}, 'json');
	} else {
		return;
	}
}

var page1 = 1;
var loading1 = false;
function loadMore1() {
	if (loading1 === false) {
		loading1 = true;
		$.get("<?=Url::to('orderlist')?>", {InnerCode:InnerCode, page: page}, function(data) {
			page++;
			var html = template('orderlist', data);
			$(".orderlist").append(html);
			loading1 = false;
		}, 'json');
	} else {
		return;
	}
}
$(document).ready(function(){
	loadMore(); loadMore1()
	$('body').delegate('.netval_click', 'click', function(){
		loadMore()
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