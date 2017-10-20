<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'statistics']);?>
<section class="mine_section">
	<div class="colT rowSlt wAuto">
		<ul>
			<li>
				<label class="textlabel">渠道</label>
				<div class="itemSelect">
					<select name="" class="asideSelect insitChose">
					<?php foreach($instList as $admin):?>
						<option value="<?= $admin['id'];?>"><?= $admin['InstName'];?></option>
					<?php endforeach;?>
					</select>	
				</div>
			</li>
			<li>
				<!-- <label class="textlabel">总市值＞</label>
				<div class="item labelItem w85">
					<div class="itemText">
						<input type="text" name="" id="" value="" class="textInput assets" />
					</div>
				</div> -->
			</li>
			<li><a href="javascript:void(0);" class="buttonA userSearch">查询</a></li>
		</ul>
	</div>

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
	var url = "<?= \yii\helpers\Url::to(['statistics/position']);?>";
	var data = {};

	/**
	* 初始化用户列表数据
	*/
	filterAjax(url,data);


    /**
	* 自定义用户条件搜索
	*/
	$('.userSearch').on('click', function(){
		instid = $('.insitChose').val();
    	startDate = $('#from').val();
    	endDate = $('#to').val();

		data = {'instid':instid,'startDate':startDate,'endDate':endDate};

		// Ajax处理函数
		filterAjax(url, data);
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
		filterAjax(url, data);
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
		filterAjax(url, data);
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
        		var tradeList = '';
        		$.each(rs.list, function(i, data){
        			tradeList += viewList(data)
                });
                var tradeTable = viewThead()+tradeList;
                $('.tableList .tableDiv').html(tradeTable);

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


function viewThead(data) {
	var html = '';

	html += '<ul class="thead">';
	// html += '<li><a class="sort linkA" href="javascript:void(0);">序号</a></li>';
	html += '<li>姓名</li>';
	html += '<li>手机号</li>';
	html += '<li><a class="sort linkA" href="javascript:void(0);">总市值/元</a></li>';
	html += '<li><a class="sort linkA" href="javascript:void(0);">累计申购额/元</a></li>';
	html += '<li><a class="sort linkA" href="javascript:void(0);">累计赎回额/元</a></li>';
	html += '<li><a class="sort linkA" href="javascript:void(0);">累计盈亏/元</a></li>';
	html += '<li><a class="sort linkA" href="javascript:void(0);">渠道</a></li></ul>';

	return html;
}

function viewList(data) {
	var html = '';
	
	html += '<ul>';
	html += '<li>'+(data['Name']?data['Name']:'-')+'</li>';
	html += '<li>'+(data['BindPhone']?data['BindPhone']:'-')+'</li>';
	html += '<li>'+(data['TotalAssets']?data['TotalAssets']:'-')+'</li>';
	html += '<li>'+(data['TotalApplyAmount']?data['TotalApplyAmount']:'-')+'</li>';
	html += '<li>'+(data['TotalConfirmAmount']?data['TotalConfirmAmount']:'-')+'</li>';
	html += '<li>'+(data['TotalProfitLoss']?data['TotalProfitLoss']:'-')+'</li>';
	html += '<li>'+(data['InstName']?data['InstName']:'-')+'</li></ul>';
	
	return html;
}
</script>