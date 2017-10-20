<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<form action="#" id="portfolio-fund" name="portfolio-fund">
		<h2 class="bT">添加基金</h2>
		<table border="0" cellspacing="1" cellpadding="0" class="table addTable">
			<tr>
				<td width="150" class="talC" align="center">基金代码</td>
				<td class="editTitle">
					<div class="wbAuto">
						<input type="" name="" id="" value="" placeholder="请输入基金代码" class="inputTitle fundName fundCode" />							
					</div>
				</td>
			</tr>
			<tr>
				<td width="150" class="talC" align="center">基金占比（%）</td>
				<td class="editTitle">
					<div class="wbAuto">
						<input type="" name="" id="" value="" placeholder="" class="inputTitle fundScale" />
					</div>
				</td>
			</tr>
			<tr>
				<td width="150" class="talC" align="center">推荐理由</td>
				<td class="editTitle">
					<div class="wbAuto">
						<input type="" name="" id="" value="" placeholder="请输入50以内字数" class="inputTitle fundReason" />
					</div>
				</td>
			</tr>
			<tr>
				<td width="150" class="talC" align="center" colspan="2"><a href="javascript:void(0);" class="buttonA opAdd">添加</a></td>
			</tr>
		</table>
	</form>
	

	<form action="#" id="portfolio-fund-list" name="portfolio-fund-list">
		<div class="tableList wrap" style="display: none">
			<h2 class="bT">基金列表</h2>
			<table border="0" cellspacing="1" cellpadding="0" class="table editTable fundList">
				
			</table>
		</div>
	</form>
</section>

<!-- 编辑弹框 -->
<div class="popUp addJJPop">
	<h2 class="popH2">
		编辑基金
		<a class="closePop" href="javascript:void(0);">&#10005</a>
	</h2>
	<div class="popCnt">
		<table border="0" cellspacing="1" cellpadding="0" class="table editTable">
			<tr>
				<td width="150" class="talC" align="center">基金代码</td>
				<td class="editTitle">
					<div class="wbAuto">
						<input type="" name="" id="" value="" placeholder="请输入基金代码" class="inputTitle edit-fund fundCode" />							
					</div>
				</td>
			</tr>
			<tr>
				<td width="150" class="talC" align="center">基金占比（%）</td>
				<td class="editTitle">
					<div class="wbAuto">
						<input type="" name="" id="" value="" placeholder="" class="inputTitle edit-ratio" />
					</div>
				</td>
			</tr>
			<tr>
				<td width="150" class="talC" align="center">推荐理由</td>
				<td class="editTitle">
					<div class="wbAuto">
						<input type="" name="" id="" value="" placeholder="请输入50以内字数" class="inputTitle edit-reason" />
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="popBut">
		<a href="javascript:void(0);" class="buttonA opEdit">提交</a>
	</div>
</div>
<div class="mask"></div>
<link rel="stylesheet" href="<?= \yii\helpers\Url::base();?>/css/jquery-ui.min.css">
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.core.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.widget.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.position.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.menu.min.js"></script>
<script src="<?= \yii\helpers\Url::base();?>/js/jquery.ui.autocomplete.min.js"></script>
<script type="text/javascript">
var availableTags = [];
var minpurchaseamount;
var PortfolioId;
var tr;

