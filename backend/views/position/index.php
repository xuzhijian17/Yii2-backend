<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'position','uid'=>$uid,'instid'=>$instid,'type'=>$type]);?>
<section class="mine_section">	
	<div class="wrap" style="display: none;">
		<!--资产信息-->
		<div class="asset-info">
			<h2 class="bT">资产信息</h2>
			<div class="tableDiv">
				<div class="colT merge tb1">
					<ul>
						<li>总市值：<span id="TotalMarketValue">0</span>元</li>
						<li>累计申购额：<span id="TotalBuyConfirmAmount">0</span>元</li>
						<li>累计赎回额：<span id="TotalSellConfirmAmount">0</span>元</li>
					</ul>
				</div>
				<div class="colT tb2">
					<ul>
						<li>申购中：<span id="TotalBuyApplyAmount">0</span>元</li>
						<li>赎回中：<span id="TotalSellApplyAmount">0</span>份</li>
						<li>历史累计盈亏：<span id="TotalProfitLoss">0</span>元</li>
						<li>昨日盈亏：<span id="TotalDayProfitLoss">0</span>元</li>
						<li>未付收益：<span id="TotalUnpaidIncome">0</span>元</li>
					</ul>
				</div>
			</div>
		</div>
			
		<!--持仓详情-->
		<div class="position-info">
			<h2 class="bT">持仓详情</h2>
			<div class="colT tableDiv">

			</div>
		</div>
		
		<!--交易记录-->
		<div class="tableList wrapTrade">
			<h2 class="bT">交易记录</h2>
			<div class="colT tableDiv">

			</div>
			<div class="pages colT">
				<ul>
					<li class="w20"><a href="javascript:void(0);" class="prev">&#9668</a></li>
					<li class="w30">第</li>
					<li class="w50"><input type="text" id="" value="1" class="pageNum" /></li>
					<li class="w30">页</li>
					<li class="w65">共<span class="totalPages">1</span>页</li>
					<li class="w20"><a href="javascript:void(0);" class="next">&#9658</a></li>
				</ul>
			</div>
		</div>
	</div>
</section>
<link rel="stylesheet" href="<?= \yii\helpers\Url::base();?>/css/jquery-ui.min.css">
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.core.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.widget.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.datepicker.min.js"></script>
<script type="application/javascript">
var instid;
var tradeType;
var tradeStatus;
var startDate;
var endDate;
var name;
var phone;
var card;
var page;
var totalPages;
var totalRecords;
var uid = '<?= $uid;?>';
var instid = '<?= $instid;?>';
var type = '<?= $type;?>';

$(document).ready(function(){
	var url1 = "<?= \yii\helpers\Url::to(['position/index']);?>";
	var url2 = "<?= \yii\helpers\Url::to(['trade/index']);?>";
	var data = {};

	/**
	* 初始化判断
	*/
    if (uid && instid) {
    	data = {'uid':uid,'instid':instid,'type':type};

		// 持仓详情
		positionAjax(url1, data);

		// 交易记录
		tradeAjax(url2, data);
    }


	/**
	* 上一页（后绑定事件）
	*/
	$('.pages').on('click', '.prev', function(){
		var prevPage = Number(page) - 1;

		if (prevPage < 1) {
			return;
		}

		data = {'uid':uid,'instid':instid,'name':name,'phone':phone,'card':card,'page':prevPage};
		// 只对交易记录进行分页
		tradeAjax(url2, data);
	});

	/**
	* 下一页（后绑定事件）
	*/
	$('.pages').on('click', '.next', function(){
		var nextPage = Number(page) + 1;
		
		if (nextPage > Number(totalPages)) {
			return;
		}

		data = {'uid':uid,'instid':instid,'name':name,'phone':phone,'card':card,'page':nextPage};
		// 只对交易记录进行分页
		tradeAjax(url2, data);
	});
});

/**
* Ajax处理函数（持仓详情+资产信息）
*/
function positionAjax(url, data) {
	$.ajax({
        type: 'GET',
        async: true,
        url: url,
        data: data,
        dataType: 'json',
        beforeSend: function(XMLHttpRequest){
        },
        complete: function(XMLHttpRequest, textStatus){
        	$('.wrap').show();
        },
        success: function(rs){
        	if (rs.error == 0) {        		
        		if (rs.list.length > 0) {
        			// 持仓详情
        			var positionList = '';
	        		$.each(rs.list, function(i, data){
	        			positionList += viewPositionList(data)
	                });
	                var positionTable = viewPosition()+positionList;
	                $('.position-info .tableDiv').html(positionTable);
        		}else{
        			$('.position-info .tableDiv').html(viewEmpty());
        		}

        		// 资产信息
                $('#TotalMarketValue').text(rs.TotalMarketValue);
                $('#TotalBuyConfirmAmount').text(rs.TotalBuyConfirmAmount);
                $('#TotalSellConfirmAmount').text(rs.TotalSellConfirmAmount);
                $('#TotalBuyApplyAmount').text(rs.TotalBuyApplyAmount);
                $('#TotalSellApplyAmount').text(rs.TotalSellApplyAmount);
                $('#TotalProfitLoss').text(rs.TotalProfitLoss);
                $('#TotalDayProfitLoss').text(rs.TotalDayProfitLoss);
                $('#TotalUnpaidIncome').text(rs.TotalUnpaidIncome);
        	}
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log('Ajax request error!');
        }
    });
}

