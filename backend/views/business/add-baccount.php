<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<form action="#" id="baccount">	
		<table border="0" cellspacing="1" cellpadding="0" class="table editTable">
			<tr>
				<td width="250" class="talC" align="center">企业名称</td>
				<td class="editTitle"><input type="" name="CompanyName" id="CompanyName" value="<?= isset($baccountData['CompanyName'])?$baccountData['CompanyName']:'';?>" placeholder="请输入企业名称" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="250" class="talC" align="center">营业执照号</td>
				<td class="editTitle"><input type="" name="BusinessLicence" id="BusinessLicence" value="<?= isset($baccountData['BusinessLicence'])?$baccountData['BusinessLicence']:'';?>" placeholder="请输入营业执照号" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="250" class="talC" align="center">基金账号</td>
				<td class="editTitle"><input type="" name="FundAcco" id="FundAcco" value="<?= isset($baccountData['FundAcco'])?$baccountData['FundAcco']:'';?>" placeholder="请输入基金账号" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="250" class="talC" align="center">交易账号</td>
				<td class="editTitle"><input type="" name="TradeAcco" id="TradeAcco" value="<?= isset($baccountData['TradeAcco'])?$baccountData['TradeAcco']:'';?>" placeholder="请输入交易账号" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="250" class="talC" align="center">交易密码</td>
				<td class="editTitle"><input type="password" name="TradePass" id="TradePass" value="<?= isset($baccountData['TradePass'])?$baccountData['TradePass']:'';?>" placeholder="请输入交易密码" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="250" class="talC" align="center">法人姓名</td>
				<td class="editTitle"><input type="" name="ArtificialPerson" id="ArtificialPerson" value="<?= isset($baccountData['ArtificialPerson'])?$baccountData['ArtificialPerson']:'';?>" placeholder="请输入法人姓名" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="250" class="talC" align="center">法人身份证号</td>
				<td class="editTitle"><input type="" name="ArtificialPersonCard" id="ArtificialPersonCard" value="<?= isset($baccountData['ArtificialPersonCard'])?$baccountData['ArtificialPersonCard']:'';?>" placeholder="请输入法人身份证号" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="250" class="talC" align="center">经办人姓名</td>
				<td class="editTitle"><input type="" name="OperatorName" id="OperatorName" value="<?= isset($baccountData['OperatorName'])?$baccountData['OperatorName']:'';?>" placeholder="请输入经办人姓名" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="250" class="talC" align="center">经办人身份证号</td>
				<td class="editTitle"><input type="" name="OperatorCard" id="OperatorCard" value="<?= isset($baccountData['OperatorCard'])?$baccountData['OperatorCard']:'';?>" placeholder="请输入经办人身份证号" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="250" class="talC" align="center">银行名称</td>
				<td class="editTitle"><input type="" name="BankName" id="BankName" value="<?= isset($baccountData['BankName'])?$baccountData['BankName']:'';?>" placeholder="请输入银行名称" class="inputTitle" /></td>
			</tr>
			<tr>
				<td width="250" class="talC" align="center">银行卡号</td>
				<td class="editTitle"><input type="" name="BankAcco" id="BankAcco" value="<?= isset($baccountData['BankAcco'])?$baccountData['BankAcco']:'';?>" placeholder="请输入银行账号" class="inputTitle" /></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<a href="javascript:void(0);" class="buttonA sub-btn">提交</a>
				</td>
			</tr>
		</table>
	</form>
</section>
<script type="text/javascript">
$(document).ready(function(){
	var url = "<?= isset($baccountData)?\yii\helpers\Url::to(['business/edit-baccount']):\yii\helpers\Url::to(['business/add-baccount']);?>";
	var data = {};

	$('.sub-btn').on('click', function(){
		data = $("form#baccount").serializeArray();
        
		if (!data) {
			return;
		}

        var uid = $_GET['uid'];
        if (uid) {
            data.push({'name':'uid','value':uid});
        }
        // console.log(data);
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
        		window.location.href = "<?= \yii\helpers\Url::to(['business/baccount']);?>";
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
</script>