$(document).ready(function(){
	var url = "<?= \yii\helpers\Url::to(['business/portfolio-fund-list']);?>";
	var data = {};

	/**
	* 初始化组合基金列表数据
	*/
	hsFundList("<?= \yii\helpers\Url::to(['business/hs-fund-list']);?>");	// 获取可购买基金列表数据（用于基金代码自动补全）
	
	PortfolioId = $_GET['PortfolioId'];
	if (PortfolioId) {
    	data.PortfolioId=PortfolioId;
    }
	Ajax(url,data);
	
	// 基金添加
	$(".opAdd").on("click",function(){
		var fundfullname = $(".addTable .fundCode").val();

		if (!fundfullname) {
			return;
		}

		var fundCode = fundfullname;
		var fundName = '';
		if (fundfullname.length > 6) {
			fundCode = fundfullname.substr(0,6);
			fundName = fundfullname.slice(7,-1);
		}
		
		var fundScale = Number($(".fundScale").val());
		var fundReason = $(".fundReason").val();
		var fundLowerPriceSum = 0;
		var isFundfullname = 0;

		if (!fundfullname || !fundScale) {
			alert("基金代码或基金占比不能为空")
			return;
		}

		getLowestSumLL("<?= \yii\helpers\Url::to(['business/get-lowest-sumll']);?>",{'fundCode':fundCode});	// 获取组合基金的起购金额
		var data = {'fundname':fundName,'fundcode':fundCode,'ratio':fundScale,'minpurchaseamount':minpurchaseamount,'reason':fundReason};
		var i = $(".fundList tr:not(:first,:last)").length;
		var listHtml = viewList(data,i);
		
		if(fundScale.toString()=="NaN"){
			alertBox("基金占比必须为数字","我知道了","alertBtnId")
			return false;
		}
		
		$(".fundScaleRst").each(function(){
			fundLowerPriceSum+=Number($(this).html());
		});

		$(".fundList input.fundcode").each(function(){
			if (fundCode == $(this).val()) {
				isFundfullname += 1;
			}
		});

		if(fundScale+fundLowerPriceSum>100){
			alertBox("基金占比不能大于100%","我知道了","alertBtnId");
		}else if (isFundfullname > 0) {
			alertBox("基金代码重复","我知道了","alertBtnId");
		}else{
			$(".fundList tr:first").after(listHtml);
			$('.wrap').show();
			$("form#portfolio-fund").get(0).reset();
		}

		
	});

	// 基金修改
	$(".opEdit").on("click",function(){
		var fundfullname = $(".editTable .fundCode").val();

		if (!fundfullname) {
			return;
		}

		var fundCode = tr.find("input.fundcode").val();
		var fundName = tr.find("input.fundname").val();
		
		var fundScale = Number($(".addJJPop .edit-ratio").val());
		var fundReason = $(".addJJPop .edit-reason").val();
		var fundLowerPriceSum = 0;
		
		if(fundScale.toString()=="NaN"){
			alertBox("基金占比必须为数字","我知道了","alertBtnId")
			return false;
		}
		
		$(".fundScaleRst").each(function(){
			fundLowerPriceSum+=Number($(this).html());
		})
		if(fundLowerPriceSum-fundScale>100){
			alertBox("基金占比不能大于100%","我知道了","alertBtnId")
			//console.log(fundScale+fundLowerPriceSum);
		}else{
			tr.find("td.fundfullname").text(fundfullname);
			tr.find("input.fundname").val(fundName);
			tr.find("input.fundcode").val(fundCode);

			tr.find("span.fundScaleRst").text(fundScale);
			tr.find("input.ratio").val(fundScale);

			tr.find("td.reason").text(fundReason);
			tr.find("input.reason").val(fundReason);

			// alertBox("基金修改完成","我知道了","alertBtnId")
			$(".addJJPop,.mask").hide();
		}
	});

	// 基金编辑
	$(document).on("click",".editJJ",function(){
		tr = $(this).parents("tr");
		$(".addJJPop .edit-fund").val(tr.find("td.fundfullname").text());
		$(".addJJPop .edit-ratio").val(tr.find("input.ratio").val());
		$(".addJJPop .edit-reason").val(tr.find("input.reason").val());

		$(".addJJPop,.mask").show();
	});

	// 基金删除
	$(document).on("click",".delLine",function(){
		$(this).parents("tr").remove();
	});

	/**
	* 提交组合基金列表
	*/
	$('form#portfolio-fund-list').on('click', '.submit-portfolio-fund', function(){	
		url = "<?= \yii\helpers\Url::to(['business/add-portfolio-fund']);?>";
		data = $("form#portfolio-fund-list").serializeArray();
		
	    if (PortfolioId) {
	    	data.push({'name':'PortfolioId','value':PortfolioId});
	    }

	    var fundLowerPriceSum = 0;
	    $(".fundScaleRst").each(function(){
			fundLowerPriceSum+=Number($(this).html());
		});

		if(fundLowerPriceSum!=100){
			alertBox("基金占比不等于100%","我知道了","alertBtnId");
			return;
		}

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
	        		alertBox("提交成功","我知道了","alertBtnId",function(alertBtnId){
	        			window.location.href = "<?= \yii\helpers\Url::to(['business/portfolio']);?>";
	        		});
	        	}else{
	        		alert(rs.message);
	        	}
	        	// console.log(rs);
	        },
	        error:function(XMLHttpRequest, textStatus, errorThrown){
	            console.log(errorThrown);
	        }
	    });
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
        	$('.tableList .fundList').empty();
        	$('.tableList .fundList').get(0).appendChild(spinner.el);
        },
        complete: function(XMLHttpRequest, textStatus){
        	spinner.stop();
        },
        success: function(rs){
        	if (rs.error == 0) {
        		var list = '';
        		$.each(rs.list, function(i, data){
        			list += viewList(data,i)
                });
                var tableList = viewThead()+list+viewButton();
                $('.tableList .fundList').html(tableList);

                if (rs.list.length > 0) {
	                $('.wrap').show();
                }
        	}
        	// console.log(rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log(errorThrown);
        }
    });
}

