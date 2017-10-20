<aside>
	<div class="colT asideTab">
		<ul>
			<li>渠道</li>
			<li>
				<div class="itemSelect">
					<select name="" class="asideSelect insitChose">
					<?php if(Yii::$app->admin->isSuperAdmin):?>
						<option value="">全部</option>
					<?php endif;?>
					<?php foreach($instList as $inst):?>
						<?php if($inst['Instid']=='1000'){continue;}?>
						<option value="<?= $inst['Instid'];?>"><?= $inst['InstName'];?></option>
					<?php endforeach;?>
					</select>	
				</div>
			</li>
		</ul>
		<ul>
			<li>绑卡状态</li>
			<li>
				<div class="itemSelect">
					<select name="" class="asideSelect bindStatus">
						<option value="" selected>全部</option>
						<option value="0">未绑</option>
						<option value="1">已绑</option>
					</select>	
				</div>
			</li>
		</ul>
		<ul>
			<?php if(Yii::$app->admin->isSuperAdmin):?>
				<li>注册人数/人<br /><?= $regNum;?></li>
			<?php endif;?>
		</ul>
		<ul>
			<li>绑卡人数/人<br /><span class="bindNum"><?= $bindNum;?></span></li>
		</ul>
	</div>
</aside>
<section class="mine_section">
	<div class="colT query">
		<ul>
			<li>
				<label class="textlabel">姓名</label>
				<div class="item labelItem">
					<div class="itemText">
						<input type="text" name="name" id="" value="" class="textInput name" />
					</div>
				</div>
			</li>
			<li>
				<label class="textlabel">注册号码</label>
				<div class="item labelItem">
					<div class="itemText">
						<input type="text" name="phone" id="" value="" class="textInput phone" />
					</div>
				</div>
			</li>
			<li>
				<label class="textlabel">身份证号</label>
				<div class="item labelItem">
					<div class="itemText">
						<input type="text" name="card" id="" value="" class="textInput card" />
					</div>
				</div>
			</li>
			<li><a href="javascript:void(0);" class="buttonA userSearch">查询</a></li>
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
				<li class="w50"><input type="text" id="pageNum" class="pageNum" value="1"></li>
				<li class="w30">页</li>
				<li class="w65">共<span class="totalPages"></span>页</li>
				<li class="w20"><a href="javascript:void(0);" class="next">&#9658</a></li>
			</ul>
		</div>
	</div>
</section>
<div id="example">
  	<my-component></my-component>
</div>
<script type="application/javascript">
var instid;
var openStatus;
var name = '';
var phone;
var card;
var page;
var totalPages;
var totalRecords;

$(document).ready(function(){
	// 初始化ajax请求参数
	var url = "<?= \yii\helpers\Url::to(['user/index']);?>";
	var data = {};
	/**
	* 初始化用户列表数据
	*/
	Ajax(url,data);
	

	/**
	* 渠道刷选
	*/
    $('.insitChose').on('change', function(){
    	instid = $(this).val();

		data = {'instid':instid,'openStatus':openStatus};

    	// Ajax处理函数
		Ajax(url,data);
		// 获取绑卡人数
		BindNumAjax("<?= \yii\helpers\Url::to(['user/bind-num']);?>",data);
	});

    /**
	* 绑卡状态刷选
	*/
    $('.bindStatus').on('change', function(){
    	openStatus = $(this).val();

		data = {'instid':instid,'openStatus':openStatus};

    	// Ajax处理函数
		Ajax(url,data);
	});

    /**
	* 自定义用户条件搜索
	*/
	$('.userSearch').on('click', function(){
		name = $('.name').val();
		phone = $('.phone').val();
		card = $('.card').val();

		if (!name && !phone && !card) {
			return;
		}

		data = {'instid':instid,'openStatus':openStatus,'name':name,'phone':phone,'card':card};

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

		data = {'instid':instid,'openStatus':openStatus,'name':name,'phone':phone,'card':card,'page':prevPage};

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

		data = {'instid':instid,'openStatus':openStatus,'name':name,'phone':phone,'card':card,'page':nextPage};
		
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
        		console.log(rs);
        	}
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log('Ajax request error!');
        }
    });
}

/**
* BindNumAjax处理函数
*/
function BindNumAjax(url, data) {
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
        		$('.bindNum').text(rs.bindNum);
        	}
        	// console.log(rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log('Ajax request error!');
        }
    });
}

function viewThead(data) {
	var html = '';

	html += '<ul class="thead">';
	html += '<li>渠道</li>';
	html += '<li>姓名</li>';
	//html += '<li>地区</li>';
	html += '<li>注册电话</li>';
	html += '<li>预留电话</li>';
	html += '<li>身份证 </li>';
	html += '<li>注册时间 </li>';
	html += '<li>绑卡时间 </li>';
	html += '<li>操作 </li></ul>';

	return html;
}

function viewList(data) {
	var html = '';
	
	html += '<ul>';
	html += '<li>'+(data['InstName']?data['InstName']:'-')+'</li>';
	html += '<li>'+(data['Name']?data['Name']:'-')+'</li>';
	// html += '<li>-</li>';
	html += '<li>'+(data['RegPhone']?data['RegPhone']:'-')+'</li>';
	html += '<li>'+(data['BindPhone']?data['BindPhone']:'-')+'</li>';
	html += '<li>'+(data['CardID']?data['CardID']:'-')+'</li>';
	html += '<li>'+(data['SysTime']?data['SysTime']:'-')+'</li>';
	html += '<li>'+(data['BindTime']?data['BindTime']:'-')+'</li>';
	html += '<li><a href="<?= \yii\helpers\Url::to(['user/detail']);?>?uid='+data.id+'&instid='+data['Instid']+'" class="linkA" target="_blank">资料</a><a href="<?= \yii\helpers\Url::to(['position/index']);?>?uid='+data.id+'&instid='+data['Instid']+'&type=0" class="linkA" target="_blank">持仓</a></li>';
	html += '</ul>';
	
	return html;
}
	
</script>