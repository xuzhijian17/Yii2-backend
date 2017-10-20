<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=640,user-scalable=no, minimum-scale=0.5,target-densitydpi=320" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection"content="telephone=no" />
	<link href="<?php echo Yii::getAlias('@web');?>/css/fund.css" rel="stylesheet">
	<title>基金超市</title>
</head>
<body class="app_body">
	<div class="app_page pT145">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">基金超市</div>
				<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach">搜索</a></div>
			</div>
			<div class="navTouch">
				<div class="app_tab_nav clearfix" style="width: 910px;">
					<div class="appTabItem curItem" data-fund-type="stock">股票型</div>
					<div class="appTabItem" data-fund-type="mix">混合型</div>
					<div class="appTabItem" data-fund-type="index">指数型</div>
					<div class="appTabItem" data-fund-type="bond">债券型</div>
					<div class="appTabItem coin" data-fund-type="currency">货币型</div>
					<div class="appTabItem coin" data-fund-type="money">理财型</div>
					<div class="appTabItem" data-fund-type="breakeven">保本型</div>
					<span class="curLine"></span>
				</div>
				<span class="moreRight"></span>
			</div>
		</div>
		<div class="drop_down">
			下拉刷新
		</div>
		<div class="app_content">
			<div class="list_columnThr_Top topSpace">
				<ul class="list_columnNav typeJJ clearfix">
					<li class="fundName">基金名称</li>
					<li class="fundVal">单位净值</li>
					<li class="fundRose sort">日涨幅&nbsp;</li>
				</ul>
				<ul class="list_columnNav typeHB clearfix" style="display: none;">
					<li class="fundName">基金名称</li>
					<li class="fundVal">万份收益</li>
					<li class="fundRose">七日年化</li>
				</ul>
			</div>
			<div class="app_tab_content clearfix">
				<!--股票型 开始-->
				<div class="app_list_columnThr drop_content app_tab_content_column tab_column_cur typeGp">
					<div class="list_up_date"><?php echo date("Y-m-d")?></div>
					<div class="list_columnThr_content">
						<?php if(!empty($stockFundData)): ?>
							<?php foreach($stockFundData as $value): ?>
								<a href="<?php echo \yii\helpers\Url::toRoute(['fund/index', 'InnerCode' => $value['InnerCode'], 'val'=>$value['UnitNV'], 'val'=>$value['NVDailyGrowthRate']]);?>" class="lineLink">
									<ul class="fundItem clearfix">
										<li class="col_fund_name w300">
											<span class="fund_name"><?php echo $value['SecuAbbr'];?></span>
											<span class="fund_Num"><?php echo $value['SecuCode'];?></span>
										</li>
										<li class="col_fund_Val w140"><?php echo $value['UnitNV'];?></li>
										<?php 
											if($value['NVDailyGrowthRate'] > 0) {
												$li = '<li class="col_fund_sort w140 rise">+'.$value['NVDailyGrowthRate'].'%</li>';
											}else if ($value['NVDailyGrowthRate'] < 0) {
												$li = '<li class="col_fund_sort w140 fall">'.$value['NVDailyGrowthRate'].'%</li>';
											}else{
												$li = '<li class="col_fund_sort w140 flat">'.$value['NVDailyGrowthRate'].'%</li>';
											}

											echo $li;
										?>
									</ul>
								</a>
							<?php endforeach; ?>
						<?php else:?>
							<div class="no_data"></div>
						<?php endif;?>
					</div>
				</div>
				<!--股票型 结束-->
				<!--混合型 开始-->
				<div class="app_list_columnThr drop_content app_tab_content_column typeHh">
					<div class="list_up_date"><?php echo date("Y-m-d")?></div>
					<div class="list_columnThr_content">
						<div class="no_data"></div>
					</div>
				</div>
				<!--混合型 结束-->
				<!--指数型 开始-->
				<div class="app_list_columnThr drop_content app_tab_content_column typeZs">
					<div class="list_up_date"><?php echo date("Y-m-d")?></div>
					<div class="list_columnThr_content">
						<div class="no_data"></div>
					</div>
				</div>
				<!--指数型 结束-->
				<!--债券型 开始-->
				<div class="app_list_columnThr drop_content app_tab_content_column typeZq">
					<div class="list_up_date"><?php echo date("Y-m-d")?></div>
					<div class="list_columnThr_content">
						<div class="no_data"></div>
					</div>
				</div>
				<!--债券型 结束-->
				<!--货币型 结束-->
				<div class="app_list_columnThr drop_content app_tab_content_column typeHb">
					<div class="list_up_date"><?php echo date("Y-m-d")?></div>
					<div class="list_columnThr_content">
						<div class="no_data"></div>
					</div>
				</div>
				<!--货币型 结束-->
				<!--理财型 开始-->
				<div class="app_list_columnThr drop_content app_tab_content_column typeLc">
					<div class="list_up_date"><?php echo date("Y-m-d")?></div>
					<div class="list_columnThr_content">
						<div class="no_data"></div>
					</div>
				</div>
				<!--理财型 结束-->
				<!--保本型 开始-->
				<div class="app_list_columnThr drop_content app_tab_content_column typeBb">
					<div class="list_up_date"><?php echo date("Y-m-d")?></div>
					<div class="list_columnThr_content">
						<div class="no_data"></div>
					</div>
				</div>
				<!--保本型 结束-->
			</div>
			<!--end app_tab_content-->
		</div>
    	<div class="drop_up">
			上拉加载
		</div>
	</div>
    
