<?php
use yii\helpers\Url;

$this->title = '产品市场';
?>
<!--登陆后结束-->
<div class="main">
	<div class="content">
		<div class="pT10">
			<div class="titBlk">
				<div class="titlist">
					<ul>
						<li>产品市场</li>
					</ul>
				</div>
			</div>
			<!--titBlk end-->
		</div>
		<!--end pT20-->
		<div class="ptype dlBlk">
			<dl>
				<dt>产品种类：</dt>
				<dd><a href="javascript:void(0);" class="linkT2 colCur" data-code="14">货币型</a></dd>
				<dd><a href="javascript:void(0);" class="linkT2" data-code="12">债券型</a></dd>
				<dd><a href="javascript:void(0);" class="linkT2" data-code="11">混合型</a></dd>
				<dd><a href="javascript:void(0);" class="linkT2" data-code="10">股票型</a></dd>
				<dd><a href="javascript:void(0);" class="linkT2" data-code="15">QDII</a></dd>
				<dd><a href="javascript:void(0);" class="linkT2" data-code="13">其他</a></dd>
			</dl>
			<dl>
				<dt>二级分类：</dt>
				<dd>
					<div class="dropBox">
						<div class="dbSlted">
							<span class="dbSltedVal secondCatgoryAll">全部</span><i class="ico icoDown"></i>
						</div>
						<div class="options optBox secondCatgory">
							<ul>
								<li data-code=''>全部</li>
								<li data-code='14010'>货币市场基金-A类</li>
								<li data-code='14020'>货币市场基金-B类</li>
							</ul>
						</div>
					</div>
				</dd>
				<dd class="label">按机构：</dd>
				<dd>
					<div class="dropBox">
						<div class="dbSlted">
							<span class="dbSltedVal">
								<input type="text" name="" id="multi" value="全部" />
							</span>
							<i class="ico icoDown"></i>
						</div>
						<div class="options multiBox advisor">
							<ul>
								<?php if(isset($advisorCodes) && !empty($advisorCodes)):?>
									<?php foreach($advisorCodes as $key=>$value):?>
										<li><label class="multi" data-code="<?= $value['advisorCode'];?>"><?= $value['advisorShortName'];?></label></li>
									<?php endforeach;?>
								<?php endif;?>
							</ul>
						</div>
					</div>
				</dd>
				<dd><a href="javascript:void(0);" class="buttonB searchBtn">查询</a></dd>
			</dl>
			<dl>
				<dt>产品搜索：</dt>
				<dd>
					<div class="itemWarp">
						<div class="txtitem seachItem">
							<div class="item">
								<input type="text" name="searchKey" id="searchKey" value="" placeholder="输入产品代码/简称/拼音" class="txtInput seachInput" />
								<a href="javascript:void(0);" class="seachBtn">
									<span class="ico icoSeach"></span>
								</a>
							</div>
						</div>
					</div>
					<!--itemWarp end-->
				</dd>
			</dl>
		</div>
		<!--没有数据时使用<div class="nodata"><span>暂无查询结果</span></div>-->
		<div class="tableWarp tableList">
			<div class="tableMain">
				<table id="ta" border="0" cellspacing="0" cellpadding="0" class="table product">
					<thead>
						<tr>
							<th><div class="column1">产品简称</div></th>
							<th><div class="column2">万份收益(元)</div></th>
							<th>
								<div class="column3 s_click allType">
									<div class="t_click">
										<span class="t_selected" data-field="annualizedOneWeek">7日年化收益率</span>
										<i class="ico icoDown"></i>
										<i class="ico sort"></i>
									</div>
									<div class="typeList">
										<ul>
											<li data-field="annualizedTwoWeek">14日年化收益率</li>
											<li data-field="annualizedThreeWeek">21日年化收益率</li>
											<li data-field="annualizedFourWeek">35日年化收益率</li>
											<li data-field="oneMonth">近1月收益率</li>
											<li data-field="threeMonth">近3月收益率</li>
											<li data-field="sixMonth">近6月收益率</li>
										</ul>
									</div>
								</div>
							</th>
							<th>
								<div class="column4 s_click allType">
									<div class="t_click">
										<span class="t_selected" data-field="thisYear">今年以来收益率</span>
										<i class="ico icoDown"></i>
										<i class="ico sort"></i>
									</div>
									<div class="typeList">
										<ul>
											<li data-field="oneYear">近1年收益率</li>
											<li data-field="twoYear">近2年收益率</li>
											<li data-field="threeYear">近3年收益率</li>
											<li data-field="fiveYear">近5年收益率</li>
											<li data-field="sinceStart">成立以来收益率</li>
										</ul>
									</div>
								</div>
							</th>
							<th><div class="column5">万份收益波动率</div></th>
							<!-- <th><div class="column6">赎回便利</div></th> -->
							<th><div class="column7">申购限制(元)</div></th>
							<th><div class="column8">最新规模(亿元)</div></th>
						</tr>
					</thead>
					<!--排序样式:需要排序的字段文字后面加<i class="ico sort"></i>；在div上加样式 s_click；升序在div上添加样式sort_up、降序添加sort_down-->
					<tbody>
						
					</tbody>
				</table>
				<div class="nodata" style="display: none;"><span>暂无查询结果</span></div>
			</div>
			<div class="pagesWarp">
				<div class="pages">

				</div>
				<!-- <a href="javascript:void(0);" class="linkT1 dataToExcel">导出查询结果</a> -->
			</div>
		</div>
		<!--tableWarp end-->
	</div>
	<!--content end-->
