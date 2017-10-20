<?php
use yii\helpers\Url;
$baseUrl = Url::base();
?>
<section class="banner homeBanner" style="margin-bottom: 0;"><a href="<?= Yii::$app->params['tradeHost'].'trade/purchase-page?fundcode=000277';?>" class="empLink" target="_blank">banner</a></section>
<section class="fixed bgF" id="" style="padding: 1px 0 45px;">
	<div class="w1120" style="margin-top: 45px;">
	<div class="picT" style="top: 42px;"><img src="<?= $baseUrl;?>/images/img.png"/></div>
	<div class="column hotList fixed">
		<ul id="recommend">
		    <?php foreach($recommend as $key=>$value): ?>
		    	<?php if($key>2){break;}?>
				<li>
					<dl class="hotBlk">
						<dt class="blkT" style="margin-bottom: 30px;"><a class="colblue" href="<?= Url::base().'/site/detail?fundcode='.$value['FundCode'];?>"><?= $value['FundAbbrName'];?> <span class="fs12 col96 dbk"><?= $value['FundCode'];?></span></a></dt>
						<dd class="fixed">
							<div class="fL">
								<span class="colRed fs30 dbk"><?= $value['RRSinceStart'];?><small>%</small></span>
								<span class="fs14 col96 dbk">成立以来</span>
							</div>
							<div class="fR">
								<span class="colRed fs30 dbk"><?= round($value['MinPurchaseAmount'],0);?><small>元</small></span>
								<span class="fs14 col96 dbk">起购金额</span>
							</div>
						</dd>
						<dd class="fixed h40" style="text-align: center;"><?= $value['Tags'];?></dd>
						<dd><a href="<?= Yii::$app->params['tradeHost'].'trade/purchase-page?fundcode='.$value['FundCode'];?>" class="buttonA">购买</a></dd>
					</dl>
				</li>
			<?php endforeach;?>
		</ul>
	</div>
	</div>