<!--涨幅排序-->
<div class="app_sort_Warp">
	<div class="app_sort list">
		<ul>
			<li class="cur" data-yield-type="NVDailyGrowthRate">日涨幅&nbsp;</li>
			<li data-yield-type="RRInSingleWeek">近一周&nbsp;</li>
			<li data-yield-type="RRInSingleMonth">近一月&nbsp;</li>
			<li data-yield-type="RRInThreeMonth">近三月&nbsp;</li>
			<li data-yield-type="RRInSixMonth">近六月&nbsp;</li>
			<li data-yield-type="RRInSingleYear">近一年&nbsp;</li>
			<li data-yield-type="RRSinceThisYear">今年来&nbsp;</li>
		</ul>
	</div>
</div>
<!--搜索-->
<div class="app_page seach_page">
	<div class="app_top_fid app_seach_top">
		<div class="app_topbar" style="padding-top: 1px;">
			<div class="seachItem itemWarp">
				<div class="itemInput">
					<form action="" method="get">
						<input type="search" class="textItem seachInput" autofocus="autofocus" value="" placeholder="输入股票代码/全拼/首字母" />
					</form>
				</div>
			</div>
			<div class="app_Rlink"><a href="javascript:void(0);" class="app_seach_text seach_cancel">取消</a></div>
		</div>
	</div>
	<div class="app_seach_content">
		
	</div>
</div>
<!--搜索结束-->
<!--加载状态-->
<!--<div class="loadPop">
	<div class="loadIco"><img src="images/ico_suss.png" alt=""> </div>
	<div class="loading">
		<span></span>
		<span class="cured"></span>
		<span class="curing"></span>
	</div>
	<div class="loadText">加载中</div>
</div>-->
<!--加载状态结束-->
</body>
</html>
<script src="<?php echo \yii\helpers\Url::base()//Yii::getAlias('@web');?>/js/jquery.min.js"></script>
<script src="<?php echo \yii\helpers\Url::base()//Yii::getAlias('@web');?>/js/mine.js"></script>
<script type="application/javascript">
var page = 1;
var fundType,yieldType=$('.app_sort li.cur').attr('data-yield-type');

function dropDown_update(){
	var cFundType = $('.app_tab_nav .appTabItem.curItem').attr('data-fund-type');
	// yieldType = $('.app_sort li.cur').attr('data-yield-type');

	fundType = cFundType;

	getFundList(fundType, yieldType, 1, false);
	page=1;
}

function dropUp_update(){
	var cFundType = $('.app_tab_nav .appTabItem.curItem').attr('data-fund-type');
	// yieldType = $('.app_sort li.cur').attr('data-yield-type');

	if (fundType != cFundType) {
		page = 1;
	}

	fundType = cFundType;

	getFundList(fundType, yieldType, ++page, true);
}


