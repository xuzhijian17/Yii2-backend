<?php
echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);
?>
<section class="mine_section">
	<form action="<?= \yii\helpers\Url::to(['business/update-fund-info']);?>" name="fundInfo" method="post">
	<table border="0" cellspacing="1" cellpadding="0" class="table editTable">
		<tr>
			<td width="150" class="talC" align="center">尾随佣金</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="CustodyFee" id="CustodyFee" value="<?= isset($data['CustodyFee'])?$data['CustodyFee']:'';?>" class="inputTitle" />							
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">基金代码</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="FundCode" id="FundCode" value="<?= isset($data['FundCode'])?$data['FundCode']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">基金类型</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="FundType" id="FundType" value="<?= isset($data['FundType'])?$data['FundType']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">基金名称</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="FundName" id="FundName" value="<?= isset($data['FundName'])?$data['FundName']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">拼音简称</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="ChiSpelling" id="ChiSpelling" value="<?= isset($data['ChiSpelling'])?$data['ChiSpelling']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">最新净值</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="PernetValue" id="PernetValue" value="<?= isset($data['PernetValue'])?$data['PernetValue']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">日涨幅</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="NVDailyGrowthRate" id="NVDailyGrowthRate" value="<?= isset($data['NVDailyGrowthRate'])?$data['NVDailyGrowthRate']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">周涨幅</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="RRInSingleWeek" id="RRInSingleWeek" value="<?= isset($data['RRInSingleWeek'])?$data['RRInSingleWeek']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">月涨幅</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="RRInSingleMonth" id="RRInSingleMonth" value="<?= isset($data['RRInSingleMonth'])?$data['RRInSingleMonth']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">三个月涨幅</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="RRInThreeMonth" id="RRInThreeMonth" value="<?= isset($data['RRInThreeMonth'])?$data['RRInThreeMonth']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">六个月涨幅</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="RRInSixMonth" id="RRInSixMonth" value="<?= isset($data['RRInSixMonth'])?$data['RRInSixMonth']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">近一年涨幅</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="RRInSingleYear" id="RRInSingleYear" value="<?= isset($data['RRInSingleYear'])?$data['RRInSingleYear']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">今年以来涨幅</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="RRSinceThisYear" id="RRSinceThisYear" value="<?= isset($data['RRSinceThisYear'])?$data['RRSinceThisYear']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">万份收益</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="DailyProfit" id="DailyProfit" value="<?= isset($data['DailyProfit'])?$data['DailyProfit']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">7日年化收益率</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="LatestWeeklyYield" id="LatestWeeklyYield" value="<?= isset($data['LatestWeeklyYield'])?$data['LatestWeeklyYield']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">基金风险等级</td>
			<td class="editTitle">
				<div class="itemSelect w300">
					<select class="asideSelect" name="FundRiskLevel">
						<option value="0" <?= isset($data['FundRiskLevel']) && $data['FundRiskLevel'] == '0'? 'selected':'';?>>低</option>
						<option value="1" <?= isset($data['FundRiskLevel']) && $data['FundRiskLevel'] == '1'? 'selected':'';?>>中</option>
						<option value="2" <?= isset($data['FundRiskLevel']) && $data['FundRiskLevel'] == '2'? 'selected':'';?>>高</option>
					</select>	
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">基金状态</td>
			<td class="editTitle">
				<div class="itemSelect w300">
					<select class="asideSelect" name="FundState">
						<option value="0" <?= isset($data['FundState']) && $data['FundState'] == '0'? 'selected':'';?>>正常</option>
						<option value="1" <?= isset($data['FundState']) && $data['FundState'] == '1'? 'selected':'';?>>发行</option>
						<option value="2" <?= isset($data['FundState']) && $data['FundState'] == '2'? 'selected':'';?>>发行成功</option>
					</select>	
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">收费方式</td>
			<td class="editTitle">
				<div class="itemSelect w300">
					<select class="asideSelect" name="ShareType">
						<option value="A" <?= isset($data['ShareType']) && $data['ShareType'] == 'A'? 'selected':'';?>>前端收费</option>
						<option value="B" <?= isset($data['ShareType']) && $data['ShareType'] == 'B'? 'selected':'';?>>后端收费</option>
						<option value="C" <?= isset($data['ShareType']) && $data['ShareType'] == 'C'? 'selected':'';?>>其它</option>
					</select>	
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">申购状态</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="DeclareState" id="DeclareState" value="<?= isset($data['DeclareState'])?$data['DeclareState']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">认购状态</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="SubScribeState" id="SubScribeState" value="<?= isset($data['SubScribeState'])?$data['SubScribeState']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">定投状态</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="ValuagrState" id="ValuagrState" value="<?= isset($data['ValuagrState'])?$data['ValuagrState']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">赎回状态</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="WithDrawState" id="WithDrawState" value="<?= isset($data['WithDrawState'])?$data['WithDrawState']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">最小持有份额</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="MinHoldShare" id="MinHoldShare" value="<?= isset($data['MinHoldShare'])?$data['MinHoldShare']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">最低赎回份额</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="MinRedemeShare" id="MinRedemeShare" value="<?= isset($data['MinRedemeShare'])?$data['MinRedemeShare']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">最低申购金额</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="MinPurchaseAmount" id="MinPurchaseAmount" value="<?= isset($data['MinPurchaseAmount'])?$data['MinPurchaseAmount']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">最低认购金额</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="MinSubscribAmount" id="MinSubscribAmount" value="<?= isset($data['MinSubscribAmount'])?$data['MinSubscribAmount']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">申购追加最小值</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="MinAddPurchaseAmount" id="MinAddPurchaseAmount" value="<?= isset($data['MinAddPurchaseAmount'])?$data['MinAddPurchaseAmount']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">最低定投金额</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="MinValuagrAmount" id="MinValuagrAmount" value="<?= isset($data['MinValuagrAmount'])?$data['MinValuagrAmount']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">最低定投追加金额</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="MinAddValuagrAmount" id="MinAddValuagrAmount" value="<?= isset($data['MinAddValuagrAmount'])?$data['MinAddValuagrAmount']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">管理费</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="ManageFee" id="ManageFee" value="<?= isset($data['ManageFee'])?$data['ManageFee']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center">T+0标示</td>
			<td class="editTitle">
				<div class="wbAuto">
					<input type="" name="MoneyFund" id="MoneyFund" value="<?= isset($data['MoneyFund'])?$data['MoneyFund']:'';?>" class="inputTitle" />
				</div>
			</td>
		</tr>
		<tr>
			<td width="150" class="talC" align="center" colspan="2"><a href="javascript:void(0);" class="buttonA fundinfo-submit">提交</a></td>
		</tr>
	</table>
	</form>
</section>
<script type="application/javascript">
$(function(){
	$('.fundinfo-submit').on('click',function(){
		var url = "<?= \yii\helpers\Url::to(['business/edit-fund-info']);?>";
		var data = $("form").serializeArray();
		
		Ajax(url,data);
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
			$('form').get(0).appendChild(spinner.el);
        },
        complete: function(XMLHttpRequest, textStatus){
        	spinner.stop();
        },
        success: function(rs){
        	if (rs.error == 0) {
        		window.location.href = "/business/fund-info";
        	}else{
        		alert(rs.message);
        	}
        	console.log(rs);
        },
        error:function(XMLHttpRequest, textStatus, errorThrown){
            console.log('Ajax request error!');
        }
    });
}
</script>