</section>
<!--section end-->
<section class="maxW">
	<h2 class="h2T">基金推荐</h2>
	<div class="tabWarp fundType" id="fundList">
		<div class="tabList">
			<ul>
				<li class="tabCur"><span class="typeIco typeHb" v-on:click="alterField(1)">货币型</span></li>
				<li><span class="typeIco typeBb" v-on:click="alterField(1)">理财型</span></li>
				<li><span class="typeIco typeZq" v-on:click="alterField(0)">债券型</span></li>
				<li><span class="typeIco typeHh" v-on:click="alterField(0)">混合型</span></li>
				<li><span class="typeIco typeGp" v-on:click="alterField(0)">股票型</span></li>
				<li><span class="typeIco typeQt" v-on:click="alterField(0)">QDII</span></li>
			</ul>
		</div>
		<div class="tabCnts">
			<div class="tabCnt">
				<table border="0" cellspacing="0" cellpadding="0" class="table fundTypeTable">
					<thead>
						<tr>
							<th width="10">&nbsp;</th>
							<th>日期</th>
							<th>基金名称</th>
							<th>基金代码</th>
							<th>万份收益</th>
							<th>七日年化</th>
							<th>成立以来收益</th>
							<th>近一年收益</th>
							<!-- <th>推荐理由</th> -->
							<th width="65">操作</th>
							<th width="10">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php if(isset($moneyFundList['list']) && !empty($moneyFundList['list'])):?>
						<?php foreach($moneyFundList['list'] as $key=>$value):?>
							<tr>
								<td>&nbsp;</td>
								<td><span><?= $value['TradingDay'];?></span></td>
								<td><a class="colblue" href="<?= Url::base().'/site/detail?fundcode='.$value['FundCode'];?>"><span><?= $value['FundAbbrName'];?></span></a></td>
								<td><span><?= $value['FundCode'];?></span></td>
								<td><span class="<?= $value['DailyProfit']>0?'colRed':($value['DailyProfit']<0?'colGreen':'');?>"><?= $value['DailyProfit'];?></span></td>
								<td><span class="<?= $value['LatestWeeklyYield']>0?'colRed':($value['LatestWeeklyYield']<0?'colGreen':'');?>"><?= $value['LatestWeeklyYield'];?>%</span></td>
								<td><span class="<?= $value['RRSinceStart']>0?'colRed':($value['RRSinceStart']<0?'colGreen':'');?>"><?= $value['RRSinceStart'];?>%</span></td>
								<td><span class="<?= $value['RRInSingleYear']>0?'colRed':($value['RRInSingleYear']<0?'colGreen':'');?>"><?= $value['RRInSingleYear'];?>%</span></td>
								<!-- <td><span>机货基中的战斗机</span></td> -->
								<td><a href="<?= Yii::$app->params['tradeHost'].'trade/purchase-page?fundcode='.$value['FundCode'];?>" class="buttonB">申购</a></td>
								<td>&nbsp;</td>
							</tr>
						<?php endforeach;?>
					<?php endif;?>
					</tbody>
				</table>
			</div>
			<div class="tabCnt">
				<table border="0" cellspacing="0" cellpadding="0" class="table fundTypeTable">
					<thead>
						<tr>
							<th width="20">&nbsp;</th>
							<th>日期</th>
							<th>基金名称</th>
							<th>基金代码</th>
							<th>万份收益</th>
							<th>七日年化</th>
							<th>成立以来收益</th>
							<th>近一年收益</th>
							<!-- <th>推荐理由</th> -->
							<th width="65">操作</th>
							<th width="20">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php if(isset($sbondFundList['list']) && !empty($sbondFundList['list'])):?>
							<?php foreach($sbondFundList['list'] as $key=>$value):?>
								<tr>
									<td>&nbsp;</td>
									<td><span><?= $value['TradingDay'];?></span></td>
									<td><a class="colblue" href="<?= Url::base().'/site/detail?fundcode='.$value['FundCode'];?>"><span><?= $value['FundAbbrName'];?></span></a></td>
									<td><span><?= $value['FundCode'];?></span></td>
									<td><span class="<?= $value['DailyProfit']>0?'colRed':($value['DailyProfit']<0?'colGreen':'');?>"><?= $value['DailyProfit'];?></span></td>
									<td><span class="<?= $value['LatestWeeklyYield']>0?'colRed':($value['LatestWeeklyYield']<0?'colGreen':'');?>"><?= $value['LatestWeeklyYield'];?>%</span></td>
									<td><span class="<?= $value['RRSinceStart']>0?'colRed':($value['RRSinceStart']<0?'colGreen':'');?>"><?= $value['RRSinceStart'];?>%</span></td>
									<td><span class="<?= $value['RRInSingleYear']>0?'colRed':($value['RRInSingleYear']<0?'colGreen':'');?>"><?= $value['RRInSingleYear'];?>%</span></td>
									<!-- <td><span>机货基中的战斗机</span></td> -->
									<td><a href="<?= Yii::$app->params['tradeHost'].'trade/purchase-page?fundcode='.$value['FundCode'];?>" class="buttonB">申购</a></td>
									<td>&nbsp;</td>
								</tr>
							<?php endforeach;?>
						<?php endif;?>
					</tbody>
				</table>
			</div>
			<div class="tabCnt">
				<table border="0" cellspacing="0" cellpadding="0" class="table fundTypeTable">
					<thead>
						<tr>
							<th width="20">&nbsp;</th>
							<th>日期</th>
							<th>基金名称</th>
							<th>基金代码</th>
							<th>单位净值</th>
							<th>日涨幅</th>
							<th>成立以来收益</th>
							<th>近一年收益</th>
							<!-- <th>推荐理由</th> -->
							<th width="65">操作</th>
							<th width="20">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php if(isset($bondFundList['list']) && !empty($bondFundList['list'])):?>
							<?php foreach($bondFundList['list'] as $key=>$value):?>
								<tr>
									<td>&nbsp;</td>
									<td><span><?= $value['TradingDay'];?></span></td>
									<td><a class="colblue" href="<?= Url::base().'/site/detail?fundcode='.$value['FundCode'];?>"><span><?= $value['FundAbbrName'];?></span></a></td>
									<td><span><?= $value['FundCode'];?></span></td>
									<td><span class="<?= $value['PernetValue']>0?'colRed':($value['PernetValue']<0?'colGreen':'');?>"><?= $value['PernetValue'];?></span></td>
									<td><span class="<?= $value['NVDailyGrowthRate']>0?'colRed':($value['NVDailyGrowthRate']<0?'colGreen':'');?>"><?= $value['NVDailyGrowthRate'];?>%</span></td>
									<td><span class="<?= $value['RRSinceStart']>0?'colRed':($value['RRSinceStart']<0?'colGreen':'');?>"><?= $value['RRSinceStart'];?>%</span></td>
									<td><span class="<?= $value['RRInSingleYear']>0?'colRed':($value['RRInSingleYear']<0?'colGreen':'');?>"><?= $value['RRInSingleYear'];?>%</span></td>
									<!-- <td><span>机货基中的战斗机</span></td> -->
									<td><a href="<?= Yii::$app->params['tradeHost'].'trade/purchase-page?fundcode='.$value['FundCode'];?>" class="buttonB">申购</a></td>
									<td>&nbsp;</td>
								</tr>
							<?php endforeach;?>
						<?php endif;?>
					</tbody>
				</table>
			</div>
			<div class="tabCnt">
				<table border="0" cellspacing="0" cellpadding="0" class="table fundTypeTable">
					<thead>
						<tr>
							<th width="20">&nbsp;</th>
							<th>日期</th>
							<th>基金名称</th>
							<th>基金代码</th>
							<th>单位净值</th>
							<th>日涨幅</th>
							<th>成立以来收益</th>
							<th>近一年收益</th>
							<!-- <th>推荐理由</th> -->
							<th width="65">操作</th>
							<th width="20">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php if(isset($mixFundList['list']) && !empty($mixFundList['list'])):?>
							<?php foreach($mixFundList['list'] as $key=>$value):?>
								<tr>
									<td>&nbsp;</td>
									<td><span><?= $value['TradingDay'];?></span></td>
									<td><a class="colblue" href="<?= Url::base().'/site/detail?fundcode='.$value['FundCode'];?>"><span><?= $value['FundAbbrName'];?></span></a></td>
									<td><span><?= $value['FundCode'];?></span></td>
									<td><span class="<?= $value['PernetValue']>0?'colRed':($value['PernetValue']<0?'colGreen':'');?>"><?= $value['PernetValue'];?></span></td>
									<td><span class="<?= $value['NVDailyGrowthRate']>0?'colRed':($value['NVDailyGrowthRate']<0?'colGreen':'');?>"><?= $value['NVDailyGrowthRate'];?>%</span></td>
									<td><span class="<?= $value['RRSinceStart']>0?'colRed':($value['RRSinceStart']<0?'colGreen':'');?>"><?= $value['RRSinceStart'];?>%</span></td>
									<td><span class="<?= $value['RRInSingleYear']>0?'colRed':($value['RRInSingleYear']<0?'colGreen':'');?>"><?= $value['RRInSingleYear'];?>%</span></td>
									<!-- <td><span>机货基中的战斗机</span></td> -->
									<td><a href="<?= Yii::$app->params['tradeHost'].'trade/purchase-page?fundcode='.$value['FundCode'];?>" class="buttonB">申购</a></td>
									<td>&nbsp;</td>
								</tr>
							<?php endforeach;?>
						<?php endif;?>
					</tbody>
				</table>
			</div>
			<div class="tabCnt">
				<table border="0" cellspacing="0" cellpadding="0" class="table fundTypeTable">
					<thead>
						<tr>
							<th width="20">&nbsp;</th>
							<th>日期</th>
							<th>基金名称</th>
							<th>基金代码</th>
							<th>单位净值</th>
							<th>日涨幅</th>
							<th>成立以来收益</th>
							<th>近一年收益</th>
							<!-- <th>推荐理由</th> -->
							<th width="65">操作</th>
							<th width="20">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php if(isset($stockFundList['list']) && !empty($stockFundList['list'])):?>
							<?php foreach($stockFundList['list'] as $key=>$value):?>
								<tr>
									<td>&nbsp;</td>
									<td><span><?= $value['TradingDay'];?></span></td>
									<td><a class="colblue" href="<?= Url::base().'/site/detail?fundcode='.$value['FundCode'];?>"><span><?= $value['FundAbbrName'];?></span></a></td>
									<td><span><?= $value['FundCode'];?></span></td>
									<td><span class="<?= $value['PernetValue']>0?'colRed':($value['PernetValue']<0?'colGreen':'');?>"><?= $value['PernetValue'];?></span></td>
									<td><span class="<?= $value['NVDailyGrowthRate']>0?'colRed':($value['NVDailyGrowthRate']<0?'colGreen':'');?>"><?= $value['NVDailyGrowthRate'];?>%</span></td>
									<td><span class="<?= $value['RRSinceStart']>0?'colRed':($value['RRSinceStart']<0?'colGreen':'');?>"><?= $value['RRSinceStart'];?>%</span></td>
									<td><span class="<?= $value['RRInSingleYear']>0?'colRed':($value['RRInSingleYear']<0?'colGreen':'');?>"><?= $value['RRInSingleYear'];?>%</span></td>
									<!-- <td><span>机货基中的战斗机</span></td> -->
									<td><a href="<?= Yii::$app->params['tradeHost'].'trade/purchase-page?fundcode='.$value['FundCode'];?>" class="buttonB">申购</a></td>
									<td>&nbsp;</td>
								</tr>
							<?php endforeach;?>
						<?php endif;?>
					</tbody>
				</table>
			</div>
			<div class="tabCnt">
				<table border="0" cellspacing="0" cellpadding="0" class="table fundTypeTable">
					<thead>
						<tr>
							<th width="20">&nbsp;</th>
							<th>日期</th>
							<th>基金名称</th>
							<th>基金代码</th>
							<th>单位净值</th>
							<th>日涨幅</th>
							<th>成立以来收益</th>
							<th>近一年收益</th>
							<!-- <th>推荐理由</th> -->
							<th width="65">操作</th>
							<th width="20">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php if(isset($qdiiFundList['list']) && !empty($qdiiFundList['list'])):?>
							<?php foreach($qdiiFundList['list'] as $key=>$value):?>
								<tr>
									<td>&nbsp;</td>
									<td><span><?= $value['TradingDay'];?></span></td>
									<td><a class="colblue" href="<?= Url::base().'/site/detail?fundcode='.$value['FundCode'];?>"><span><?= $value['FundAbbrName'];?></span></a></td>
									<td><span><?= $value['FundCode'];?></span></td>
									<td><span class="<?= $value['PernetValue']>0?'colRed':($value['PernetValue']<0?'colGreen':'');?>"><?= $value['PernetValue'];?></span></td>
									<td><span class="<?= $value['NVDailyGrowthRate']>0?'colRed':($value['NVDailyGrowthRate']<0?'colGreen':'');?>"><?= $value['NVDailyGrowthRate'];?>%</span></td>
									<td><span class="<?= $value['RRSinceStart']>0?'colRed':($value['RRSinceStart']<0?'colGreen':'');?>"><?= $value['RRSinceStart'];?>%</span></td>
									<td><span class="<?= $value['RRInSingleYear']>0?'colRed':($value['RRInSingleYear']<0?'colGreen':'');?>"><?= $value['RRInSingleYear'];?>%</span></td>
									<!-- <td><span>机货基中的战斗机</span></td> -->
									<td><a href="<?= Yii::$app->params['tradeHost'].'trade/purchase-page?fundcode='.$value['FundCode'];?>" class="buttonB">申购</a></td>
									<td>&nbsp;</td>
								</tr>
							<?php endforeach;?>
						<?php endif;?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>
