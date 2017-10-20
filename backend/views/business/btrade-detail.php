<?php echo \backend\widgets\LeftMenu::widget(['menuName'=>'maintain']);?>
<section class="mine_section">
	<h2 class="bT">基本信息</h2>
	<table border="0" cellspacing="1" cellpadding="0" class="table editTable">
		<tr>
			<td class="wb50">
				交易账号：<?= isset($rs['TradeAcco'])?$rs['TradeAcco']:'';?>
			</td>
			<td>
				企业名称：<?= isset($rs['CompanyName'])?$rs['CompanyName']:'';?>
			</td>
		</tr>
		<tr>
			<td class="wb50">
				交易类型：<?= isset($rs['TradeTypeName'])?$rs['TradeTypeName']:'';?>
			</td>
			<td>
				收费方式：<?= isset($rs['ShareTypeName'])?$rs['ShareTypeName']:'';?>
			</td>
		</tr>
		<tr>
			<td class="wb50">
				基金代码：<?= isset($rs['FundCode'])?$rs['FundCode']:'';?>
			</td>
			<td>
				基金名称：<?= isset($rs['FundName'])?$rs['FundName']:'';?>
			</td>
		</tr>
		<tr>
			<td class="wb50">
				下单时间：<?= isset($rs['ApplyTime'])?$rs['ApplyTime']:'';?>
			</td>
			<td>
				交易状态：<?= isset($rs['TradeStatusName'])?$rs['TradeStatusName']:'';?>
			</td>
		</tr>
		<tr>
			<?php if(isset($rs['TradeType']) && $rs['TradeType']=='1'):?>
				<td class="wb50">
					申请份额：<?= isset($rs['ApplyShare'])?$rs['ApplyShare']:'';?>份
				</td>
				<td>
					确认金额：<?= isset($rs['ConfirmAmount'])?$rs['ConfirmAmount']:'';?>
				</td>
			<?php else:?>
				<td class="wb50">
					申请金额：<?= isset($rs['ApplyAmount'])?$rs['ApplyAmount']:'';?>元
				</td>
				<td>
					确认份额：<?= isset($rs['ConfirmShare'])?$rs['ConfirmShare']:'';?>
				</td>
			<?php endif;?>
		</tr>
		<?php if(isset($rs['TradeStatusName']) && $rs['TradeStatusName']==='失败'):?>
			<tr>
				<td class="wb50">
					失败原因：<?= isset($rs['OtherInfo'])?$rs['OtherInfo']:'';?>
				</td>
				<td></td>
			</tr>
		<?php endif;?>
	</table>
	<h2 class="bT mT70">汇款凭证</h2>
	<div class="voucher"><!--有凭证的时候去掉noVoucher，没凭证的时候加上noVoucher-->
		<?php if(isset($rs['Pic']) && !empty($rs['Pic'])):?>
			<img src="<?= isset($rs['Pic'])?Yii::getAlias('@web').$rs['Pic']:Yii::getAlias('@web').'/images/updefault.png';?>"/>
		<?php else:?>
			<div class="noVoucher">您还未上传凭证</div>
		<?php endif;?>
	</div>
</section>
