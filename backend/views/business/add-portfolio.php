<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<form action="#" id="portfolio">
		<h2 class="bT">组合名称</h2>
		<div class="addOne colT">
			<ul>
				<li>
					<div class="item labelItem">
						<div class="itemText">
							<input type="text" name="PortfolioName" id="PortfolioName" value="<?= isset($portfolioData['PortfolioName'])?$portfolioData['PortfolioName']:'';?>" class="textInput" placeholder="请输入组合名称" />
						</div>
					</div>
				</li>
			</ul>
		</div>
		
		<h2 class="bT">组合介绍</h2>
		<table border="0" cellspacing="1" cellpadding="0" class="table editTable">
			<tr>
				<td class="editTitle">
					<div class="textArea">
						<textarea class="" cols="" rows="" name="OtherInfo" id="OtherInfo"><?= isset($portfolioData['OtherInfo'])?$portfolioData['OtherInfo']:'';?></textarea>
					</div>
				</td>
			</tr>
			<tr>
				<td class="editTitle">
					<div class="colT">
						<ul>
							<li>
								<label class="textlabel">低</label>
								<div class="item labelItem">
									<div class="itemText">
										<input type="text" name="low" id="low" value="<?= isset($portfolioData['ExpectInfo']['low'])?$portfolioData['ExpectInfo']['low']:'';?>" class="textInput" placeholder="预期最低收益" />
									</div>
								</div>
							</li>
							<li>
								<label class="textlabel">中</label>
								<div class="item labelItem">
									<div class="itemText">
										<input type="text" name="mid" id="mid" value="<?= isset($portfolioData['ExpectInfo']['mid'])?$portfolioData['ExpectInfo']['mid']:'';?>" class="textInput" placeholder="预期平均收益" />
									</div>
								</div>
							</li>
							<li>
								<label class="textlabel">高</label>
								<div class="item labelItem">
									<div class="itemText">
										<input type="text" name="high" id="high" value="<?= isset($portfolioData['ExpectInfo']['high'])?$portfolioData['ExpectInfo']['high']:'';?>" class="textInput" placeholder="预期最高收益" />
									</div>
								</div>
							</li>
						</ul>
					</div>
				</td>
			</tr>
		</table>
		<div class="buttonLine">
			<a href="javascript:void(0);" class="buttonA submit-portfolio">确认提交</a>				
		</div>
	</form>
</section>
<script type="text/javascript">
$(document).ready(function(){
	var url = "<?= empty($portfolioData)?\yii\helpers\Url::to(['business/add-portfolio']):\yii\helpers\Url::to(['business/edit-portfolio']);?>";
	var data = {};

	$('#portfolio .submit-portfolio').on('click', function(){
		if (!$('#PortfolioName').val()) {
			alert("基金名称不能为空")
			return;
		}

		data = $("form#portfolio").serializeArray();

		var PortfolioId = $_GET['PortfolioId'];
	    if (PortfolioId) {
	    	// data.PortfolioId = PortfolioId;
	    	data.push({'name':'PortfolioId','value':PortfolioId});
	    }

		Ajax(url,data);
	});
});

/**
* Ajax处理函数
*/
function Ajax(url, data) {
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
}
</script>