<aside>
	<div class="subNav">
		<a href="<?= \yii\helpers\Url::to(['business/index']);?>" class="subLink <?= \yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('business/index') ? 'subCur' : ''; ?>">财经资讯</a>
		<a href="<?= \yii\helpers\Url::to(['business/theme']);?>" class="subLink <?= \yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('business/theme') ? 'subCur' : ''; ?>">主题推荐</a>
		<a href="<?= \yii\helpers\Url::to(['business/hot']);?>" class="subLink <?= \yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('business/hot') ? 'subCur' : ''; ?>">热销基金</a>
		<a href="<?= \yii\helpers\Url::to(['business/category']);?>" class="subLink <?= \yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('business/category') ? 'subCur' : ''; ?>">基金类型</a>
		<a href="<?= \yii\helpers\Url::to(['business/fund-list']);?>" class="subLink <?= \yii\helpers\Url::to(Yii::$app->request->pathInfo) == \yii\helpers\Url::to('business/fund-list') ? 'subCur' : ''; ?>">基金维护</a>
	</div>
</aside>
<section class="mine_section">
	<table border="0" cellspacing="1" cellpadding="0" class="table editTable">
		<tr>
			<td width="100" class="talC" align="center">基金代码</td>
			<td class="editTitle"><input type="" name="" id="" value="" placeholder="请输入基金代码" class="inputTitle" /></td>
		</tr>
		<tr>
			<td class="talC" align="center">tags</td>
			<td class="editTitle">
				<input type="" name="" id="" value="" placeholder="请输入标签" class="inputTitle" />
				<span class="notes">注：多个tag请以逗号隔开  eg:混合型,中高风险,近三月收益前十</span>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<a href="javascript:void(0);" class="buttonA">提交</a>
			</td>
		</tr>
	</table>
</section>
