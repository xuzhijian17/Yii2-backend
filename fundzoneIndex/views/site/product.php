<?php
use yii\helpers\Url;
?>
<div id="app">
	<section class="maxW bgF">
		<div class="navType">
			<dl class="fixed fundtype">
				<dt>基金类型:</dt>
				<dd><a href="javascript:void(0);" class="typeLink cur">全部</a></dd>
				<dd><a href="javascript:void(0);" class="typeLink">货币型</a></dd>
				<dd><a href="javascript:void(0);" class="typeLink">理财型</a></dd>
				<dd><a href="javascript:void(0);" class="typeLink">债券型</a></dd>
				<dd><a href="javascript:void(0);" class="typeLink">混合型</a></dd>
				<dd><a href="javascript:void(0);" class="typeLink">股票型</a></dd>
				<dd><a href="javascript:void(0);" class="typeLink">QDII</a></dd>
				<?php
					/*foreach($fundType as $key=>$value){
						if ($value=='短期理财债券型') {
							$value = '理财型';
						}
						echo '<dd><a href="javascript:void(0);" class="typeLink">'.$value.'</a></dd>';
						
					}*/
				?>
			</dl>		
			<dl class="fixed company">
				<dt>基金公司:</dt>
				<dd><a href="javascript:void(0);" class="typeLink2 cur">全部</a></dd>
				<?php
					foreach($fundCompany as $key=>$value){
						echo '<dd><a href="javascript:void(0);" class="typeLink2">'.$value.'</a></dd>';
						
					}
				?>
			</dl>
			<dl class="fixed searchItem">
				<dt>搜索基金:</dt>
				<dd><input type="text" class="search" name="" id="searchcode" value="" placeholder="输入基金名称/代码" /></dd>
			</dl>
		</div>
		<!--block_section end-->
	</section>
	<section class="maxW bgF">
		<table border="0" cellspacing="0" cellpadding="0" class="table tableList fundList">
			<thead class="tableHeader">
				<tr>
					<th></th>
					<th  class="talL"><span>基金代码</span></th>
					<th><span class="sort dynamicField1" data-val="PernetValue">净值</span></th>
					<th><span class="sort dynamicField2" data-val="NVDailyGrowthRate">日涨幅</span></th>
					<th><span class="sort" data-val="RRInSelectedMonth">月涨幅</span></th>
					<th><span class="sort" data-val="RRInThreeMonth">季涨幅</span></th>
					<th><span class="sort" data-val="RRInSixMonth">半年涨幅</span></th>
					<th><span class="sort" data-val="RRSinceThisYear">今年以来涨幅</span></th>
					<th width="65"><span>操作</span></th>
					<th></th>
				</tr>
			</thead>
			<tbody class="tableDiv">
				
			</tbody>
			<tfoot>
				<tr>
					<td></td>
					<td colspan="8">
						<div class="pages">
							
						</div>
					</td>
					<td></td>
				</tr>
			</tfoot>
		</table>
		<!--<div class="noData"><!--暂无数据-- ></div>-->
	</section>
</div>
<script type="text/javascript">
var fundtype;
var company;
var fundcode;
var order;
var sort;
var page = 1;
var pageSize;
var totalPages;
var totalRecords;

