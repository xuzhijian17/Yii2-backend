<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<div class="addOne colT">
		<ul>
			<li>
				<div class="item labelItem">
					<div class="itemText">
						<input type="text" name="" id="fundCode" value="" class="textInput fundCode" placeholder="请输入基金代码" />
					</div>
				</div>
			</li>
			<li><a href="javascript:void(0);" class="buttonA addFund">添加</a></li>
		</ul>
	</div>
	<!--addOne-->
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
</section>
<link rel="stylesheet" href="<?= \yii\helpers\Url::base();?>/css/jquery-ui.min.css">
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.core.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.widget.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.position.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.menu.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.autocomplete.min.js"></script>
<script type="application/javascript">
var instid;
var tradeType;
var tradeStatus;
var fundCode;
var fundName;
var name;
var phone;
var card;
var page;
var totalPages;
var totalRecords;
var availableTags = [];
var clickStatus = true;
var type = '<?= $type;?>';	// 1-theme 2-fundtype 3-hot
var id = '<?= $id;?>';	// Type id，maybe is ThemeId or CategoryId, according to type field

$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['business/fund-list']);?>";
	var data = {'id':id,'type':type};

	/**
	* 初始化用户列表数据
	*/
	hsFundList("<?= \yii\helpers\Url::to(['business/hs-fund-list']);?>");	// 获取可购买基金列表数据（用于基金代码自动补全）
	Ajax(url,data);
	

    /**
	* 添加基金
	*/
	$('.addFund').on('click', function(){
		var fundfullname = $('.fundCode').val();

		if (!clickStatus) {
			return;
		}

		if (!fundfullname) {
			return;
		}

		if (fundfullname.length > 6) {
			fundCode = fundfullname.substr(0,6);
			fundName = fundfullname.slice(7,-1);
		}else{
			fundCode = fundfullname;
			fundName = '';
		}
		

		// 请求参数
		data = {'fundCode':fundCode,'fundName':fundName,'id':id,'type':type};
		url = "<?= \yii\helpers\Url::to(['business/add-fund']);?>";

		$.ajax({
	        type: 'POST',
	        async: true,
	        url: url,
	        data: data,
	        dataType: 'json',
	        beforeSend: function(XMLHttpRequest){
	        	clickStatus = false;
	        },
	        complete: function(XMLHttpRequest, textStatus){
	        	clickStatus = true;
	        },
	        success: function(rs){
	        	if (rs.error == 0) {
	        		window.location.reload(true);
	        	}else{
	        		alert(rs.message);
	        	}
	        	
	        	console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
	});

	/**
     * 更新基金
     */
	$('.tableList').on('click', '.update', function(){
		var t = $(this);
		var id = $(this).attr("data-id");
		var tags = $(this).parents('ul').find("input[name='tags']").val();

		if (!tags) {
			return;
		}
		
		url = "<?= \yii\helpers\Url::to(['business/update-fund']);?>";
		data = {'id':id,'tags':tags};
		
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
     * 删除基金
     */
	$('.tableList').on('click', '.del', function(){
		var t = $(this);
		var id = $(this).attr("data-id");
		
		url = "<?= \yii\helpers\Url::to(['business/del-fund']);?>";
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
     * 置顶基金
     */
	$('.tableList').on('click', '.istop', function(){
		var id = $(this).attr("data-id");
		var istop = $(this).attr("data-istop");

		url = "<?= \yii\helpers\Url::to(['business/top-fund']);?>";
		data = {'id':id,'istop':istop};
		
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
     * 推荐基金
     */
	$('.tableList').on('click', '.recommend', function(){
		var id = $(this).attr("data-id");
		var recommend = $(this).attr("data-recommend");

		url = "<?= \yii\helpers\Url::to(['business/recommend']);?>";
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
	$('.tableList').on('click', '.prev', function(){
		var prevPage = Number(page) - 1;

		if (prevPage < 1) {
			return;
		}
		// 请求参数
		data = {'id':id,'type':type,'page':prevPage};

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
		data = {'id':id,'type':type,'page':nextPage};

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
            console.log('Ajax request error!');
        }
    });
}

function hsFundList(url) {
	$.ajax({
        type: 'POST',
        async: false,
        url: url,
        data: {},
        dataType: 'json',
        beforeSend: function(XMLHttpRequest){

        },
        complete: function(XMLHttpRequest, textStatus){
        	// 基金代码自动补全
			$( "#fundCode" ).autocomplete({
				source: availableTags
			});
        },
        success: function(rs){
        	if (rs.error == 0) {
        		$.each(rs.list, function(i, data){
        			availableTags.push(data['fundcode']+'('+data['fundname']+')');
                });
        	}
        	// console.log(rs,availableTags);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log(errorThrown);
        }
    });
}

function viewThead(data) {
	var html = '';

	// 1-主题 2-类型 3-热销 空或0为默认
	if (type == '1') {
		html += '<ul class="thead">';
		html += '<li>基金名称</li>';
		html += '<li>基金代码 </li>';
		html += '<li>上传时间 </li>';
		html += '<li>更新时间 </li>';
		html += '<li>标签 </li>';
		html += '<li>操作 </li></ul>';
	}else if (type == '2') {
		html += '<ul class="thead">';
		html += '<li>基金名称</li>';
		html += '<li>基金代码 </li>';
		html += '<li>上传时间 </li>';
		html += '<li>更新时间 </li>';
		html += '<li>标签 </li>';
		html += '<li>操作 </li></ul>';
	}else if (type == '3') {
		html += '<ul class="thead">';
		html += '<li>基金名称</li>';
		html += '<li>基金代码 </li>';
		html += '<li>上传时间 </li>';
		html += '<li>更新时间 </li>';
		html += '<li>是否推荐 </li>';
		html += '<li>标签 </li>';
		html += '<li>操作 </li></ul>';
	}else{
		html += '<ul class="thead">';
		html += '<li>基金名称</li>';
		html += '<li>基金代码 </li>';
		html += '<li>上传时间 </li>';
		html += '<li>更新时间 </li>';
		html += '<li>是否推荐 </li>';
		html += '<li>操作 </li></ul>';
	}

	return html;
}

function viewList(data) {
	var html = '';
	
	// 1-主题 2-类型 3-热销 空或0为默认
	if (type == '1') {
		html += '<ul><li>'+(data['FundName']?data['FundName']:'-')+'</li>';
		html += '<li>'+(data['FundCode']?data['FundCode']:'-')+'</li>';
		html += '<li>'+(data['InsertTime']?data['InsertTime']:'-')+'</li>';
		html += '<li>'+(data['UpdateTime']?data['UpdateTime']:'-')+'</li>';
		html += '<li><input type="text" name="tags" class="tags" value="'+(data['Tags']?data['Tags']:'')+'" placeholder="请输入标签"></li>';
		html += '<li><a class="linkA inLine update" href="javascript:void(0);" data-id="'+data['id']+'">更新</a><a class="linkA inLine del" href="javascript:void(0);" data-id="'+data['id']+'">删除</a><a class="linkA inLine istop" href="javascript:void(0);" data-id="'+data['id']+'" data-istop="'+(data['IsTop']==1?0:1)+'">'+(data['IsTop']==1?'取消置顶':'置顶')+'</a></li></ul>';
	}else if (type == '2') {
		html += '<ul><li>'+(data['FundName']?data['FundName']:'-')+'</li>';
		html += '<li>'+(data['FundCode']?data['FundCode']:'-')+'</li>';
		html += '<li>'+(data['InsertTime']?data['InsertTime']:'-')+'</li>';
		html += '<li>'+(data['UpdateTime']?data['UpdateTime']:'-')+'</li>';
		html += '<li><div class="item tagItem ciTalC"><div class="itemText"><input type="text" name="tags" class="textInput tags" value="'+(data['Tags']?data['Tags']:'')+'" placeholder="请输入标签"></div></div></li>';
		html += '<li><a class="linkA inLine update" href="javascript:void(0);" data-id="'+data['id']+'">更新</a><a class="linkA inLine del" href="javascript:void(0);" data-id="'+data['id']+'">删除</a><a class="linkA inLine istop" href="javascript:void(0);" data-id="'+data['id']+'" data-istop="'+(data['IsTop']==1?0:1)+'">'+(data['IsTop']==1?'取消置顶':'置顶')+'</a></li></ul>';
	}else if (type == '3') {
		html += '<ul><li>'+(data['FundName']?data['FundName']:'-')+'</li>';
		html += '<li>'+(data['FundCode']?data['FundCode']:'-')+'</li>';
		html += '<li>'+(data['InsertTime']?data['InsertTime']:'-')+'</li>';
		html += '<li>'+(data['UpdateTime']?data['UpdateTime']:'-')+'</li>';
		html += '<li>'+(data['RecommendName']?data['RecommendName']:'-')+'</li>';
		html += '<li><div class="item tagItem ciTalC"><div class="itemText"><input type="text" name="tags" class="textInput tags" value="'+(data['Tags']?data['Tags']:'')+'" placeholder="请输入标签"></div></div></li>';
		html += '<li><a class="linkA inLine update" href="javascript:void(0);" data-id="'+data['id']+'">更新</a><a class="linkA inLine recommend" href="javascript:void(0);" data-id="'+data['id']+'" data-recommend="'+(data['Recommend']==1?0:1)+'">'+(data['Recommend']==1?'取消推荐':'推荐')+'</a><a class="linkA inLine del" href="javascript:void(0);" data-id="'+data['id']+'">删除</a></li></ul>';
	}else{
		html += '<ul><li>'+(data['FundName']?data['FundName']:'-')+'</li>';
		html += '<li>'+(data['FundCode']?data['FundCode']:'-')+'</li>';
		html += '<li>'+(data['InsertTime']?data['InsertTime']:'-')+'</li>';
		html += '<li>'+(data['UpdateTime']?data['UpdateTime']:'-')+'</li>';
		html += '<li>'+(data['RecommendName']?data['RecommendName']:'-')+'</li>';
		html += '<li><a class="linkA inLine recommend" href="javascript:void(0);" data-id="'+data['id']+'" data-recommend="'+(data['Recommend']==1?0:1)+'">'+(data['Recommend']==1?'取消推荐':'推荐')+'</a><a class="linkA inLine del" href="javascript:void(0);" data-id="'+data['id']+'">删除</a></li></ul>';
	}
	
	
	return html;
}
</script>
