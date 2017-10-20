<?php
use yii\helpers\Url;

$cid = isset(Yii::$app->request->queryParams['cid']) ? Yii::$app->request->queryParams['cid'] : $cid;
$page = isset(Yii::$app->request->queryParams['page']) ? Yii::$app->request->queryParams['page'] : 1;
?>
<section class="banner newsBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<?php foreach ($Categorys as $key => $value): ?> 
				<li><a href="<?= Url::to(['site/news','cid'=>$value['id']]);?>" class="subLink <?= $cid==$value['id']?'cur':'';?>"><?= $value['Category'];?></a></li>
			<?php endforeach;?>
		</ul>
	</div>
	<div class="main_right">
		<?php if(isset($NewsList['list']) && !empty($NewsList['list'])):?>
			<h2 class="cntT"><?= $NewsList['list'][0]['Category'];?></h2>
			<div class="newList">
				<ul>
					<?php foreach ($NewsList['list'] as $key => $value): ?> 
						<li>
							<a href="<?= isset($value['Link']) && !empty($value['Link']) ? $value['Link'] : Url::to(['site/news-detail','id'=>$value['id'],'cid'=>$cid]);?>" class="dbk">
								<span class="newsDate"><?= $value['UpdateTime'];?></span>
								<?= mb_strlen($value['Title'])>40?mb_substr($value['Title'], 0, 40, 'UTF-8').'...':$value['Title'];?>
							</a>
						</li>
					<?php endforeach;?>
				</ul>
			</div>
			<div class="pages" style="display: block;">
				<a href="<?= Url::to(['site/news','cid'=>$cid,'page'=>$NewsList['page']-1>0?$NewsList['page']-1:1]);?>" class="pagePrev page">&nbsp;</a>
				<?php foreach(range(1,$NewsList['totalPages']) as $key=>$value):?>
					<a href="<?= Url::to(['site/news','cid'=>$cid,'page'=>$value]);?>" class="page <?= $page==$value?'cur':'';?>"><?= $value;?></a>
				<?php endforeach;?>
				<a href="<?= Url::to(['site/news','cid'=>$cid,'page'=>$NewsList['page']+1>$NewsList['totalPages']?$NewsList['totalPages']:$NewsList['page']+1]);?>" class="pageNext page">&nbsp;</a>
		    </div>
		<?php endif;?>
	</div>
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
	cid = "<?= $cid;?>";
	if (cid) {
		data.cid = cid;
	}
	// Ajax(url,data);

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


	return html;
}

function viewList(data) {
	var html = '';
	
	return html;
}
</script>