$(document).ready(function(){
    var tm = new Date().getTime();

    var tab = $('.appTabItem').not(".curItem");

    
    // Init ajax async request other tab data
	getFundList('mix', yieldType, 1, false);
    getFundList('index', yieldType, 1, false);
	getFundList('bond', yieldType, 1, false);
	getFundList('currency', yieldType, 1, false);
	getFundList('money', yieldType, 1, false);
	getFundList('breakeven', yieldType, 1, false);

	// Yield type data
    $(".app_sort li").on("click", function(){
    	var cFundType = $('.app_tab_nav .appTabItem.curItem').attr('data-fund-type');
		yieldType = $('.app_sort li.cur').attr('data-yield-type');

		fundType = cFundType;

		// Curreny page sort yield type data
    	// getFundList(fundType, yieldType, 1, false);

    	// All tab for sort yield type data
    	getFundList('stock', yieldType, 1, false);
    	getFundList('mix', yieldType, 1, false);
	    getFundList('index', yieldType, 1, false);
		getFundList('bond', yieldType, 1, false);
		getFundList('currency', yieldType, 1, false);
		getFundList('money', yieldType, 1, false);
		getFundList('breakeven', yieldType, 1, false);

    	page=1;
    });

    // Ssarch request
    $(".seachInput").on("keyup",function(){
    	var s = $(this).val();

    	if (!s) {
    		return;
    	}

		$.ajax({
	        type: 'GET',
	        async: true,
	        url: '<?php echo \yii\helpers\Url::to(['fund-market/search']);?>',
	        data: {"s":s},
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){
	        	$('.app_seach_content').html('');
	        	loadPop(1,'.app_seach_content',"正在加载，请稍后...");
	        },
	        complete: function(XMLHttpRequest, textStatus){
	        	removePop(1,'.app_seach_content');
	        },
	        success: function(rs){
	        	if (rs.error == 0) {
	        		$.each(rs.list, function(i, data){
	                    $('.app_seach_content').append(viewSearch(data));
	                });
	        	}
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
	});


    /*$(".follow").on("click", function(){
    	var innerCode = $(this).attr('data-inner-code');
    	console.log(innerCode);
    	$.ajax({
	        type: 'GET',
	        async: true,
	        url: '',
	        data: {"innerCode":innerCode},
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){
	        	$('.app_seach_content').html('');
	        },
	        complete: function(XMLHttpRequest, textStatus){

	        },
	        success: function(rs){
	        	console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
    });*/
    
});

function getFundList(fundType, yieldType, page, append, callback) {
	var appendDivClass = '';

	if (fundType === 'stock') {
		appendDivClass = '.typeGp';
	}else if(fundType === 'mix') {
		appendDivClass = '.typeHh';
	}else if(fundType === 'index') {
		appendDivClass = '.typeZs';
	}else if(fundType === 'bond') {
		appendDivClass = '.typeZq';
	}else if(fundType === 'currency') {
		appendDivClass = '.typeHb';
	}else if(fundType === 'money') {
		appendDivClass = '.typeLc';
	}else if(fundType === 'breakeven') {
		appendDivClass = '.typeBb';
	}

	
	$.ajax({
        type: 'GET',
        async: true,
        url: '<?php echo \yii\helpers\Url::to(['fund-market/fund-list']);?>',
        data: {"fund_type":fundType,"yield_type":yieldType, "page":page},
        dataType: 'json',
        beforeSend: function(XMLHttpRequest){
        	if (!append) {
        		$(appendDivClass+' .list_columnThr_content').html('');
        	}
        	loadPop(1,appendDivClass+' .list_columnThr_content',"正在加载，请稍后...");
        },
        complete: function(XMLHttpRequest, textStatus){
        	removePop(1,appendDivClass+' .list_columnThr_content');
        	dropOver();
        },
        success: function(rs){
        	if (rs.error == 0) {
        		$.each(rs.list, function(i, data){
		    		$(appendDivClass+' .list_columnThr_content').append(viewlist(data,yieldType));
		        });
        	}
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log(errorThrown);
        }
    });
}

function viewlist(data,yieldType) {
	var val1,val2;

	if (data.FundTypeCode == 1109 || data.FundTypeCode == 1106 || (data.TypeCode == 10 && data.DataCode == 1106)) {
		val1 = data.DailyProfit;
		val2 = data.LatestWeeklyYield;
	}else{
		val1 = data.UnitNV;
		val2 = data[yieldType];
	}
	
	var html = '';
	html += '<a href="<?php echo \yii\helpers\Url::to(['fund/index']);?>?InnerCode='+data.InnerCode+'&val='+val1+'&rate='+val2+'" class="lineLink">'
	html += '<ul class="fundItem clearfix">';
	html += '<li class="col_fund_name w300">';
	html += '<span class="fund_name">'+data.SecuAbbr+'</span>'
	html += '<span class="fund_Num">'+data.SecuCode+'</span></li>'
	html += '<li class="col_fund_Val w140">'+val1+'</li>';
	if (val2 > 0) {
		html += '<li class="col_fund_sort w140 rise">+'+val2+'%</li>';
	}else if (val2 < 0) {
		html += '<li class="col_fund_sort w140 fall">'+val2+'%</li>';
	}else{
		html += '<li class="col_fund_sort w140 flat">'+val2+'%</li>';
	}
	html += '</ul></a>';

	return html;
}

function viewSearch(data) {

	var html = '';
	html += '<ul class="fundItem seachList clearfix">';
	html += '<a href="<?php echo \yii\helpers\Url::to(['fund/index']);?>?InnerCode='+data.InnerCode+'"><li class="col_fund_name w300">';
	html += '<span class="fund_name">'+data.SecuAbbr+'</span>'
	html += '<span class="fund_Num">'+data.SecuCode+'&nbsp;&nbsp;'+data.FundType+'</span>'
	html += '</li><li class="follow" data-inner-code="'+data.InnerCode+'" style="display:none"></li></a></ul>';

	return html;
}
</script>