$(document).ready(function(){
	 
	 var url = '<?= Url::to(['site/fund-list']);?>';
	 var data = {};
	 
	 Ajax(url,data);

	 /**
	 * Select fundtype
	 */
	 $(".fundtype").on('click','.typeLink',function(){
		fundtype = $(this).text();
		if (fundtype=='理财型') {fundtype='短期理财债券型'};
		
		if(fundtype == '全部'){
			fundtype = '';
			$('.sort').removeClass('curUp');
			$('.sort').removeClass('curDown');
			$('.dynamicField1').text('单位净值').attr('data-val','PernetValue');
			$('.dynamicField2').text('日涨幅').attr('data-val','NVDailyGrowthRate');
		}else if(fundtype == '货币型' || fundtype == '短期理财债券型' || fundtype == '理财型'){
			$('.dynamicField1').text('万份收益').attr('data-val','DailyProfit');
			$('.dynamicField2').text('七日年化收益').attr('data-val','LatestWeeklyYield');
		}else{
			$('.dynamicField1').text('单位净值').attr('data-val','PernetValue');
			$('.dynamicField2').text('日涨幅').attr('data-val','NVDailyGrowthRate');
		}
		$(this).addClass("cur").parent().siblings().children(".typeLink").removeClass("cur");
		
		// request data
		data = {'fundtype':fundtype,'company':company};
			
		// ajax request
		Ajax(url,data);
	 });
	 
	 /**
	 * Select company
	 */
	 $(".company").on('click','.typeLink2',function(){
		company = $(this).text();
		
		if(company == '全部'){
			company = '';
			$('.sort').removeClass('curUp');
			$('.sort').removeClass('curDown');
		}
		$(this).addClass("cur").parent().siblings().children(".typeLink2").removeClass("cur");
		
		// request data
		data = {'fundtype':fundtype,'company':company};
			
		// ajax request
		Ajax(url,data);
	 });
	 
	 /**
	 * Search fundcode
	 */
	 $("#searchcode").on('keydown',function(e){
		if(e.keyCode == 13){
			var fundcode = $('#searchcode').val();
			
			if(!fundcode){
				return;
			}
			
			$(".fundtype .navLink").removeClass('cur');
			$(".fundtype .typeLink").removeClass('cur');
			$(".fundtype dd:first>a").addClass('cur');
			$(".company .navLink2").removeClass('cur');
			$(".company .typeLink2").removeClass('cur');
			$(".company dd:first>a").addClass('cur');
			$('.sort').removeClass('curUp');
			$('.sort').removeClass('curDown');
			
			// request data
			var regx=/^\d+$/;	// new RegExp("^\d+{6}$")
			if(regx.test(fundcode)){
				data = {'fundcode':fundcode};
			}else{
				data = {'fundname':fundcode};
			}
			
			// ajax request
			Ajax(url,data);
		}
	 });
	 
	 /**
	 * Sort
	 */
	 $(".tableList .tableHeader").on('click','.sort',function(){
		if($(this).hasClass('curUp')){
			$(this).removeClass('curUp');
			$(this).addClass('curDown');
			sort = 'DESC';
		}else if($(this).hasClass('curDown')){
			$(this).removeClass('curDown');
			$(this).addClass('curUp');
			sort = 'ASC';
		}else{
			$('.sort').removeClass('curUp');
			$('.sort').removeClass('curDown');
			$(this).addClass('curDown');
			sort = 'DESC';
		}
		
		order = $(this).attr('data-val');
		
		// request data
		data['order'] = order;
		data['sort'] = sort;
			
		// ajax request
		Ajax(url,data);
	 });
	 
	/**
	* Previous
	*/
	$('.tableList .pages').on('click', '.pagePrev', function(){
		var prevPage = Number(page) - 1;

		if (prevPage < 1) {
			return;
		}
		// 请求参数
		data = {'fundtype':fundtype,'company':company,'fundcode':fundcode,'order':order,'sort':sort,'page':prevPage,'pageSize':pageSize};

		// Ajax处理函数
		Ajax(url, data);
	});
	
	/**
	* Next
	*/
	$('.tableList .pages').on('click', '.pageNext', function(){
		var nextPage = Number(page) + 1;

		if (nextPage > Number(totalPages)) {
			return;
		}
		// request data
		data = {'fundtype':fundtype,'company':company,'fundcode':fundcode,'order':order,'sort':sort,'page':nextPage,'pageSize':pageSize};

		// ajax request
		Ajax(url, data);
	});
	
	/**
	* Paging
	*/
	$('.tableList .pages').on('click', '.page', function(){
		var curPage = $(this).text();

		if (curPage > Number(totalPages) || curPage < 1) {
			return;
		}
		// request data
		data = {'fundtype':fundtype,'company':company,'fundcode':fundcode,'order':order,'sort':sort,'page':curPage,'pageSize':pageSize};

		// ajax request
		Ajax(url, data);
	});
});

/**
* Ajax处理函数
*/
function Ajax(url, data, type) {
	var spinner = new Spinner().spin();
	
	data.pageSize = pageSize;
	
	$.ajax({
        type: type || 'GET',
        async: true,
        url: url,
        data: data,
        dataType: 'json',
		crossDomain: true,    //允许跨域
        beforeSend: function(XMLHttpRequest){
			$('.tableList').get(0).appendChild(spinner.el);
        },
        complete: function(XMLHttpRequest, textStatus){
			spinner.stop();
        },
        success: function(rs){
			if (rs.error == 0) {
                if (rs.list.length > 0) {
	                $(".tableList .tableDiv").html(viewList(rs.list));
					
                	page = rs.page;
	                totalPages = rs.totalPages;
	                totalRecords = rs.totalRecords;
                }else{
                	// 重置分页数据
                	page = 1;
	                totalPages = 1;
	                totalRecords = 1;

	                $(".tableList .tableDiv").html('');
                }
				$(".tableList .pages").html(viewPaging(rs));
        	}else{
        		console.log(rs);
        	}
			
			// console.log(rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log(errorThrown);
        }
    });
}


function viewEmpty(data){
	var html = '';
	
	html += '<ul class="nodate"><li></li>';
	html += '<li></li>';
	html += '<li></li>';
	html += '<li></li>';
	html += '<li></li>';
	html += '<li></li>';
	html += '<li></li>';
	html += '<li></li>';
	html += '<li></li>';
	html += '<li></li></ul>';
	
	return html;
}

