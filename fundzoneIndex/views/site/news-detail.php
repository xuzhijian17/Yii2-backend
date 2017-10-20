<?php
use yii\helpers\Url;

$curCategory = '';
$cid = isset(Yii::$app->request->queryParams['cid']) ? Yii::$app->request->queryParams['cid'] : '';
?>
<section class="banner newsBanner"><!--banner--></section>
<section class="main maxW fixed">
	<div class="main_left">
		<ul class="subMenu">
			<?php foreach ($Categorys as $key => $value): ?> 
				<li><a href="<?= Url::to(['site/news','cid'=>$value['id']]);?>" class="subLink <?php if($cid==$value['id']){$curCategory=$value['Category'];echo 'cur';};?>"><?= $value['Category'];?></a></li>
			<?php endforeach;?>
		</ul>
	</div>
	<div class="main_right">
		<h2 class="cntT"><?= $curCategory;?></h2>
		<?php if(isset($NewsDetail) && !empty($NewsDetail)):?>
			<h3 class="articleT"><?= $NewsDetail['Title'];?></h3>
			<div class="newsCnt">
				<?= $NewsDetail['Content'];?>
			</div>
		<?php endif;?>
	</div>
</section>
