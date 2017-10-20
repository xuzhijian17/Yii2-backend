<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<div class="colT query">
		<ul>
			<li>
				<label class="textlabel">联系人</label>
				<div class="item labelItem">
					<div class="itemText">
						<input type="text" name="name" id="" value="" class="textInput name" />
					</div>
				</div>
			</li>
			<li>
				<label class="textlabel">联系电话</label>
				<div class="item labelItem">
					<div class="itemText">
						<input type="text" name="phone" id="" value="" class="textInput phone" />
					</div>
				</div>
			</li>
			<li>
				<label class="textlabel">公司名称</label>
				<div class="item labelItem">
					<div class="itemText">
						<input type="text" name="company" id="" value="" class="textInput company" />
					</div>
				</div>
			</li>
			<li><a href="javascript:void(0);" class="buttonA Search">查询</a></li>
		</ul>
	</div>
	<!--query end--> 

	<div class="tableList wrap" style="display: none">
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
<script type="application/javascript">
var name = '';
var phone;
var company;
var page;
var totalPages;
var totalRecords;

$(document).ready(function(){
	// 初始化ajax请求参数
	var url = "<?= \yii\helpers\Url::to(['business/cooperation']);?>";
	var data = {};
	/**
	* 初始化用户列表数据
	*/
	Ajax(url,data);
	

    /**
	* 自定义用户条件搜索
	*/
	$('.Search').on('click', function(){
		name = $('.name').val();
		phone = $('.phone').val();
		company = $('.company').val();

		if (!name && !phone && !company) {
			return;
		}

		data = {'name':name,'phone':phone,'company':company};

    	// Ajax处理函数
		Ajax(url,data);
	});

	/**
	* 上一页
	*/
	$('.prev').on('click', function(){
		var prevPage = Number(page) - 1;

		if (prevPage < 1) {
			return;
		}

		data = {'name':name,'phone':phone,'company':company,'page':prevPage};

    	// Ajax处理函数
		Ajax(url,data);
	});

	/**
	* 下一页
	*/
	$('.next').on('click', function(){
		var nextPage = Number(page) + 1;

		if (nextPage > Number(totalPages)) {
			return;
		}

		data = {'name':name,'phone':phone,'company':company,'page':nextPage};
		
    	// Ajax处理函数
		Ajax(url,data);
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
                	var userList = '';
	        		$.each(rs.list, function(i, data){
	        			userList += viewList(data)
	                });
	                var userTable = viewThead()+userList;
	                $('.tableList .tableDiv').html(userTable);

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
        	}else{
        		console.log(rs);
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
	html += '<li>联系人</li>';
	html += '<li>联系电话</li>';
	html += '<li>公司名称</li>';
	html += '<li>申请时间 </li>';
	// html += '<li>操作 </li></ul>';
	html += '</ul>';

	return html;
}

function viewList(data) {
	var html = '';
	
	html += '<ul>';
	html += '<li>'+(data['Contact']?data['Contact']:'-')+'</li>';
	html += '<li>'+(data['Phone']?data['Phone']:'-')+'</li>';
	html += '<li>'+(data['Company']?data['Company']:'-')+'</li>';
	html += '<li>'+(data['InsertTime']?data['InsertTime']:'-')+'</li>';
	// html += '<li><a class="linkA inLine del" href="javascript:void(0);" data-id="'+data['id']+'">删除</a></li>';
	html += '</ul>';
	
	return html;
}
	
</script>