<!--section end-->
<section class="bgF">
	<div class="maxW newsBlkWarp fixed">
		<?php
			$model = new \fundzone\models\News();
        	$Categorys = $model->getCatNews();
		;?>
		<?php foreach ($Categorys as $key => $value): ?> 
			<div class="newsBlk <?= $key%2?'odd':'even';?>">
				<h2 class="h2T"><?= $value['Category'];?></h2>
				<a href="<?= Url::base().'/site/news?cid='.$value['id'];?>" class="more">更多</a>
				<ul>
					<?php foreach ($model->getRecommendNewsList($value['id']) as $k => $v):?>
						<?php if($k==0):?>
							<li class="topLine">
								<a href="<?= isset($v['Link']) && !empty($v['Link']) ? $v['Link'] : Url::base().'/site/news?id='.$v['id'].'&cid='.$value['id'];?>">
									<img src="<?= $baseUrl;?>/images/img0<?= $key+1;?>.jpg"/>
									<span class="newsT"><?= mb_strlen($v['Title'])>15?mb_substr($v['Title'], 0, 15, 'UTF-8').'...':$v['Title'];?></span>
									<span class="newsDate"><?= $v['UpdateTime'];?></span>
									<p><?= $v['Excerpt']?:mb_substr(strip_tags($v['Content']), 0, 50, 'UTF-8');?>...</p>
								</a>
							</li>
						<?php else:?>
							<li>
								<a href="<?= isset($v['Link']) && !empty($v['Link']) ? $v['Link'] : Url::base().'/site/news?id='.$v['id'].'&cid='.$value['id'];?>">
									<span class="newsT"><?= mb_strlen($v['Title'])>20?mb_substr($v['Title'], 0, 20, 'UTF-8').'...':$v['Title'];?></span>
									<span class="newsDate"><?= date("Y-m-d",strtotime($v['UpdateTime']));?></span>
								</a>
							</li>
						<?php endif;?>
					<?php endforeach;?>
				</ul>
			</div>
		<?php endforeach;?>
		<!--newsBlk end-->
	</div>
