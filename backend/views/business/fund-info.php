<?php
echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);
?>
<section class="mine_section">
	<div class="colT rowSlt wAuto mB0">
		<ul>
			<li>
				<div class="item labelItem">
					<div class="itemText">
						<input type="text" class="textInput tgnC" value="" id="fundCode" name="" placeholder="请输入基金代码">
					</div>
				</div>
			</li>
			<li style="padding-right: 50px;"><a href="javascript:void(0);" class="buttonA search">查询</a></li>
			<li>
				<label class="textlabel">尾随佣金</label>
				<div class="itemSelect h38">
					<select name="" class="asideSelect CustodyFee">
						<option value="0">全部</option>
						<option value="1">已维护</option>
						<option value="2">未维护</option>
					</select>	
				</div>
			</li>
		</ul>
	</div>	
	<div class="crumbs">
		<span class="inLine pR40 fundNums">全部基金数：<?= $StatisticalData['totalRecords'];?>只</span>
		<span class="inLine pR40 hasMaintainCustodyFeeNums">尾佣维护数：<?= $StatisticalData['hasMaintainCustodyFeeNums'];?>只</span>
		<span class="inLine pR40 notMaintainCustodyFeeNums">尾佣未维护数：<?= $StatisticalData['notMaintainCustodyFeeNums'];?>只</span>
	</div>
	<div class="wrap tableList">
		<div class="colT tableDiv">

		</div>
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
<link rel="stylesheet" href="<?= \yii\helpers\Url::base();?>/css/jquery-ui.min.css">
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.core.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.widget.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.position.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.menu.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.autocomplete.min.js"></script>
<script type="application/javascript">
var instid;
var CustodyFee;
var fundCode;
var startDate;
var endDate;
var name;
var phone;
var card;
var page;
var totalPages;
var totalRecords;
var availableTags;

$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['business/fund-info']);?>";
	var data = {};

	/**
	* 初始化用户列表数据
	*/
	Ajax(url,data);

	/**
	* 尾佣刷选
	*/
    $('.CustodyFee').on('change', function(){
    	CustodyFee = $(this).val();

    	// 重置数据，防止分页附加
		fundCode = null;

    	// 请求参数
		data = {'CustodyFee':CustodyFee};
		
    	// Ajax处理函数
		Ajax(url,data);
	});

    /**
	* 搜索基金
	*/
	$('.search').on('click', function(){
		fundCode = $('#fundCode').val();

		if (!fundCode) {
			return;
		}

		// 重置尾佣选择
		$(".CustodyFee option:first").prop("selected", 'selected');

		// 重置数据，防止分页附加
		CustodyFee = null;

		// 请求参数
		data = {'FundCode':fundCode};

		Ajax(url,data);
	});


	/**
	* 上一页
	*/
	$('.tableList').on('click', '.prev', function(){
		var prevPage = Number(page) - 1;

		if (prevPage < 1) {
			return;
		}
		// 请求参数
		data = {'fundCode':fundCode,'CustodyFee':CustodyFee,'page':prevPage};

		// Ajax处理函数
		Ajax(url, data);
	});

	/**
	* 下一页
	*/
	$('.tableList').on('click', '.next', function(){
		var nextPage = Number(page) + 1;

		if (nextPage > Number(totalPages)) {
			return;
		}
		// 请求参数
		data = {'fundCode':fundCode,'CustodyFee':CustodyFee,'page':nextPage};

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
                	// 生成列表
	        		var list = '';
	        		$.each(rs.list, function(i, data){
	        			list += viewList(data)
	                });
	                var tableList = viewThead()+list;
	                $('.tableList .tableDiv').html(tableList);

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
            console.log(errorThrown);
        }
    });
}


function viewThead(data) {
	var html = '';

	// 1-主题 2-类型 3-热销 空或0为默认
	html += '<ul class="thead">';
	html += '<li>基金名称</li>';
	html += '<li>基金代码</li>';
	html += '<li>尾随佣金 </li>';
	html += '<li>操作 </li></ul>';

	return html;
}

function viewList(data) {
	var html = '';
	
	html += '<ul><li>'+(data['FundName']?data['FundName']:'-')+'</li>';
	html += '<li>'+(data['FundCode']?data['FundCode']:'-')+'</li>';
	html += '<li>'+(data['CustodyFeeName']?data['CustodyFeeName']:'-')+'</li>';
	html += '<li><a class="linkA inLine edit" href="<?= \yii\helpers\Url::to(['business/edit-fund-info']);?>?fundCode='+data['FundCode']+'">编辑</a></li></ul>';
	
	return html;
}
</script>