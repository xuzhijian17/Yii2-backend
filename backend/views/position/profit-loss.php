<aside>
	<div class="subNav">
		<a href="javascript:void(0);" class="subLink subCur">每日盈亏</a>
	</div>
</aside>
<section class="mine_section">
	<div class="profit-loss tableList">
		<div class="colT tableDiv">
			
		</div>
		<!--tableDiv end-->
		<div class="pages colT">
			<ul>
				<li class="w20"><a href="javascript:void(0);" class="prev">&#9668</a></li>
				<li class="w30">第</li>
				<li class="w50"><input type="text" id="" value="1" class="pageNum" /></li>
				<li class="w30">页</li>
				<li class="w65">共<span class="totalPages"></span>页</li>
				<li class="w20"><a href="javascript:void(0);" class="next">&#9658</a></li>
			</ul>
		</div>
	</div>
</section>
<script type="application/javascript">
var page;
var totalPages;
var totalRecords;

$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['position/profit-loss']);?>";
	var data = {};

	/**
	* 初始化判断
	*/
	var id = $_GET['id'];	// 持仓id
	var uid = $_GET['uid'];
	var instid = $_GET['instid'];
    if (id && uid && instid) {
    	data = {'id':id,'uid':uid,'instid':instid};

		Ajax(url, data);
    }
	
	/**
	* 上一页
	*/
	$('.prev').on('click', function(){
		var prevPage = Number(page) - 1;

		if (prevPage < 1) {
			return;
		}

		// 请求参数
		data = {'id':id,'uid':uid,'instid':instid,'page':prevPage};

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
		data = {'id':id,'uid':uid,'instid':instid,'page':nextPage};

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
                	var list = '';
	        		$.each(rs.list, function(i, data){
	        			list += viewList(data)
	                });
	        		var tableList = viewThead()+list;
	                $(".tableList .tableDiv").html(tableList);

                	page = rs.page;
	                totalPages = rs.totalPages;
	                totalRecords = rs.totalRecords;
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
	html += '<li class="wb25">日期</li>';
	html += '<li class="wb25">基金名称</li>';
	html += '<li class="wb25">基金代码</li>';
	html += '<li class="wb25">盈亏/元</li></ul>';

	return html;
}

function viewList(data) {
	var html = '';
	
	html += '<ul><li>'+(data['TradeDay']?data['TradeDay']:'-')+'</li>';
	html += '<li>'+(data['FundName']?data['FundName']:'-')+'</li>';
	html += '<li>'+(data['FundCode']?data['FundCode']:'-')+'</li>';
	html += '<li class="'+(data['ProfitLoss']>0?'colU':'colD')+'">'+data['ProfitLoss']+'</li></ul>';
	
	return html;
}
	
</script>