<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'statistics']);?>
<section class="mine_section">
	<div class="colT rowSlt wAuto">
		<ul>
			<li>
				<label class="textlabel">渠道</label>
				<div class="itemSelect">
					<select name="" class="asideSelect insitChose">
					<?php foreach($instList as $inst):?>
						<option value="<?= $inst['Instid'];?>"><?= $inst['InstName'];?></option>
					<?php endforeach;?>
					</select>	
				</div>
			</li>
			<li><span class="inLine">起始日期</span><a href="javascript:void(0);" class="icoTime ico"><span id="startTime">————</span><input type="hidden" id="from" name="from" value="" style="width:100%" /></a></li>
			<li><span class="inLine">结束日期</span><a href="javascript:void(0);" class="icoTime ico"><span id="endTime"><?= date("Y-m-d")?></span><input type="hidden" id="to" name="to" value="" style="width:100%" /></a></li>
			<li><a href="javascript:void(0);" class="buttonA userSearch">查询</a></li>
		</ul>
	</div>

	<div class="wrap" style="display: none;">
		<div class="base-statistics">
			<h2 class="bT">总量统计</h2>
			<div class="colT tableDiv">
				<ul class="thead">
					<li>总注册量</li>
					<li>总绑卡量</li>
					<li>累计申购额</li>
					<li>累计赎回额</li>
					<li>累计佣金 </li>
				</ul>
				<ul class="total-statistics">

				</ul>
			</div>
			<!--总量统计-->
		</div>
		
		<div class="tableList statisticsList">
			<h2 class="bT">每日统计</h2>
			<div class="colT tableDiv">
				
			</div>
			<!--每日统计-->
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

$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['statistics/index']);?>";
	var totalStatisticsUrl = "<?= \yii\helpers\Url::to(['statistics/total-statistics']);?>";
	var data = {};

	/**
	* 初始化用户列表数据
	*/
	totalStatisticsAjax(totalStatisticsUrl,data);
	Ajax(url,data);


    /**
	* 开始时间选择
	*/
	$( "#from" ).datepicker({
		showOn: "button",	// 触发日期显示方式：1.fouce(default), 2.button, 3.both
		buttonImage: "<?= \yii\helpers\Url::base();?>/images/calendar.gif",	// 当选择button触发时，可设置一个icon图
		buttonImageOnly: true,	// 设置为true时，为图片按钮（默认为按钮图片），即显示时将只有图片而不会有按钮框
		changeYear: false,	// 日期控件中的年份是否可选
		dateFormat: "yy-mm-dd",	// 格式化日期的显示方式
		maxDate: new Date(),	// 设置最大可选日期
		altField: "#startTime",
		onClose: function( selectedDate ) {		// 设置#from日期控件中的可选范围必需是基于#to中的minDate值
			$( "#to" ).datepicker( "option", "minDate", selectedDate );
		},
		onSelect: function (selectedDate) {		// 选择完日期后的回调处理函数
			startDate = selectedDate;
			$('#startTime').text(selectedDate);
		}
	});
	/**
	* 结束时间选择
	*/
	$( "#to" ).datepicker({
		showOn: "button",	// 触发日期显示方式：1.fouce(default), 2.button, 3.both
		buttonImage: "<?= \yii\helpers\Url::base();?>/images/calendar.gif",	// 当选择button触发时，可设置一个icon图
		buttonImageOnly: true,	// 设置为true时，为图片按钮（默认为按钮图片），即显示时将只有图片而不会有按钮框
		changeYear: false,	// 日期控件中的年份是否可选
		dateFormat: "yy-mm-dd",	// 格式化日期的显示方式
		maxDate: new Date(),	// 设置最大可选日期
		onClose: function( selectedDate ) {		// 设置#to日期控件中的可选范围必需是基于#from中的maxDate值
			$( "#from" ).datepicker( "option", "maxDate", selectedDate );
		},
		onSelect: function (selectedDate) {		// 选择完日期后的回调处理函数
			endDate = selectedDate;
			$('#endTime').text(selectedDate);
		}
	});

    /**
	* 自定义用户条件搜索
	*/
	$('.userSearch').on('click', function(){
		instid = $('.insitChose').val();
    	startDate = $('#from').val();
    	endDate = $('#to').val();

		data = {'instid':instid,'startDate':startDate,'endDate':endDate};

		// Ajax处理函数
		totalStatisticsAjax(totalStatisticsUrl,data);
		Ajax(url, data);
	});

	/**
	* 上一页
	*/
	$('.prev').on('click', function(){
		var prevPage = Number(page) - 1;

		if (prevPage < 1) {
			return;
		}

		data = {'instid':instid,'startDate':startDate,'endDate':endDate,'page':prevPage};

		// Ajax处理函数
		Ajax(url, data);
	});

	/**
	* 下一页
	*/
	$('.next').on('click', function(){
		var nextPage = Number(page) + 1;

		if (nextPage > Number(totalPages)) {
			return;
		}

		data = {'instid':instid,'startDate':startDate,'endDate':endDate,'page':nextPage};

		// Ajax处理函数
		Ajax(url, data);
	});
});

