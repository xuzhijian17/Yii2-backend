<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'position','uid'=>$uid,'instid'=>$instid]);?>
<section class="mine_section">
	<div class="channel">
		<label class="textlabel">渠道</label>
		<div class="itemSelect">
			<select name="" class="asideSelect insitChose">
			<?php foreach($instList as $admin):?>
				<option value="<?= $admin['id'];?>"><?= $admin['InstName'];?></option>
			<?php endforeach;?>
			</select>	
		</div>
	</div>
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
	<div class="wrap tableList" style="display: none;">
		<table border="0" cellspacing="1" cellpadding="0" class="table talC">

		</table>
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

	<!-- <table border="0" cellspacing="1" cellpadding="0" class="table talC">
		<tr>
			<td colspan="3">累计扣款：9413.63元</td>
			<td colspan="3">累计盈亏：-586.37元</td>
			<td colspan="3">昨日盈亏：+67.37元</td>
		</tr>
		<tr>
			<td>基金代码</td>
			<td>基金简称</td>
			<td>基金类型</td>
			<td>累计扣款金额</td>
			<td>最新净值</td>
			<td>所持份额/份</td>
			<td>市值/元</td>
			<td>未付收益</td>
			<td>盈亏查询</td>
		</tr>
		<tr>
			<td>590007</td>
			<td>中邮上证380</td>
			<td>股票型</td>
			<td>4000</td>
			<td>1.9930</td>
			<td>1185.63</td>
			<td>2362.96</td>
			<td>0.00</td>
			<td><a href="chicang_details.html" class="linkA" target="_blank">每日盈亏</a></td>
		</tr>
	</table> -->
</section>
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
	var url = "<?= \yii\helpers\Url::to(['position/cast-surely-ajax']);?>";
	var data = {};

	/**
	* 初始化判断
	*/
	var uid = $_GET['uid'];
    if (uid) {
    	data = {'uid':uid};
    }

    // filterAjax(url, data);

    /**
	* 自定义用户条件搜索
	*/
	$('.userSearch').on('click', function(){
		name = $('.name').val();
		phone = $('.phone').val();
		card = $('.card').val();
		instid = $('.insitChose').val();

		if (!name && !phone && !card) {
			return;
		}

		data = {'instid':instid,'name':name,'phone':phone,'card':card};

		filterAjax(url, data);
	});

	/**
	* 上一页（后绑定事件）
	*/
	$('.tableList').on('click', '.prev', function(){
		var prevPage = Number(page) - 1;

		if (prevPage < 1) {
			return;
		}

		data = {'instid':instid,'name':name,'phone':phone,'card':card,'page':prevPage};
		
		tradeAjax(url, data);
	});

	/**
	* 下一页（后绑定事件）
	*/
	$('.tableList').on('click', '.next', function(){
		var nextPage = Number(page) + 1;
		
		if (nextPage > Number(totalPages)) {
			return;
		}

		data = {'instid':instid,'name':name,'phone':phone,'card':card,'page':nextPage};
		
		tradeAjax(url, data);
	});
});


/**
* Ajax处理函数
*/
function filterAjax(url, data) {
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
        		var tableList = '';
        		$.each(rs.list, function(i, data){
        			tableList += viewList(data)
                });
                var table = viewThead()+tableList;
                $('.tableList .table').html(table);

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
        	console.log(rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log('Ajax request error!');
        }
    });
}


function viewThead(data) {
	var html = '';

	html += '<tr><td colspan="3">累计扣款：元</td>';
	html += '<td colspan="3">累计盈亏：元</td>';
	html += '<td colspan="3">昨日盈亏：元</td></tr>';
	html += '<tr><td>基金代码</td>';
	html += '<td>基金简称</td>';
	html += '<td>基金类型</td>';
	html += '<td>累计扣款金额</td>';
	html += '<td>最新净值</td>';
	html += '<td>所持份额/份</td>';
	html += '<td>市值/元</td>';
	html += '<td>未付收益</td>';
	html += '<td>盈亏查询</td></tr>';

	return html;
}

function viewList(data) {
	var html = '';
	
	html += '<ul><li>'+(data['FundCode']?data['FundCode']:'-')+'</li>';
	html += '<li>'+(data['FundName']?data['FundName']:'-')+'</li>';
	html += '<li>'+(data['FundType']?data['FundType']:'-')+'</li>';
	html += '<li>'+(data['TotalBuyConfirmAmount']?data['TotalBuyConfirmAmount']:'-')+'</li>';
	html += '<li>'+(data['PernetValue']?data['PernetValue']:'-')+'</li>';
	html += '<li>'+(data['CurrentRemainShare']?data['CurrentRemainShare']:'-')+'</li>';
	html += '<li>'+(data['MarketValue']?data['MarketValue']:'-')+'</li>';
	html += '<li>'+(data['UnpaidIncome']?data['UnpaidIncome']:'-')+'</li>';
	html += '<li><a href="<?= \yii\helpers\Url::to(['position/profit-loss']);?>?id='+data['id']+'&instid='+data['Instid']+'&uid='+data['Uid']+'" class="linkA" target="_blank">每日盈亏</a></li></ul>';
	
	return html;
}	
</script>