</div>
<!--main end-->
<script src="<?= \yii\helpers\Url::base();?>/js/spin.min.js"></script>
<script>
var dataType = 1;	// 1-货基/短期理财，0-非货基
var multiField = [{attr:'annualizedOneWeek',text:'7日年化收益率'},{attr:'thisYear',text:'今年以来收益率'}];
var sort = 'annualizedOneWeek';
var order = 'desc';
var page = 1;
var pageSize = 10;
var totalPages;
var totalRecords;

var othead = $('.tableList table thead');
var otbody = $('.tableList table tbody');

$(function(){
	var url = '<?php Url::to(["fund/index"]);?>';
	var data = {type1Code:'14',type2Code:'',codeOrName:'','advisorCodes':[],page:page,rows:pageSize,sort:sort,order:order};

	// 初始化数据请求
	Ajax(url,data);

	// 基金类型选择事件
	$(".ptype .linkT2").on("click",function(e){
		var secCatData = [];
		$(this).addClass("colCur").parent().siblings().children(".linkT2").removeClass("colCur");
		if($(this).hasClass("colCur")){
			var dataCode1 = $(this).attr("data-code");
			if (dataCode1=='10') {
				dataType = 0;
				secCatData = [{code:10010,name:'股票型基金'},{code:10020,name:'指数型股票基金'},{code:10030,name:'特定策略股票型基金'},{code:10040,name:'股票型分级子基金'},{code:10050,name:'行业股票型基金'}];
			}else if (dataCode1=='11') {
				dataType = 0;
				secCatData = [{code:11000,name:'其他混合基金'},{code:11010,name:'偏股型基金'},{code:11020,name:'灵活配置型基金'},{code:11030,name:'股债平衡型基金'},{code:11040,name:'偏债型基金'},{code:11050,name:'保本型基金'},{code:11060,name:'特定策略混合型基金'},{code:11070,name:'绝对收益目标基金'}];
			}else if (dataCode1=='12') {
				dataType = 0;
				secCatData = [{code:12010,name:'标准债券型基金'},{code:12020,name:'普通债券型基金'},{code:12030,name:'特定策略债券型基金'},{code:12040,name:'可转换债券型基金'},{code:12050,name:'指数型债券基金'},{code:12060,name:'债券型分级子基金'},{code:12070,name:'短期理财债券型基金'}];
			}else if (dataCode1=='13') {
				dataType = 0;
				secCatData = [{code:13010,name:'黄金基金'}];
			}else if (dataCode1=='14') {
				dataType = 1;
				secCatData = [{code:14010,name:'货币市场基金(A类)'},{code:14020,name:'货币市场基金(B类)'}];
			}else if (dataCode1=='15') {
				dataType = 0;
				secCatData = [{code:15010,name:'QDII股票基金'},{code:15020,name:'QDII(FOF)基金'},{code:15030,name:'QDII债券基金'},{code:15040,name:'QDII混合基金'},{code:15050,name:'特定策略QDII基金'},{code:15060,name:'QDII商品基金'},{code:15070,name:'其他QDII基金'}];
			}else{
				dataType = 1;
				secCatData = [{code:14010,name:'货币市场基金(A类)'},{code:14020,name:'货币市场基金(B类)'}];
			}
			// 修改二级分类
			$(".secondCatgory").html(secondCatgory(secCatData));
			$(".secondCatgoryAll").text("全部");
			// ajax请求
			data.type1Code = dataCode1;
			Ajax(url, data);
			// 隐藏字段鼠标经过事件
			$(".allType").hover(function(){
				$(this).children(".typeList").show();
			},function(){
				$(this).children(".typeList").hide();
			});
		}
	});

	// 二级分类选择事件
	$(".secondCatgory").on("click", "li",function(e){
		var sltVal = $(this).html();
		$(this).parents(".options").hide().siblings(".dbSlted").children(".dbSltedVal").html(sltVal);
		data.type2Code = $(this).attr("data-code");
		// console.log(data);
	});

	// 基金公司/机构选择事件
	$(".advisor").on("click", "li",function(e){
		e.stopPropagation();
		$(this).children(".multi").toggleClass("multied");
		var tagArray= new Array();
		var dataCodes = [];
		$(".multied").each(function(){
			tagArray.push($(this).html());
			dataCodes.push($(this).attr("data-code"));
		});
		if(tagArray.length==0){
			$("#multi").val("全部")
			data.advisorCodes = []
		}else{
			$("#multi").val(tagArray).focus();
			data.advisorCodes = dataCodes;
		}
		// console.log(data);
	});

	// 点击查询事件
	$(".searchBtn").on("click",function(e){
		data.codeOrName = $("#searchKey").val();

		Ajax(url,data);
	});

	// 字段1切换
	$(".tableList").on("click",".column3 .typeList li",function(e){
		var a1 = $(this).attr("data-field");
		var t1 = $(this).text();
		var a2 = $(this).parents(".allType").find(".t_selected").attr("data-field");
		var t2 = $(this).parents(".allType").find(".t_selected").text();
		$(this).text(t2).attr("data-field",a2);
		$(this).parents(".allType").find(".t_selected").text(t1).attr("data-field",a1);
		$(this).parents(".s_click").removeClass("sort_up").removeClass("sort_down");
		multiField[0] = {attr:a1,text:t1};
	});

	// 字段2切换
	$(".tableList").on("click",".column4 .typeList li",function(e){
		var a1 = $(this).attr("data-field");	// 选择字段属性
		var t1 = $(this).text();	// 选择字段
		var a2 = $(this).parents(".allType").find(".t_selected").attr("data-field");	// 原字段属性
		var t2 = $(this).parents(".allType").find(".t_selected").text();	// 原字段
		$(this).text(t2).attr("data-field",a2);
		$(this).parents(".allType").find(".t_selected").text(t1).attr("data-field",a1);
		$(this).parents(".s_click").removeClass("sort_up").removeClass("sort_down");
		multiField[1] = {attr:a1,text:t1};
	});

	// 字段排序
	$(document).on("click",".s_click",function(e){
		if($(this).hasClass("sort_down")){
			$(this).removeClass("sort_down");
			$(this).addClass("sort_up");
			data.order = 'asc';
		}else if($(this).hasClass("sort_up")){
			$(this).removeClass("sort_up");
			$(this).addClass("sort_down");
			data.order = 'desc';
		}else{
			$(this).addClass("sort_down");
			data.order = 'desc';
		}
		$(this).parent("th").siblings().children(".s_click").removeClass("sort_up").removeClass("sort_down");

		// 请求参数
		data.sort = $(this).find(".t_selected").attr("data-field");
		// Ajax处理函数
		Ajax(url, data);
		
	});

	/**
	* 首页
	*/
	$('.tableList').on('click', '.home', function(){
		// 请求参数
		data.page = 1;

		// Ajax处理函数
		Ajax(url, data);
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
		data.page = prevPage;

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
		data.page = nextPage;

		// Ajax处理函数
		Ajax(url, data);
	});

	/**
	* 尾页
	*/
	$('.tableList').on('click', '.end', function(){
		// 请求参数
		data.page = totalPages;

		// Ajax处理函数
		Ajax(url, data);
	});

	//导出excel
	$(".dataToExcel").on("click",function(e){
		var excelUrl = '<?= Url::base();?>'+'/phpexcel/data-to-excel.php?dataType='+dataType;
		// var excelUrl = '<?= Url::base();?>'+'/phpexcel/data-to-excel.php?data='+data;
		$(".dataToExcel").attr("href",excelUrl);

		/*var theadData;
		if (dataType == 1) {
			var excelUrl = '<?= Url::base();?>'+'/phpexcel/data-to-excel.php?dataType='+dataType;
			$(".dataToExcel").attr("href",excelUrl);
			theadData = ['产品简称','万份收益(元)','7日年化收益率'];
		}else{
			theadData = ['产品简称','单位净值（元）','日增长率'];
		}
		

		var url = '';
		$.each(data,function(i,n){
			url += '&'+i+'='+n;
		});
		url = '<?= Url::base().'/phpexcel/data-to-excel.php';?>?'+url.substr(1);
		console.log(url);
		$.ajax({
	        type: 'GET',
	        async: false,
	        url: url,
	        data: {},
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){
	        },
	        complete: function(XMLHttpRequest, textStatus){
	        },
	        success: function(rs){

	        	console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });*/
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
        	var offset = (page-1)*pageSize?(page-1)*pageSize:1;
        	$('.curPage').text(offset+'-'+(page*pageSize));
        	$('.totalRecords').text(totalRecords);
        	spinner.stop();
        },
        success: function(rs){
        	if (rs.code == '111') {
        		otbody.html('');
                if (rs.res.listObjects.length > 0) {
                	otbody.html(viewList(rs.res.listObjects));

                	page = rs.res.page;
                	pageSize = rs.res.rows;
	                totalRecords = rs.res.total;
	                totalPages = Math.ceil(totalRecords/pageSize);
	                $(".pagesWarp").show();
	                $(".nodata").hide();
                }else{
                	// 重置列表数据
                	$(".nodata").show();
                	$(".pagesWarp").hide();

                	// 重置分页数据
                	page = 1;
	                totalPages = 1;
	                totalRecords = 0;	                
                }
                // 分页
                $(".tableList .pages").html(viewPaging(page,pageSize,totalRecords));
                // 隐藏字段鼠标经过事件
                $(".allType").hover(function(){
					$(this).children(".typeList").show();
				},function(){
					$(this).children(".typeList").hide();
				});
        	}
        	console.log(multiField,rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log(errorThrown);
        }
    });
}

/**
* 二级分类
*/
function secondCatgory(secCatData) {
	var html = '<ul>';
	html += '<li data-code="">全部</li>'
	$.each(secCatData,function(i,n){
		html += '<li data-code="'+n.code+'">'+n.name+'</li>'
	});
	html += '</ul>';

	return html;
}

/**
* table list
* @params dataType 1-货基/短期理财，0-非货基
*/
function viewList(dataList) {
	var html = '';
	
	$.each(dataList,function(i,n){
		html += '<tr class="'+(i%2?'odd':'even')+' animation animation-delay-'+i+'">';
		html += '<td><div class="column1">';
		html += '<span>'+n.fundShortName+'</span>';
		html += '<span class="colGray">'+n.fundcode+'</span>';
		html += '</div></td>';
		html += '<td><div class="column2">';
		html += '<span class="'+(dataType?(n.hf_incomeratio>0?'colRed':(n.hf_incomeratio<0?'colGreen':'colGray')):(n.pernetvalue>0?'colRed':(n.pernetvalue<0?'colGreen':'colGray')))+'">'+(dataType?(n.hf_incomeratio==null?'--':n.hf_incomeratio):(n.pernetvalue==null?'--':n.pernetvalue))+'</span>';
		html += '<span class="colGray">'+n.navdate.replace(/(\d{4})(\d{2})(\d{2})/, "$1-$2-$3"); +'</span>';
		html += '</div></td>';
		html += '<td><div class="column3">';
		if (dataType == 1) {
			if (multiField[0]['attr'] == 'annualizedOneWeek') {
				html += '<span class="'+(n.annualizedOneWeek>0?'colRed':(n.annualizedOneWeek<0?'colGreen':'colGray'))+'">'+(n.annualizedOneWeek==null?'--':n.annualizedOneWeek+'%')+'</span>';
			}else if (multiField[0]['attr'] == 'annualizedTwoWeek') {
				html += '<span class="'+(n.annualizedTwoWeek>0?'colRed':(n.annualizedTwoWeek<0?'colGreen':'colGray'))+'">'+(n.annualizedTwoWeek==null?'--':n.annualizedTwoWeek+'%')+'</span>';
			}else if (multiField[0]['attr'] == 'annualizedThreeWeek') {
				html += '<span class="'+(n.annualizedThreeWeek>0?'colRed':(n.annualizedThreeWeek<0?'colGreen':'colGray'))+'">'+(n.annualizedThreeWeek==null?'--':n.annualizedThreeWeek+'%')+'</span>';
			}else if (multiField[0]['attr'] == 'annualizedFourWeek') {
				html += '<span class="'+(n.annualizedFourWeek>0?'colRed':(n.annualizedFourWeek<0?'colGreen':'colGray'))+'">'+(n.annualizedFourWeek==null?'--':n.annualizedFourWeek+'%')+'</span>';
			}else if (multiField[0]['attr'] == 'oneMonth') {
				html += '<span class="'+(n.oneMonth>0?'colRed':(n.oneMonth<0?'colGreen':'colGray'))+'">'+(n.oneMonth==null?'--':n.oneMonth+'%')+'</span>';
			}else if (multiField[0]['attr'] == 'threeMonth') {
				html += '<span class="'+(n.threeMonth>0?'colRed':(n.threeMonth<0?'colGreen':'colGray'))+'">'+(n.threeMonth==null?'--':n.threeMonth+'%')+'</span>';
			}else if (multiField[0]['attr'] == 'sixMonth') {
				html += '<span class="'+(n.sixMonth>0?'colRed':(n.sixMonth<0?'colGreen':'colGray'))+'">'+(n.sixMonth==null?'--':n.sixMonth+'%')+'</span>';
			}else{
				html += '<span class="'+(n.annualizedOneWeek>0?'colRed':(n.annualizedOneWeek<0?'colGreen':'colGray'))+'">'+(n.annualizedOneWeek==null?'--':n.annualizedOneWeek+'%')+'</span>';
			}
		}else{
			html += '<span class="'+(n.nVDailyGrowthRate>0?'colRed':(n.nVDailyGrowthRate<0?'colGreen':'colGray'))+'">'+(n.nVDailyGrowthRate==null?'--':n.nVDailyGrowthRate+'%')+'</span>';
		}
		html += '</div></td>';
		html += '<td><div class="column4">';
		if (dataType == 1) {
			if (multiField[1]['attr'] == 'thisYear') {
				html += '<span class="'+(n.thisYear>0?'colRed':(n.thisYear<0?'colGreen':'colGray'))+'">'+(n.thisYear==null?'--':n.thisYear+'%')+'</span>';
			}else if (multiField[1]['attr'] == 'oneYear') {
				html += '<span class="'+(n.oneYear>0?'colRed':(n.oneYear<0?'colGreen':'colGray'))+'">'+(n.oneYear==null?'--':n.oneYear+'%')+'</span>';
			}else if (multiField[1]['attr'] == 'twoYear') {
				html += '<span class="'+(n.twoYear>0?'colRed':(n.twoYear<0?'colGreen':'colGray'))+'">'+(n.twoYear==null?'--':n.twoYear+'%')+'</span>';
			}else if (multiField[1]['attr'] == 'threeYear') {
				html += '<span class="'+(n.threeYear>0?'colRed':(n.threeYear<0?'colGreen':'colGray'))+'">'+(n.threeYear==null?'--':n.threeYear+'%')+'</span>';
			}else if (multiField[1]['attr'] == 'fiveYear') {
				html += '<span class="'+(n.fiveYear>0?'colRed':(n.fiveYear<0?'colGreen':'colGray'))+'">'+(n.fiveYear==null?'--':n.fiveYear+'%')+'</span>';
			}else if (multiField[1]['attr'] == 'sinceStart') {
				html += '<span class="'+(n.sinceStart>0?'colRed':(n.sinceStart<0?'colGreen':'colGray'))+'">'+(n.sinceStart==null?'--':n.sinceStart+'%')+'</span>';
			}else{
				html += '<span class="'+(n.thisYear>0?'colRed':(n.thisYear<0?'colGreen':'colGray'))+'">'+(n.thisYear==null?'--':n.thisYear+'%')+'</span>';
			}
		}else{
			if (multiField[1]['attr'] == 'thisYear') {
				html += '<span class="'+(n.thisYear>0?'colRed':(n.thisYear<0?'colGreen':'colGray'))+'">'+(n.thisYear==null?'--':n.thisYear+'%')+'</span>';
			}else if (multiField[1]['attr'] == 'oneMonth') {
				html += '<span class="'+(n.oneMonth>0?'colRed':(n.oneMonth<0?'colGreen':'colGray'))+'">'+(n.oneMonth==null?'--':n.oneMonth+'%')+'</span>';
			}else if (multiField[1]['attr'] == 'threeMonth') {
				html += '<span class="'+(n.threeMonth>0?'colRed':(n.threeMonth<0?'colGreen':'colGray'))+'">'+(n.threeMonth==null?'--':n.threeMonth+'%')+'</span>';
			}else if (multiField[1]['attr'] == 'sixMonth') {
				html += '<span class="'+(n.sixMonth>0?'colRed':(n.sixMonth<0?'colGreen':'colGray'))+'">'+(n.sixMonth==null?'--':n.sixMonth+'%')+'</span>';
			}else if (multiField[1]['attr'] == 'oneYear') {
				html += '<span class="'+(n.oneYear>0?'colRed':(n.oneYear<0?'colGreen':'colGray'))+'">'+(n.oneYear==null?'--':n.oneYear+'%')+'</span>';
			}else if (multiField[1]['attr'] == 'twoYear') {
				html += '<span class="'+(n.twoYear>0?'colRed':(n.twoYear<0?'colGreen':'colGray'))+'">'+(n.twoYear==null?'--':n.twoYear+'%')+'</span>';
			}else if (multiField[1]['attr'] == 'threeYear') {
				html += '<span class="'+(n.threeYear>0?'colRed':(n.threeYear<0?'colGreen':'colGray'))+'">'+(n.threeYear==null?'--':n.threeYear+'%')+'</span>';
			}else if (multiField[1]['attr'] == 'fiveYear') {
				html += '<span class="'+(n.fiveYear>0?'colRed':(n.fiveYear<0?'colGreen':'colGray'))+'">'+(n.fiveYear==null?'--':n.fiveYear+'%')+'</span>';
			}else if (multiField[1]['attr'] == 'sinceStart') {
				html += '<span class="'+(n.sinceStart>0?'colRed':(n.sinceStart<0?'colGreen':'colGray'))+'">'+(n.sinceStart==null?'--':n.sinceStart+'%')+'</span>';
			}else{
				html += '<span class="'+(n.oneYear>0?'colRed':(n.oneYear<0?'colGreen':'colGray'))+'">'+(n.thisYear==null?'--':n.thisYear+'%')+'</span>';
			}
		}
		html += '</div></td>';
		html += '<td><div class="column5">';
		html += '<span class="'+(dataType?(n.volatility>0?'colRed':(n.volatility<0?'colGreen':'colGray')):(n.sharpRatio>0?'colRed':(n.sharpRatio<0?'colGreen':'colGray')))+'">'+(dataType?(n.volatility==null?'--':n.volatility+'%'):(n.sharpRatio==null?'--':n.sharpRatio+'%'))+'</span>';
		html += '</div></td>';
		/*html += '<td><div class="column6">';
		html += '<span class="'+(dataType?(n.redemption>0?'colRed':(n.redemption<0?'colGreen':'colGray')):(n.maxDrawdown>0?'colRed':(n.maxDrawdown<0?'colGreen':'colGray')))+'">'+(dataType?(n.redemption==''?'--':n.redemption):(n.maxDrawdown==''?'--':n.maxDrawdown))+'</span>';
		html += '</div></td>';*/
		html += '<td><div class="column7">';
		html += '<span class="'+(n.lowestSumPurLL>0?'colRed':(n.lowestSumPurLL<0?'colGreen':'colGray'))+'">'+(n.lowestSumPurLL==null?'--':n.lowestSumPurLL)+'</span>';
		html += '</div></td>';
		html += '<td><div class="column8">';
		html += '<span class="'+(dataType?(n.foundedSize>0?'colRed':(n.foundedSize<0?'colGreen':'colGray')):(n.ranking>0?'colRed':(n.ranking<0?'colGreen':'colGray')))+'">'+(dataType?(n.foundedSize==null?'--':(n.foundedSize/100000000).toFixed(2)):(n.ranking==null?'--':n.ranking))+'</span>';
		html += '</div></td>';
		html += '</tr>';
	});
	
	return html;
}

function viewPaging(page,pageSize,totalRecords) {

	var html = '';
	
	var page = page ? parseInt(page) : 0;
	var totalPages = Math.ceil(totalRecords/pageSize);

	if (page > 1) {
		html += '<a href="javascript:void(0);" class="page pLink home"><< 首页</a>';
		html += '<a href="javascript:void(0);" class="page pLink prev">< 上一页</a>'
	}
	html += totalRecords>pageSize ? '<span class="page pSpan curPage"></span>条，' : '';
	html += '共<span class="page pSpan totalRecords">'+totalRecords+'</span>条';
	if (page < totalPages) {
		html += '<a href="javascript:void(0);" class="page pLink next">下一页></a>';
		html += '<a href="javascript:void(0);" class="page pLink end">尾页>></a>';
	}
	
	return html;
}
</script>