function viewList(data){
	var html = '';
	
	var fundtype = '';
	var dynamicField1 = $('.dynamicField1').attr('data-val');
	var dynamicField2 = $('.dynamicField2').attr('data-val');
	$.each(data, function(i, data){
		if(data['FundTypeCode'] == '1101'){
			fundtype = 'tagGp';
		}else if(data['FundTypeCode'] == '1109'){
			fundtype = 'tagHb';
		}else if(data['FundTypeCode'] == '1103'){
			fundtype = 'tagHh';
		}else if(data['FundTypeCode'] == '1105'){
			fundtype = 'tagZq';
		}else if(data['FundTypeCode'] == '1106'){
			fundtype = 'tagLc';
		}else if(data['FundTypeCode'] == '1110'){
			fundtype = 'tagQdii';
		}
		
		dynamicVal1 = (dynamicField1 == 'DailyProfit' && (data['FundTypeCode'] == '1106' || data['FundTypeCode'] == '1109')?data['DailyProfit']:data['PernetValue']);
		dynamicVal2 = (dynamicField2 == 'LatestWeeklyYield' && (data['FundTypeCode'] == '1106' || data['FundTypeCode'] == '1109')?data['LatestWeeklyYield']:data['NVDailyGrowthRate']);
		
		if(dynamicVal1>0){
			valCol1 = 'colRed';
		}else if(dynamicVal1<0){
			valCol1 = 'colGreen';
		}else{
			valCol1 = '';
		}
		if(dynamicVal2>0){
			valCol2 = 'colRed';
		}else if(dynamicVal2<0){
			valCol2 = 'colGreen';
		}else{
			valCol2 = '';
		}
		if(data['RRInSelectedMonth']>0){
			valCol3 = 'colRed';
		}else if(data['RRInSelectedMonth']<0){
			valCol3 = 'colGreen';
		}else{
			valCol3 = '';
		}
		if(data['RRInThreeMonth']>0){
			valCol4 = 'colRed';
		}else if(data['RRInThreeMonth']<0){
			valCol4 = 'colGreen';
		}else{
			valCol4 = '';
		}
		if(data['RRInSixMonth']>0){
			valCol5 = 'colRed';
		}else if(data['RRInSixMonth']<0){
			valCol5 = 'colGreen';
		}else{
			valCol5 = '';
		}
		if(data['RRSinceThisYear']>0){
			valCol6 = 'colRed';
		}else if(data['RRSinceThisYear']<0){
			valCol6 = 'colGreen';
		}else{
			valCol6 = '';
		}
		html += '<tr class="content-list">';
		html += '<td></td>';
		html += '<td class="talL"><a href="<?= Url::to(['site/detail']);?>?fundcode='+data['FundCode']+'"><span class="fundTag '+fundtype+'">'+data['FundName']+'<br />'+data['FundCode']+'</span></a></td>'
		html += '<td><span class="'+valCol1+'">'+dynamicVal1+'</span></td>';
		html += '<td><span class="'+valCol2+'">'+(dynamicVal2+'%')+'</span></td>';
		html += '<td><span class="'+valCol3+'">'+(data['RRInSelectedMonth']?data['RRInSelectedMonth']+'%':'--')+'</span></td>';
		html += '<td><span class="'+valCol4+'">'+(data['RRInThreeMonth']?data['RRInThreeMonth']+'%':'--')+'</span></td>';
		html += '<td><span class="'+valCol5+'">'+(data['RRInSixMonth']?data['RRInSixMonth']+'%':'--')+'</span></td>';
		html += '<td><span class="'+valCol6+'">'+(data['RRSinceThisYear']?data['RRSinceThisYear']+'%':'--')+'</span></td>';
		html += '<td><a href="<?= Yii::$app->params['tradeHost'];?>trade/purchase-page?fundcode='+data['FundCode']+'" class="buttonB">购买</a></td></tr>';
	});
	
	return html;
}


function viewPaging(data){
	
	var html = '';
	
	var page = data.page ? parseInt(data.page) : 0;
	var totalPages = parseInt(data.totalPages);
	
	html += '<a href="javascript:void(0);" class="pagePrev page">&nbsp;</a>';
	if(totalPages>1 && page>1){
		html += '<a href="javascript:void(0);" class="page firstPage">1</a>';
	}
	if(totalPages>4 && page > 3){
		html += '<span class="page">······</span>';
	}
	if(page-1>1){
		html += '<a href="javascript:void(0);" class="page curPrev">'+(page-1)+'</a>';
	}
	
	html += '<a href="javascript:void(0);" class="page cur">'+page+'</a>';
	
	if(page+1<totalPages){
		html += '<a href="javascript:void(0);" class="page curNext">'+(page+1)+'</a>';
	}
	
	if(totalPages > 4 && page<totalPages-2){
		html += '<span class="page">······</span>';
	}
	if(page<totalPages){
		html += '<a href="javascript:void(0);" class="page endPage">'+totalPages+'</a>';
	}
	
	html += '<a href="javascript:void(0);" class="pageNext page">&nbsp;</a>';

	return html;
}
</script>
