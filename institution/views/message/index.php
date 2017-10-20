<?php
use yii\helpers\Url;
use institution\components\LeftBarWidget;

$this->title = '消息中心';
?>
<!--登陆后结束-->
<div class="main">
	<div class="Side">
		<?php echo LeftBarWidget::widget();?>
		<!--leftBar end-->
		<div class="rightBar">
			<div class="content">
				<div class="newestNotice slideDown">
					<span class="close">X</span>
					<span class="date">2016-02-24</span>
					<span class="horn">最新公告：春节期间基金申购赎回、小金库快赎业务公告</span> 
				</div>
				<!--newestNotice end-->
				<div class="pT10">
					<div class="titBlk">
						<div class="titlist">
							<ul>
								<li><span class="tabOption">消息中心</span></li>
							</ul>
						</div>
					</div>
					<!--titBlk end-->
				</div>
				<!--end pT20-->
				<div class="tableWarp tabContent"> 
					<div class="tabCnts">
						<div class="tabCnt">
							<!--<div class="nodata">
								<span>暂无数据</span>
							</div>-->
							<div class="ptype dlBlk">
								<dl>
									<dt>业务类型：</dt>
									<dd>
										<div class="dropBox">
											<div class="dbSlted">
												<span class="dbSltedVal">全部</span>
												<i class="ico icoDown"></i>
											</div>
											<div class="options optBox bustype">
												<ul>
													<li>全部</li>
													<li>公告</li>
													<li>开户</li>
													<li>申购</li>
													<li>赎回</li>
													<li>认购</li>
													<li>转换</li>
													<li>撤单</li>
													<li>分红</li>
													<li>产品</li>
												</ul>
											</div>
										</div>
										<!--dropBox end-->
									</dd>
									<dd class="label">日历</dd>
									<dd>
										<div class="itemWarp">
											<div class="txtitem">
												<div class="item">
													<input type="text" name="" id="startDate" value="" placeholder="起始日期" class="txtInput startDate" />
													<i class="ico icoDate"></i>
												</div>
											</div>
										</div>
										<!--itemWarp end-->
									</dd>
									<dd class="label">至</dd>
									<dd>
										<div class="itemWarp">
											<div class="txtitem">
												<div class="item">
													<input type="text" name="" id="endDate" value="" placeholder="结束日期" class="txtInput endDate" />
													<i class="ico icoDate"></i>
												</div>
											</div>
										</div>
										<!--itemWarp end-->
									</dd>
									<dd><a href="javascript:void(0);" class="buttonB searchBtn">查询</a></dd>
								</dl>
							</div>
							<!--ptype end-->
							<div class="tableList">
								<div class="message tableMain">
									<table border="0" cellspacing="1" cellpadding="0" class="table mTable">
										<thead>
											<tr>
												<th width="100" align="left" style="padding-left: 20px;">时间</th>
												<th>内容</th>
												<th width="80">类型</th>
												<th width="80">附件</th>
											</tr>
										</thead>
										<tbody>
											
										</tbody>
									</table>
								</div>
								<div class="pagesWarp">
									<div class="pages">
										<!-- <a href="javascript:void(0);" class="page pLink"><< 首页</a>
										<a href="javascript:void(0);" class="page pLink">< 上一页</a>
										<span class="page pSpan curPage">1-20</span>条，共
										<span class="page pSpan">564</span>条
										<a href="javascript:void(0);" class="page pLink">下一页></a>
										<a href="javascript:void(0);" class="page pLink">尾页>></a> -->
									</div>
								</div>
								<!--分页结束-->
								<div class="nodata" style="display: none"><span>暂无查询结果</span></div>
							</div>
						</div>
					</div>
					<!--tabCnts end-->
				</div>
				<!--tableWarp end-->
			</div>
			<!--content end-->
		</div>
		<!--rightBar end-->
	</div>
	<!--Side end-->
</div>
<!--main end-->
<script src="<?= \yii\helpers\Url::base();?>/js/spin.min.js"></script>
<script type="text/javascript">
var orgCode = '<?= $orgCode;?>';
var type = '';
var startDate = '';
var endDate = '';
var page = 1;
var pageSize = 10;
var totalPages;
var totalRecords;

var othead = $('.tableList table thead');
var otbody = $('.tableList table tbody');

$(document).ready(function(e){
	var url = '<?= Url::to(["message/index"]);?>';
	var data = {orgCode:orgCode,sdate:startDate,edate:endDate,type:type,page:page,rows:pageSize};

	// 初始化数据请求
	Ajax(url,data);

	/*
	 * 日历
	 * 交易申请
	 */
	laydate({
        elem: '.startDate',
        choose: function(datas){ //选择日期完毕的回调
        	startDate = datas;
	    }
    });

	laydate({
        elem: '.endDate',
        choose: function(datas){ //选择日期完毕的回调
        	endDate = datas;
	    }
    });

    //分类选择
	$(".options ul li").on("click",function(e){
		var typeName = $(this).html();
		type = typeName === '全部' ? '' : typeName;
		$(this).parents(".options").hide().siblings(".dbSlted").children(".dbSltedVal").html(typeName);
	});	
	
	//下拉选择
	$(".s_opt").on("click",function(){
		var thisVal = $(this).attr("thisVal");
		var thisShow = $(this).html();
		$(this).addClass("s_opted").siblings().removeClass("s_opted").parents(".s_opts").hide().siblings(".sItemed").children(".sltedCnt").html(thisShow).siblings(".valInput").val(thisVal);
	})

    // 点击查询事件
	$(".searchBtn").on("click",function(e){
		data.sdate = startDate;
		data.edate = endDate;
		data.type = type;
		
		Ajax(url,data);
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
                if (rs.res.listObjects.length > 0) {
                	res = rs.res.listObjects;
	            	otbody.html(viewList(res));
                	
                	page = rs.res.page;
                	pageSize = rs.res.rows;
	                totalRecords = rs.res.total;
	                totalPages = Math.ceil(totalRecords/pageSize);

	                $(".nodata").hide();
                	$(".tableMain").show();
	                $(".pagesWarp").show();
                }else{
                	// 重置列表数据
                	res = [];
                	
                	$(".nodata").show();
                	$(".tableMain").hide();
                	$(".pagesWarp").hide();

                	// 重置分页数据
                	page = 1;
	                totalPages = 1;
	                totalRecords = 0;	                
                }
                // 分页
                $(".tableList .pages").html(viewPaging(page,pageSize,totalRecords));
        	}
        	// console.log(rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log(errorThrown);
        }
    });
}

function viewList(dataList) {
	var html = '';

	$.each(dataList,function(i,n){
		html += '<tr><td>'+n.cdate.replace(/(\d{4}\-\d{2}\-\d{2})\s(\d+\:\d+)/, "$1<br>$2")+'</td>';
		html += '<td class="tdCnt">'+n.content+'</td>';
		html += '<td>'+n.type+'</td>';
		if (n.attachmentId) {
			// html += '<td><a href="<?= Yii::$app->params["JavaServerHost"];?>/cometotc/account/download/'+n.attachmentId+'" class="linkT1" target="_blank">查看</a></td></tr>';
			html += '<td><a href="<?= Url::to(["message/attach"]);?>?attach_id='+n.attachmentId+'" class="linkT1" target="_blank">查看</a></td></tr>';
		}else{
			html += '<td>暂无</td></tr>';
		}
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

