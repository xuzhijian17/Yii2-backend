<?php
use yii\helpers\Url;
?>
<section class="content" style="margin-bottom: 60px;">
	<?php if($bulletinDetailData):?>
		<div class="fixW">
			<h2 class="articleT"><?= $bulletinDetailData['InfoTitle'];?></h2>
			<div class="newsCnt">
				<p><?= $bulletinDetailData['Content'];?></p>
			</div>
		</div>
	<?php endif;?>
</section>