</section>
<!--section end-->	
<section class="partners maxW">
	<h2 class="h2T">合作伙伴
		<a class="prev slideP" href="javascript:void(0);"><span></span></a>
		<a class="next slideP" href="javascript:void(0);"><span class="canScroll"></span></a>
	</h2>
	<div class="scrollPage partnerPage" page="1">
		<span class="prevPage">1</span>/<span class="nextPage">5</span>
	</div>
	<div class="partnerWarp scrollWarp">
		<div class="partnterlist scrollCnt fixed">
			<a class="partner scrollUnit" href="http://www.gffunds.com.cn/index.htm" target="_blank"><img src="<?= $baseUrl;?>/images/logo_gf.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.99fund.com/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_htf.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.chinaamc.com/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_hx.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.jysld.com/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_jy.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.igwfmc.com/index.htm" target="_blank"><img src="<?= $baseUrl;?>/images/logo_js.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.jtamc.com/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_jt.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.southernfund.com/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_nf.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.qhkyfund.com/main/home/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_qh.jpg" /></a>
			<a class="partner scrollUnit" href="https://www.galaxyasset.com/etrade/index.html" target="_blank"><img src="<?= $baseUrl;?>/images/logo_yh.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.changanfunds.com/index.html" target="_blank"><img src="<?= $baseUrl;?>/images/logo_ca.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.dbfund.com.cn/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_db.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.byfunds.com/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_by.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.ccbfund.cn/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_jx.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.yhfund.com.cn/main/yhfund/index.shtml" target="_blank"><img src="<?= $baseUrl;?>/images/logo_hy.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.huatai-pb.com/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_br.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.lionfund.com.cn/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_na.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.csfunds.com.cn/minisite/index4Sale/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_cs.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.sinvofund.com/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_xw.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.bosera.com/index.html" target="_blank"><img src="<?= $baseUrl;?>/images/logo_bs.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.lordabbettchina.com/main/index.shtml" target="_blank"><img src="<?= $baseUrl;?>/images/logo_rd.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.hftfund.com/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_hf.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.msfunds.com.cn/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_mg.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.icbccs.com.cn/gyrx/wsjy/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_gy.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.bxrfund.com/front/index_10022.jhtml" target="_blank"><img src="<?= $baseUrl;?>/images/logo_rf.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.postfund.com.cn/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_zy.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.zrfunds.com.cn/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_zr.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.df5888.com/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_df.jpg" /></a>
			<a class="partner scrollUnit" href="http://www.wjasset.com/" target="_blank"><img src="<?= $baseUrl;?>/images/logo_wj.jpg" /></a>
			
		</div>
	</div>
	<!--partnerList end-->
	<div class="license fixed">
		<ul>
			<li>
				<a class="blkLink" href="<?= Url::to(['site/aptitude']);?>">
					<span class="ctsT">基金销售资格证书</span>
					<img src="<?= $baseUrl;?>/images/img_jjzg.jpg" alt="" />
				</a>
			</li>
			<li>
				<a class="blkLink" href="http://www.csrc.gov.cn/pub/newsite/" target="_blank">
					<span class="ctsT">监管机构</span>
					<img src="<?= $baseUrl;?>/images/img_jgjg.jpg" alt="" />
				</a>
			</li>
			<li>
				<a class="blkLink" href="http://www.amac.org.cn/" target="_blank">
					<span class="ctsT">自律组织</span>
					<img src="<?= $baseUrl;?>/images/img_zlzz.jpg" alt="" />
				</a>
			</li>
			<li>
				<a class="blkLink" href="http://www.cmbc.com.cn/" target="_blank">
					<span class="ctsT">监管银行</span>
					<img src="<?= $baseUrl;?>/images/img_jgyh.jpg" alt="" />
				</a>
			</li>
			<!--<li>
				<a class="blkLink" href="javascript:void(0);">
					<span class="ctsT">基金销售资格公示</span>
					<img src="images/img_jjzggs.jpg" alt="" />
				</a>
			</li>-->
		</ul>
	</div>
	<!--blkList end-->
