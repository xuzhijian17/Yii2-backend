<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<div class="colT rowSlt wAuto">
		<ul>
			<li><a href="<?= \yii\helpers\Url::to(['business/add-portfolio']);?>" class="buttonA">新增组合</a></li>
		</ul>
	</div>

	<div class="wrap tableList" style="display: none;">
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
	var url = "<?= \yii\helpers\Url::to(['business/portfolio']);?>";
	var data = {};

	/**
	* 初始化用户列表数据
	*/
	Ajax(url,data);

	/**
     * 上线组合
     */
	$('.tableList').on('click', '.online', function(){
		var PortfolioId = $(this).attr("data-id");
		var status = $(this).attr("data-status");

		url = "<?= \yii\helpers\Url::to(['business/online-portfolio']);?>";
		data = {'PortfolioId':PortfolioId,'Status':status};
		
		$.ajax({
	        type: 'POST',
	        async: true,
	        url: url,
	        data: data,
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){
	        },
	        complete: function(XMLHttpRequest, textStatus){
	        },
	        success: function(rs){
	        	if (rs.error == 0) {
	        		window.location.reload(true);
	        	}
	        	// console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
	});

	/**
     * 删除组合
     */
	$('.tableList').on('click', '.del', function(){
		var t = $(this);
		var id = $(this).attr("data-id");
		
		url = "<?= \yii\helpers\Url::to(['business/del-portfolio']);?>";
		data = {'PortfolioId':id};
		
		$.ajax({
	        type: 'POST',
	        async: true,
	        url: url,
	        data: data,
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){

	        },
	        complete: function(XMLHttpRequest, textStatus){
	        },
	        success: function(rs){
	        	if (rs.error == 0) {
	        		t.parents('ul').fadeOut();
	        	}
	        	// console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
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
		data = {'page':prevPage};

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
		data = {'page':nextPage};

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
        type: 'GET',
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
        	// console.log(rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log(errorThrown);
        }
    });
}


function viewThead(data) {
	var html = '';

	html += '<ul class="thead">';
	html += '<li>组合名称</li>';
	html += '<li>基金名称（代码）</li>';
	html += '<li>基金占比 </li>';
	html += '<li>基金起购（元） </li>';
	html += '<li>组合起购（元） </li>';
	html += '<li>创建时间 </li>';
	html += '<li>修改时间 </li>';
	html += '<li>状态 </li>';
	html += '<li>操作 </li></ul>';

	return html;
}

function viewList(data) {
	var html = '';
	
	html += '<ul><li><a class="linkA inLine" href="<?= \yii\helpers\Url::to(['business/edit-portfolio']);?>?PortfolioId='+data['PortfolioId']+'">'+(data['PortfolioName']?data['PortfolioName']:'-')+'</a></li>';
	html += '<li class="p0">';
	if (data['FundList']) {
		var html1 = '';
		$.each(data['FundList'], function(i, d){
			html1 += '<div class="tdRow double">'+d.fundname+'<br />（'+d.fundcode+'）</div>';
        });
        html += html1;
	}
	html += '</li>';
	html += '<li class="p0">';
	if (data['FundList']) {
		var html1 = '';
		$.each(data['FundList'], function(i, d){
			html1 += '<div class="tdRow">'+d.ratio+'%</div>';
        });
        html += html1;
	}
	html += '</li>';
	html += '<li class="p0">';
	if (data['FundList']) {
		var html1 = '';
		$.each(data['FundList'], function(i, d){
			html1 += '<div class="tdRow">'+d.lowestsumll+'</div>';
        });
        html += html1;
	}
	html += '</li>';
	html += '<li>'+(data['MinSum']?data['MinSum']:'-')+'</li>';
	html += '<li>'+(data['Ctime']?data['Ctime']:'-')+'</li>';
	html += '<li>'+(data['UpdateTime']?data['UpdateTime']:'-')+'</li>';
	html += '<li>'+(data['StatusName']?data['StatusName']:'-')+'</li>';
	html += '<li><a class="linkA inLine online" href="javascript:void(0);" data-id="'+data['PortfolioId']+'" data-status="'+(data['Status']==1?0:1)+'">'+(data['Status']==1?'下线':'上线')+' </a><a class="linkA inLine edit" href="<?= \yii\helpers\Url::to(['business/portfolio-fund-list']);?>?PortfolioId='+data['PortfolioId']+'">编辑</a><a class="linkA inLine del" href="javascript:void(0);"  data-id="'+data['PortfolioId']+'">删除</a></li></ul>';
	
	return html;
}
</script>