/**
* 起购金额
*/
function getLowestSumLL(url,data) {
	$.ajax({
        type: 'POST',
        async: false,
        url: url,
        data: data,
        dataType: 'json',
        beforeSend: function(XMLHttpRequest){
        },
        complete: function(XMLHttpRequest, textStatus){
        },
        success: function(rs){
        	if (rs.error == 0) {
        		minpurchaseamount = rs.minpurchaseamount.toFixed(2);
        	}
        	// console.log(rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log(errorThrown);
        }
    });
}

/**
* 自动补全
*/
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
			$( ".fundCode" ).autocomplete({
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

	html += '<tr><td align="center">基金名称（代码）</td>';
	html += '<td align="center">基金占比</td>';
	html += '<td align="center">基金起购（元）</td>';
	html += '<td align="center" width="300">推荐理由</td>';
	html += '<td align="center">操作</td></tr>';
	
	return html;
}

function viewList(data,i) {
	var html = '';
	
	html += '<tr><td align="center" class="fundfullname">'+(data['fundname']+'('+data['fundcode']+')')+'</td>';
	html += '<input type="hidden" class="fundname" name="'+i+'[fundname]" value="'+data['fundname']+'">';
	html += '<input type="hidden" class="fundcode" name="'+i+'[fundcode]" value="'+data['fundcode']+'">';
	html += '<td align="center"><span class="fundScaleRst">'+data['ratio']+'</span>%</td>';
	html += '<input type="hidden" class="ratio" name="'+i+'[ratio]" value="'+(data['ratio']?data['ratio']:'')+'">';
	html += '<td align="center">'+(data['minpurchaseamount']?data['minpurchaseamount']:'-')+'</td>';
	html += '<input type="hidden" class="minpurchaseamount" name="'+i+'[minpurchaseamount]" value="'+(data['minpurchaseamount']?data['minpurchaseamount']:'')+'">';
	html += '<td align="center" class="reason">'+(data['reason']?data['reason']:'')+'</td>';
	html += '<input type="hidden" class="reason" name="'+i+'[reason]" value="'+(data['reason']?data['reason']:'')+'">';
	html += '<td align="center"><a href="javascript:void(0);" class="linkA inLine editJJ">编辑</a><a href="javascript:void(0);" class="linkA inLine delLine">删除</a></td></tr>';

	return html;
}

function viewButton(argument) {
	var html = '';

	html += '<tr><td class="talC" align="center" colspan="5"><a href="javascript:void(0);" class="buttonA submit-portfolio-fund">提交</a></td></tr>';

	return html;
}
</script>




