/**
* Ajax数据
*/
function Ajax(url, data) {
	$.ajax({
        type: 'GET',
        async: true,
        url: url,
        data: data,
        dataType: 'json',
        beforeSend: function(XMLHttpRequest){
        },
        complete: function(XMLHttpRequest, textStatus){
        	$('.pageNum').val(page);
        	$('.totalPages').text(totalPages);
        	$('.wrap').show();
        },
        success: function(rs){
        	if (rs.error == 0) {
        		// 每日统计
        		var tradeList = '';
        		$.each(rs.list, function(i, data){
        			tradeList += viewList(data)
                });
                var tradeTable = viewThead()+tradeList;
                $('.statisticsList .tableDiv').html(tradeTable);

                // 重置分页数据
                if (rs.list.length > 0) {
                	page = rs.page;
	                totalPages = rs.totalPages;
	                totalRecords = rs.totalRecords;
                }else{
                	page = 1;
	                totalPages = 1;
	                totalRecords = 1;
                }
        	}
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log('Ajax request error!');
        }
    });
}

function totalStatisticsAjax(url, data) {
	$.ajax({
        type: 'GET',
        async: true,
        url: url,
        data: data,
        dataType: 'json',
        beforeSend: function(XMLHttpRequest){
        },
        complete: function(XMLHttpRequest, textStatus){
        },
        success: function(rs){
        	if (rs.error == 0) {
        		// 总量统计
        		var sumStatisticsTable = viewTotalStatistics(rs);
        		$('.total-statistics').html(sumStatisticsTable);
        	}
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log('Ajax request error!');
        }
    });
}


function viewTotalStatistics(data) {
	var html = '';

	html += '<li>'+(data.sumRegNums=="undefined"?'-':data.sumRegNums)+'</li>';
	html += '<li>'+(data.sumBindNums=="undefined"?'-':data.sumBindNums)+'</li>';
	html += '<li>'+(data.sumTotalBuyAmount=="undefined"?'-':data.sumTotalBuyAmount)+'</li>';
	html += '<li>'+(data.sumTotalSellAmount=="undefined"?'-':data.sumTotalSellAmount)+'</li>';
	html += '<li>'+(data.sumCommission=="undefined"?'-':data.sumCommission)+'</li>';

	return html;
}

function viewThead(data) {
	var html = '';

	html += '<ul class="thead">';
	html += '<li>日期</li>';
	html += '<li>注册量/人</li>';
	html += '<li>绑卡量/人</li>';
	html += '<li>累计申购额/元</li>';
	html += '<li>累计赎回额/元</li>';
	html += '<li>申购中/元 </li>';
	html += '<li>赎回中/元 </li>';
	html += '<li>日保有量/元 </li>';
	html += '<li>佣金 </li></ul>';

	return html;
}

function viewList(data) {
	var html = '';
	
	html += '<ul><li>'+(data['Day']?data['Day']:'-')+'</li>';
	html += '<li>'+(data['RegNums']?data['RegNums']:'-')+'</li>';
	html += '<li>'+(data['BindNums']?data['BindNums']:'-')+'</li>';
	html += '<li>'+(data['TotalBuyAmount']?data['TotalBuyAmount']:'-')+'</li>';
	html += '<li>'+(data['TotalSellAmount']?data['TotalSellAmount']:'-')+'</li>';
	html += '<li>'+(data['InBuyAmount']?data['InBuyAmount']:'-')+'</li>';
	html += '<li>'+(data['InSellAmount']?data['InSellAmount']:'-')+'</li>';
	html += '<li>'+(data['TotalAsset']?data['TotalAsset']:'-')+'</li>';
	html += '<li>'+(data['Commission']?data['Commission']:'-')+'</li></ul>';
	
	return html;
}
	
</script>