/**
* Ajax处理函数（持仓交易记录）
*/
function tradeAjax(url, data) {
	var spinner = new Spinner().spin();

	data['pageSize'] = 5;
	$.ajax({
        type: 'GET',
        async: true,
        url: url,
        data: data,
        dataType: 'json',
        beforeSend: function(XMLHttpRequest){
        	$('.tableList').get(0).appendChild(spinner.el);
        },
        complete: function(XMLHttpRequest, textStatus){
        	$('.pageNum').val(page);
        	$('.totalPages').text(totalPages);
        	$('.wrap').show();
        	spinner.stop();
        },
        success: function(rs){
        	if (rs.error == 0) {
                if (rs.list.length > 0) {
                	var tradeList = '';
	        		$.each(rs.list, function(i, data){
	        			tradeList += viewList(data)
	                });
	                var tradeTable = viewThead()+tradeList;
	                $('.tableList .tableDiv').html(tradeTable);

                	page = rs.page;
	                totalPages = rs.totalPages;
	                totalRecords = rs.totalRecords;
	                $('.tableList .pages').show();
                }else{
                	// 重置分页数据
                	page = 1;
	                totalPages = 1;
	                totalRecords = 1;
	                
	                $('.tableList .tableDiv').html(viewEmpty());
	                $('.tableList .pages').hide();
                }
        	}
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log('Ajax request error!');
        }
    });
}

function viewPosition(data) {
	var html = '';

	html += '<ul class="thead">';
	html += '<li>基金代码</li>';
	html += '<li>基金简称</li>';
	html += '<li>基金类型</li>';
	html += '<li>最新净值</li>';
	html += '<li>所持份额/份</li>';
	html += '<li>市值/元 </li>';
	html += '<li>可赎份额 </li>';
	html += '<li>昨日盈亏 </li>';
	html += '<li>累计盈亏 </li>';
	html += '<li>未付收益 </li>';
	html += '<li>申购中 </li>';
	html += '<li>赎回中 </li>';
	html += '<li>盈亏查询 </li></ul>';

	return html;
}

function viewPositionList(data) {
	var html = '';

	html += '<ul><li>'+(data['FundCode']?data['FundCode']:'-')+'</li>';
	html += '<li>'+(data['FundName']?data['FundName']:'-')+'</li>';
	html += '<li>'+(data['FundType']?data['FundType']:'-')+'</li>';
	html += '<li>'+data['UnitNV']+'</li>';
	html += '<li>'+data['CurrentRemainShare']+'</li>';
	html += '<li>'+data['MarketValue']+'</li>';
	html += '<li>'+data['RedeemableShare']+'</li>';
	html += '<li>'+data['DayProfitLoss']+'</li>';
	html += '<li>'+data['TotalProfitLoss']+'</li>';
	html += '<li>'+data['UnpaidIncome']+'</li>';
	html += '<li>'+data['ApplyAmount']+'元</li>';
	html += '<li>'+data['FreezeSellAmount']+'元</li>';
	html += '<li><a href="<?= \yii\helpers\Url::to(['position/profit-loss']);?>?id='+data['id']+'&uid='+data['Uid']+'&instid='+data['Instid']+'" class="linkA" target="_blank">每日盈亏</a></li></ul>';

	return html;
}

function viewThead(data) {
	var html = '';

	html += '<ul class="thead">';
	html += '<li>交易时间</li>';
	html += '<li>银行|尾号</li>';
	html += '<li>姓名</li>';
	html += '<li>手机号</li>';
	html += '<li>基金简称</li>';
	html += '<li>基金代码 </li>';
	html += '<li>投资金额 </li>';
	html += '<li>份额 </li>';
	html += '<li>操作类型 </li>';
	html += '<li>操作状态 </li>';
	html += '<li>状态说明 </li>';
	html += '<li>渠道 </li></ul>';

	return html;
}

function viewList(data) {
	var html = '';

	var BankAddrName = data['BankName']?data['BankName']:'-';
	var BankAcco = data.BankAcco?data.BankAcco.slice(-4):'-';
	
	html += '<ul><li>'+(data['ApplyTime']?data['ApplyTime']:'-')+'</li>';
	html += '<li>'+BankAddrName+'|'+BankAcco+'</li>';
	html += '<li>'+(data['Name']?data['Name']:'-')+'</li>';
	html += '<li>'+(data['BindPhone']?data['BindPhone']:'-')+'</li>';
	html += '<li>'+(data['FundName']?data['FundName']:'-')+'</li>';
	html += '<li>'+(data['FundCode']?data['FundCode']:'-')+'</li>';
	html += '<li>'+(data['ApplyAmount']?data['ApplyAmount']:'-')+'</li>';
	html += '<li>'+(data['ApplyShare']?data['ApplyShare']:'-')+'</li>';
	html += '<li>'+(data['TradeTypeName']?data['TradeTypeName']:'-')+'</li>';
	html += '<li>'+(data['TradeStatusName']?data['TradeStatusName']:'-')+'</li>';
	html += '<li>'+(data['TradeTypeName']?data['TradeTypeName']:'-')+'申请<br />提交成功</li>';
	html += '<li>'+(data['InstName']?data['InstName']:'-')+'</li></ul>';
	
	return html;
}

function paging(data) {
	var html = '';
	
	html += '<div class="pages colT">';
	html += '<ul><li class="w20"><a href="javascript:void(0);" class="prev">&#9668</a></li>';
	html += '<li class="w30">第</li>';
	html += '<li class="w50"><input type="text" id="" value="1" class="pageNum" /></li>';
	html += '<li class="w30">页</li>';
	html += '<li class="w65">共<span class="totalPages">'+data.totalPages+'</span>页</li>';
	html += '<li class="w20"><a href="javascript:void(0);" class="next">&#9658</a></li></ul></div>';
				
	return html;
}	
</script>