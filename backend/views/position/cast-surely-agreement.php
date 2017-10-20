<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'position','uid'=>$uid,'instid'=>$instid,'type'=>$type]);?>
<section class="mine_section">
	<div class="wrap tableList" style="display: none;">
		<div class="colT tableDiv">

		</div>
		<!-- <div class="pages colT">
			<ul>
				<li class="w20"><a href="javascript:void(0);" class="prev">&#9668</a></li>
				<li class="w30">第</li>
				<li class="w50"><input type="text" id="" value="1" class="pageNum" /></li>
				<li class="w30">页</li>
				<li class="w65">共<span class="totalPages">1</span>页</li>
				<li class="w20"><a href="javascript:void(0);" class="next">&#9658</a></li>
			</ul>
		</div> -->
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
var uid = '<?= $uid;?>';
var instid = '<?= $instid;?>';
var type = '<?= $type;?>';

$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['position/cast-surely-agreement']);?>";
	var data = {};

	/**
	* 初始化判断
	*/
    if (uid && instid && type) {
    	data = {'uid':uid,'instid':instid,'type':type};

    	Ajax(url, data);
    }


	/**
	* 上一页（后绑定事件）
	*/
	$('.tableList').on('click', '.prev', function(){
		var prevPage = Number(page) - 1;

		if (prevPage < 1) {
			return;
		}

		data = {'uid':uid,'instid':instid,'page':prevPage};
		
		Ajax(url, data);
	});

	/**
	* 下一页（后绑定事件）
	*/
	$('.tableList').on('click', '.next', function(){
		var nextPage = Number(page) + 1;
		
		if (nextPage > Number(totalPages)) {
			return;
		}

		data = {'uid':uid,'instid':instid,'page':nextPage};
		
		Ajax(url, data);
	});
});


/**
* Ajax处理函数
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
                if (rs.list.length > 0) {
                	var tableList = '';
	        		$.each(rs.list, function(i, data){
	        			tableList += viewList(data)
	                });
	                var table = viewThead()+tableList;
	                $('.tableList .tableDiv').html(table);

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
        	console.log(rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log('Ajax request error!');
        }
    });
}


function viewThead(data) {
	var html = '';

	html += '<ul class="thead">';
	html += '<li>签订日期</li>';
	html += '<li>协议号</li>';
	html += '<li>基金代码</li>';
	html += '<li>基金简称</li>';
	html += '<li>基金类型</li>';
	html += '<li>申请金额 </li>';
	html += '<li>成功扣款次数 </li>';
	html += '<li>交易日期 </li>';
	html += '<li>协议状态 </li></ul>';

	return html;
}

function viewList(data) {
	var html = '';
	
	html += '<ul><li>'+(data['SysTime']?data['SysTime']:'-')+'</li>';
	html += '<li>'+(data['Xyh']?data['Xyh']:'-')+'</li>';
	html += '<li>'+(data['FundCode']?data['FundCode']:'-')+'</li>';
	html += '<li>'+(data['FundName']?data['FundName']:'-')+'</li>';
	html += '<li>'+(data['FundType']?data['FundType']:'-')+'</li>';
	html += '<li>'+(data['Applysum']?data['Applysum']:'-')+'</li>';
	html += '<li>'+(data['SuccNum']?data['SuccNum']:'-')+'</li>';
	html += '<li>'+(data['Jyrq']?data['Jyrq']:'-')+'号</li>';
	html += '<li>'+(data['StateName']?data['StateName']:'-')+'</li></ul>';
	
	return html;
}	
</script>