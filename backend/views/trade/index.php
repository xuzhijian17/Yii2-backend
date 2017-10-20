<aside>
	<div class="colT asideTab">
		<ul>
			<li>渠道</li>
			<li>
				<div class="itemSelect">
					<select name="" class="asideSelect insitChose">
					<?php foreach($instList as $inst):?>
						<?php if($inst['Instid']=='1000'){continue;}?>
						<option value="<?= $inst['Instid'];?>"><?= $inst['InstName'];?></option>
					<?php endforeach;?>
					</select>	
				</div>
			</li>
		</ul>
		<ul>
			<li>操作类型</li>
			<li>
				<div class="itemSelect">
					<select name="" class="asideSelect tradeType">
						<option value="">全部</option>
						<option value="0">申购</option>
						<option value="1">赎回</option>
						<?php if($type == 0):?>
							<option value="2">撤单</option>
							<option value="3">定投</option>
						<?php endif;?>
					</select>	
				</div>
			</li>
		</ul>
		<ul>
			<li>操作状态</li>
			<li>
				<div class="itemSelect">
					<select name="" class="asideSelect tradeStatus">
						<option value="">全部</option>
						<option value="9">申购中</option>
						<option value="9">赎回中</option>
						<option value="1">成功</option>
						<option value="0">失败</option>
						<option value="4">撤单</option>
					</select>	
				</div>
			</li>
		</ul>
		<ul>
			<li>交易时间</li>
			<li><a href="javascript:void(0);" class="icoTime ico"><span id="startTime">————</span><input type="hidden" id="from" name="from" value="" style="width:100%" /></a></li>
		</ul>
		<ul>
			<li>结束时间</li>
			<li><a href="javascript:void(0);" class="icoTime ico"><span id="endTime"><?= date("Y-m-d")?></span><input type="hidden" id="to" name="to" value="" style="width:100%" /></a></li>
		</ul>
	</div>
</aside>
<section class="mine_section">
	<div class="colT query">
		<ul>
			<li>
				<label class="textlabel">姓名</label>
				<div class="item labelItem">
					<div class="itemText">
						<input type="text" name="" id="" value="" class="textInput name" />
					</div>
				</div>
			</li>
			<li>
				<label class="textlabel">注册号码</label>
				<div class="item labelItem">
					<div class="itemText">
						<input type="text" name="" id="" value="" class="textInput phone" />
					</div>
				</div>
			</li>
			<li>
				<label class="textlabel">身份证号</label>
				<div class="item labelItem">
					<div class="itemText">
						<input type="text" name="" id="" value="" class="textInput card" />
					</div>
				</div>
			</li>
			<li><a href="javascript:void(0);" class="buttonA userSearch">查询</a></li>
		</ul>
	</div>
	<!--query end--> 
	<div class="tableList wrap" style="display: none;">
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
var type = '<?= $type?>';

$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['trade/index']);?>";
	var data = {};

	/**
	* 初始化用户列表数据
	*/
	Ajax(url,data);

	/**
	* 渠道刷选
	*/
    $('.insitChose').on('change', function(){
    	instid = $(this).val();
    	startDate = $('#from').val();
    	endDate = $('#to').val();

    	$('.name').val('');
		$('.phone').val('');
		$('.card').val('');
		name = '';
		phone = '';
		card = '';

    	// 请求参数
		data = {'instid':instid,'tradeType':tradeType,'tradeStatus':tradeStatus,'startDate':startDate,'endDate':endDate};

    	// Ajax处理函数
		Ajax(url,data);
	});

	/**
	* 操作类型刷选
	*/
    $('.tradeType').on('change', function(){
    	tradeType = $(this).val();
    	startDate = $('#from').val();
    	endDate = $('#to').val();

    	$('.name').val('');
		$('.phone').val('');
		$('.card').val('');
		name = '';
		phone = '';
		card = '';

    	// 请求参数
		data = {'instid':instid,'tradeType':tradeType,'tradeStatus':tradeStatus,'startDate':startDate,'endDate':endDate};

		// Ajax处理函数
		Ajax(url,data);
	});

    /**
	* 操作状态刷选
	*/
    $('.tradeStatus').on('change', function(){
    	tradeStatus = $(this).val();
    	startDate = $('#from').val();
    	endDate = $('#to').val();

    	name = $('.name').val('');
		$('.phone').val('');
		$('.card').val('');
		name = '';
		phone = '';
		card = '';

    	// 请求参数
		data = {'instid':instid,'tradeType':tradeType,'tradeStatus':tradeStatus,'startDate':startDate,'endDate':endDate};

    	// Ajax处理函数
		Ajax(url,data);
	});

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
			// 请求参数
			data = {'instid':instid,'tradeType':tradeType,'tradeStatus':tradeStatus,'startDate':startDate,'endDate':endDate};

			Ajax(url,data);
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
			// 请求参数
			data = {'instid':instid,'tradeType':tradeType,'tradeStatus':tradeStatus,'startDate':startDate,'endDate':endDate};

			Ajax(url,data);
		}
	});

    /**
	* 自定义用户条件搜索
	*/
	$('.userSearch').on('click', function(){
		name = $('.name').val();
		phone = $('.phone').val();
		card = $('.card').val();

		if (!name && !phone && !card) {
			return;
		}

		// 请求参数
		data = {'instid':instid,'tradeType':tradeType,'tradeStatus':tradeStatus,'startDate':startDate,'endDate':endDate,'name':name,'phone':phone,'card':card};

		// Ajax处理函数
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

		// 请求参数
		data = {'instid':instid,'tradeType':tradeType,'tradeStatus':tradeStatus,'startDate':startDate,'endDate':endDate,'name':name,'phone':phone,'card':card,'page':prevPage};

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

		// 请求参数
		data = {'instid':instid,'tradeType':tradeType,'tradeStatus':tradeStatus,'startDate':startDate,'endDate':endDate,'name':name,'phone':phone,'card':card,'page':nextPage};

		// Ajax处理函数
		Ajax(url, data);
	});
});

/**
* Ajax处理函数
*/
function Ajax(url, data) {
	var spinner = new Spinner().spin();

	data['type'] = type;	// 交易类型
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
	
	html += '<ul><li>'+(data['ApplyTime']?data['ApplyTime'].replace(" ","<br>"):'-')+'</li>';
	html += '<li><a href="<?= \yii\helpers\Url::to(['user/user-bank']);?>?uid='+data['Uid']+'&instid='+data['Instid']+'" class="linkA" target="_blank">'+BankAddrName+'|'+BankAcco+'</a></li>';
	html += '<li><a href="<?= \yii\helpers\Url::to(['position/index']);?>?uid='+data['Uid']+'&instid='+data['Instid']+'&type='+type+'" class="linkA" target="_blank">'+(data['Name']?data['Name']:'-')+'</a></li>';
	html += '<li>'+(data['BindPhone']?data['BindPhone']:'-')+'</li>';
	html += '<li>'+(data['FundName']?data['FundName']:'-')+'</li>';
	html += '<li>'+(data['FundCode']?data['FundCode']:'-')+'</li>';
	html += '<li>'+data['ApplyAmount']+'</li>';
	html += '<li>'+data['ApplyShare']+'</li>';
	html += '<li>'+(data['TradeTypeName']?data['TradeTypeName']:'-')+'</li>';
	html += '<li>'+(data['TradeStatusName']?data['TradeStatusName']:'-')+'</li>';
	html += '<li>'+data['TradeTypeName']+'申请<br />提交成功</li>';
	html += '<li>'+(data['InstName']?data['InstName']:'-')+'</li></ul>';
	
	return html;
}
	
</script>
