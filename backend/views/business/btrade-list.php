<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<div class="colT rowSlt wAuto">
		<ul>
			<li>
				<label class="textlabel">交易账户</label>
				<div class="item labelItem">
					<div class="itemText">
						<input type="text" name="tradeAcco" id="tradeAcco" value="" class="textInput tradeAcco" style="width: 155px" />
					</div>
				</div>
			</li>
			<li>
				<label class="textlabel">操作类型</label>
				<div class="itemSelect">
					<select name="tradeType" class="asideSelect tradeType">
						<option value="">全部</option>
						<option value="0">申购</option>
						<option value="1">赎回</option>
						<option value="2">撤单</option>
					</select>
				</div>
			</li>
			<li><span class="inLine">起始日期</span><a href="javascript:void(0);" class="icoTime ico"><span id="startTime">————</span><input type="hidden" id="from" name="from" value="" style="width:100%" /></a></li>
			<li><span class="inLine">结束日期</span><a href="javascript:void(0);" class="icoTime ico"><span id="endTime"><?= date("Y-m-d")?></span><input type="hidden" id="to" name="to" value="" style="width:100%" /></a></li>
			<li><a href="javascript:void(0);" class="buttonA btradeSearch">查询</a></li>
		</ul>
	</div>
	<div class="tableList wrap" style="display: none;">
		<div class="colT tableDiv opss">
			<ul class="thead">
				<li>申请日期</li>
				<li>交易账号</li>
				<li>基金代码</li>
				<li>基金名称</li>
				<li>申请金额</li>
				<li>申请份额</li>
				<li>操作类型</li>
				<li>汇款状态</li>
				<li>交易状态</li>
				<li>操作</li>
			</ul>
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
</section>
<link rel="stylesheet" href="<?= \yii\helpers\Url::base();?>/css/jquery-ui.min.css">
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.core.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.widget.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.datepicker.min.js"></script>
<script type="application/javascript">
var tradeAcco;
var tradeType;
var startDate;
var endDate;
var page;
var totalPages;
var totalRecords;

$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['business/trade-remit']);?>";
	var data = {};

	/**
	* 初始化用户列表数据
	*/
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
	$('.btradeSearch').on('click', function(){
		tradeAcco = $('.tradeAcco').val();
		tradeType = $('.tradeType').val();
		startDate = $('#from').val();
    	endDate = $('#to').val();

		// 请求参数
		data = {'tradeAcco':tradeAcco,'tradeType':tradeType,'startDate':startDate,'endDate':endDate};

		// Ajax处理函数
		Ajax(url, data);
	});

	/**
	* 确认凭证上传
	*/
	$('.tableList').on('click', '.ensureRemit', function(){
		id = $(this).attr("data-id");

		if (!id || $(this).text()==='已确认' || !window.confirm("是否确认上传凭证？")) {
			return;
		}

		$.ajax({
	        type: 'POST',
	        async: true,
	        url: "<?= \yii\helpers\Url::to(['business/ensure-remit']);?>",
	        data: {'id':id},
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){
	        },
	        complete: function(XMLHttpRequest, textStatus){
	        },
	        success: function(rs){
	        	if (rs.error == 0) {
	        		window.location.reload(true);
	        	}else{
	        		window.alert(rs.message);
	        	}
	        }.bind(this),
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
	});

	/**
	* 上一页
	*/
	$('.pages').on('click', '.prev', function(){
		var prevPage = Number(page) - 1;

		if (prevPage < 1) {
			return;
		}

		// 请求参数
		data = {'tradeAcco':tradeAcco,'tradeType':tradeType,'startDate':startDate,'endDate':endDate,'page':prevPage};

		// Ajax处理函数
		Ajax(url, data);
	});

	/**
	* 下一页
	*/
	$('.pages').on('click', '.next', function(){
		var nextPage = Number(page) + 1;

		if (nextPage > Number(totalPages)) {
			return;
		}

		// 请求参数
		data = {'tradeAcco':tradeAcco,'tradeType':tradeType,'startDate':startDate,'endDate':endDate,'page':nextPage};

		// Ajax处理函数
		Ajax(url, data);
	});
});

/**
* Ajax处理函数
*/
function Ajax(url, data) {
	var spinner = new Spinner().spin();

	$.ajax({
        type: 'POST',
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
	                $('.tableList .tableDiv>ul.tbody').remove();
	                $('.tableList .tableDiv>ul.thead').after(tradeList);

                	page = rs.page;
	                totalPages = rs.totalPages;
	                totalRecords = rs.totalRecords;
	                $('.tableList .pages').show();
                }else{
                	// 重置分页数据
                	page = 1;
	                totalPages = 1;
	                totalRecords = 1;
	                
	                $('.tableList .tableDiv>ul.tbody').remove();
	                $('.tableList .pages').hide();
                }
        	}
        	console.log(rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log('Ajax request error!');
        }
    });
}


function viewList(data) {
	var html = '';

	html += '<ul class="tbody"><li>'+data.ApplyTime.replace(" ","<br>")+'</li>';
	html += '<li>'+data.TradeAcco+'</li>';
	html += '<li>'+data.FundCode+'</li>';
	html += '<li>'+data.FundName+'</li>';
	html += '<li>'+data.ApplyAmount+'</li>';
	html += '<li>'+data.ApplyShare+'份</li>';
	html += '<li>'+data.TradeTypeName+'</li>';
	html += '<li>'+data.DeductMoneyName+'</li>';
	html += '<li>'+data.TradeStatusName+'</li>';
	html += '<li>';
	if (data.TradeType == '0') {
		if (data.TradeStatusName === '失败' || data.TradeStatusName === '撤单') {
			html += '<a class="linkA blk noClick" href="javascript:void(0);">上传凭证</a>';
			html += '<a class="linkA blk noClick" href="javascript:void(0);">确认凭证</a>';
		}else{
			html += '<a class="linkA blk '+(data.DeductMoney=='2'?'noClick':'')+'" href="'+(data.DeductMoney=='2'?'javascript:void(0);':'<?= \yii\helpers\Url::to(['business/upload-remit']);?>?id='+data.id)+'">'+(data.Pic?'已上传':'上传凭证')+'</a>';
			html += '<a class="linkA blk ensureRemit '+(data.DeductMoney=='2'?'noClick':'')+'" data-id="'+data.id+'" href="javascript:void(0);">'+(data.DeductMoney=='2'?'已确认':'确认凭证')+'</a>';
		}
	}
	html += '<a class="linkA blk" href="<?= \yii\helpers\Url::to(['business/detail-remit']);?>?id='+data.id+'">详情</a>';
	html += '</li></ul>';

	return html;
}
	
</script>