</section>
<script>
$(function(){
	scroll(".scrollCnt",2,190)
	/*new Vue({
	  el: '#fundList',
	  data: {
		dynamicFieldText1: '万份收益',
		dynamicFieldText2: '七日年化收益',
		url: "<?= \yii\helpers\Url::to(['site/get-fund-list']);?>",
		items1: [],
		items2: [],
		items3: [],
		items4: [],
		items5: [],
		items6: []
	  },
	  methods: {
	  	alterField: function(arg){
	  		if (arg) {
	  			this.dynamicFieldText1 = '万份收益';
	  			this.dynamicFieldText2 = '七日年化收益';
	  		}else{
	  			this.dynamicFieldText1 = '单位净值';
	  			this.dynamicFieldText2 = '日涨幅';
	  		}
	  	},
		ajax: function (url,data,type) {
			data.pageSize = 10;
			$.ajax({
				type: type || 'GET',
				async: true,
				url: url,
				data: data,
				dataType: 'json',
				crossDomain: false,
				beforeSend: function(XMLHttpRequest){
				}.bind(this),
				complete: function(XMLHttpRequest, textStatus){
				}.bind(this),
				success: function(rs){
					if (rs.error == 0) {
						if(data.fundtype == '货币型'){
							this.items1 = rs.list;
						}else if (data.fundtype == '短期理财债券型') {
							this.items2 = rs.list;
						}else if (data.fundtype == '债券型') {
							this.items3 = rs.list;
						}else if (data.fundtype == '混合型') {
							this.items4 = rs.list;
						}else if (data.fundtype == '股票型') {
							this.items5 = rs.list;
						}else if (data.fundtype == 'QDII') {
							this.items6 = rs.list;
						}
					}
					// console.log(rs);
				}.bind(this),
				error:function(XMLHttpRequest, textStatus, errorThrown){
					console.log(errorThrown);
				}.bind(this)
			});
		}
	  },
	  ready: function(){
		this.ajax(this.url,{fundtype:'股票型'});
		this.ajax(this.url,{fundtype:'混合型'});
		this.ajax(this.url,{fundtype:'债券型'});
		this.ajax(this.url,{fundtype:'短期理财债券型'});
		this.ajax(this.url,{fundtype:'货币型'});
		this.ajax(this.url,{fundtype:'QDII'});
	  }
	});*/
	
	<?php
	$session = Yii::$app->session;
	if (isset($session['user_login']['Instid']) && $session['user_login']['Instid']==1000
		&& isset($session['company_attach']['FirstLogin']) && empty($session['company_attach']['FirstLogin'])) {?>
		popUpOPSingle(".popUpOp","初次登录，为了您账户的安全，建议您修改初始密码。","提示","立即修改","verifySure","<?= $baseUrl;?>/user/changepassword/");
	<?php }?>
});
</script>
