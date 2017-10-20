<?php
echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);
?>
<section class="mine_section">
	<div class="colT rowSlt wAuto">
		<ul>
			<li><a href="<?= \yii\helpers\Url::to(['business/add-news','cid'=>$cid]);?>" class="buttonA">新增资讯</a></li>
		</ul>
	</div>

	<div class="wrap tableList" style="display: none">
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
	<!--总量统计-->
	
</section>
<script type="application/javascript">
var cid;
var page;
var totalPages;
var totalRecords;

$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['business/news']);?>";
	var data = {};

	/**
	* 初始化请求数据
	*/
	cid = $_GET['cid'];
	if (cid) {
		data.cid = cid;
	}
	Ajax(url,data);

    /**
     * 删除资讯
     */
	$('.tableDiv').on('click', '.del', function(){
		var t = $(this);
		var id = $(this).attr("data-id");
		
		url = "<?= \yii\helpers\Url::to(['business/del-news']);?>";
		data = {'id':id};
		
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
	        	console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
	});

	/**
     * 推荐资讯
     */
	$('.tableList').on('click', '.recommend', function(){
		var id = $(this).attr("data-id");
		var recommend = $(this).attr("data-recommend");

		url = "<?= \yii\helpers\Url::to(['business/recommend-news']);?>";
		data = {'id':id,'recommend':recommend};
		
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
	        	console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
	});

	/**
	* 上一页
	*/
	$('.pages').on('click', '.prev', function(){
		var prevPage = Number(page) - 1;

		if (prevPage < 1 || !cid) {
			return;
		}
		// 请求参数
		data = {'cid':cid,'page':prevPage};

		// Ajax处理函数
		Ajax(url, data);
	});

	/**
	* 下一页
	*/
	$('.pages').on('click', '.next', function(){
		var nextPage = Number(page) + 1;

		if (nextPage > Number(totalPages) || !cid) {
			return;
		}
		// 请求参数
		data = {'cid':cid,'page':nextPage};

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
	                $('.tableList .pages').show();
                }else{
                	// 重置分页数据
                	page = 1;
	                totalPages = 1;
	                totalRecords = 1;
	                
	                $('.tableList .tableDiv').html(viewEmpty());
	                $('.tableList .pages').hide();
                }
        	}else{
        		alert(rs.message);
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
	html += '<li class="wb25">标题</li>';
	html += '<li class="wb25">更新时间 </li>';
	html += '<li class="wb25">上传时间</li>';
	html += '<li class="wb25">是否推荐</li>';
	html += '<li class="wb25">操作 </li></ul>';

	return html;
}

function viewList(data) {
	var html = '';
	
	html += '<ul><li>'+(data['Title']?data['Title'].substr(0,20):'-')+'</li>';
	html += '<li>'+(data['UpdateTime']?data['UpdateTime']:'-')+'</li>';
	html += '<li>'+(data['InsertTime']?data['InsertTime']:'-')+'</li>';
	html += '<li>'+(data['RecommendName']?data['RecommendName']:'')+'</li>';
	html += '<li><a class="linkA inLine recommend" href="javascript:void(0);" data-id="'+data['id']+'" data-recommend="'+(data['Recommend']==1?0:1)+'">'+(data['Recommend']==1?'取消推荐':'推荐')+'</a><a class="linkA" href="<?= \yii\helpers\Url::to(['business/edit-news']);?>?cid='+cid+'&id='+(data['id']?data['id']:'0')+'">编辑</a><a class="linkA inLine del" href="javascript:void(0);" data-id="'+data['id']+'">删除</a></li></ul>';
	
	return html;
}
</script>