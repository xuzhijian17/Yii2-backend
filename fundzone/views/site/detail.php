<?php
use yii\helpers\Url;
?>
<section class="content">
	<div class="fundWarp">
		<div class="fundT">
			<?php if($detailData):?>
				<dl>
					<dt>
						<span class="fundName"><?= $detailData['FundName'].'（'.$detailData['FundType'].'）';?></span>
						<span class="inline col75">基金代码 <?= $detailData['FundCode'];?></span>
						<span class="inline sbor"><span class="fundTag <?= $detailData['FundTypeClass'];?>">&nbsp;</span></span>
						<span class="inline risk <?= $detailData['FundRiskLevelClass'];?>"><?= $detailData['FundRiskLevelName'];?></span>
					</dt>
					<dd>
						<span class="fundlable starBtm">晨星评级</span>
						<span class="stars <?= isset($detailData['StarRankClass'])?$detailData['StarRankClass']:'';?>"></span><!--星星个数添加样式 stars01，几个星星就是零几，没有星星就不加样式-->
					</dd>
					<?php if($detailData['FundTypeCode']=='1109' || $detailData['FundTypeCode']=='1106'):?>
						<dd>
							<span class="fundlable">7日年化收益率</span>
							<span class="fundVol <?= $detailData['DailyProfit']>0?'colRed':($detailData['DailyProfit']<0?'colGreen':'col96');?>"><?= $detailData['LatestWeeklyYield'];?> </span>
						</dd>
						<dd>
							<span class="fundlable">万份收益</span>
							<span class="fundVol"><?= $detailData['DailyProfit'];?> </span>
						</dd>
					<?php else:?>
						<dd>
							<span class="fundlable">日净值增长率</span>
							<span class="fundVol <?= $detailData['NVDailyGrowthRate']>0?'colRed':($detailData['NVDailyGrowthRate']<0?'colGreen':'col96');?>"><?= $detailData['NVDailyGrowthRate'];?>% </span>
						</dd>
						<dd>
							<span class="fundlable">最新净值(<?= $detailData['TradingDay'];?>)</span>
							<span class="fundVol"><?= $detailData['PernetValue'];?> </span>
						</dd>
					<?php endif;?>
				</dl>
			<?php endif;?>
		</div>
		<!--fundT end-->
		<div class="fixed">
			<div class="btn2 w150 fL"><a class="submit2" href="<?= Url::to(['trade/purchase-page','fundcode'=>isset($detailData['FundCode'])?$detailData['FundCode']:'']);?>">申购</a></div>
		</div>
		
		<div class="chart">
			<div class="dateList fixed">
				<ul>
					<li><a href="javascript:void(0);" class="date oneMonth cur">一个月</a></li>
					<li><a href="javascript:void(0);" class="date thrMonth">三个月</a></li>
					<li><a href="javascript:void(0);" class="date sixMonth">六个月</a></li>
					<li><a href="javascript:void(0);" class="date tweMonth">一年</a></li>
					<li><a href="javascript:void(0);" class="date allMonth">成立以来</a></li>
				</ul>
			</div>
			<div class="chartWarp" id="chartWarp"></div>
			<!--<img src="../images/Chart.jpg"/>-->
		</div>
	</div>
</section>
<!--section content end-->
<section class="content" style="margin-bottom: 50px;">
	<div class="tabWarp fixW">
		<div class="tabList w720">
			<ul>
				<li class="tabCur"><span class="">基金经理</span></li>
				<li><span class="">交易须知</span></li>
				<li><span class="">基金概况</span></li>
				<li><span class="">持仓信息</span></li>
				<li><span class="">基金公告</span></li>
				<li><span class="">基金分红</span></li>
			</ul>
		</div>
		<!--tabList end-->
		<div class="tabCnts">
			<div class="tabCnt">
				<?php if($managerData):?>
					<?php foreach($managerData as $key=>$value):?>
						<?php if($value['Incumbent']=='0'){continue;};?>
						<div class="manager mB20">
							<div class="mgrPhoto">
								<img src="<?= Url::to(['fund/manager-avatar','ID'=>$value['ID']]);?>"/>
							</div>
							<div class="mgrInfo">
								<dl>
									<dt>
										<span class="mgrName"><?= $value['Name'];?></span>
										<span class="mgrDate"><?= $value['AccessionDate'];?> <?= $value['DimissionDate'];?></span>
									</dt>
									<dd><?= $value['Background'];?></dd>
								</dl>
							</div>
						</div>
					<?php endforeach;?>
					<!--manager end-->
					<!-- <h2 class="h2T2">历任情况</h2>
					<table border="0" cellspacing="1" cellpadding="0" class="tableB">
						<thead>
							<tr>
								<th>基金代码</th>
								<th>基金名称</th>
								<th>基金类型</th>
								<th>任期时间</th>
								<th>任期回报</th>
								<th>同类平均</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($managerData as $key=>$value):?>
								<tr>
									<td>080012</td>
									<td>长盛电子信息</td>
									<td>混合型</td>
									<td>2014-04-23至今</td>
									<td>56.75%</td>
									<td>7.07</td>
								</tr>
							<?php endforeach;?>
						</tbody>
					</table> -->
				<?php else:?>
					<div class="noData"></div>
				<?php endif;?>
			</div>
			<!--tabCnt end-->
			<div class="tabCnt">
				<?php if($profitGuide):?>
					<h2 class="h2T2">买入须知</h2>
					<table border="0" cellspacing="1" cellpadding="0" class="tableD mB30">
						<tbody>
							<tr>
							  <td width="16.66%" bgcolor="#f6f6f6">收费方式</td>
								<td width="16.66%" bgcolor="#FFFFFF"><?= $profitGuide['ShareTypeName'];?></td>
								<td width="16.66%" bgcolor="#F6F6F6"><?= $profitGuide['fundStateName'];?>状态</td>
								<td width="16.66%" bgcolor="#FFFFFF"><?= $profitGuide['buyStatusName'];?></td>
								<td width="16.66%" bgcolor="#F6F6F6">管理费</td>
								<td width="16.66%" bgcolor="#FFFFFF"><?= isset($profitGuide['manageChargeRate'][0]['ChargeRateDes'])?$profitGuide['manageChargeRate'][0]['ChargeRateDes']:'0%';?>（每年）</td>
							</tr>
							<tr>
							  <td bgcolor="#f6f6f6">起始金额</td>
								<td bgcolor="#FFFFFF"><?= $profitGuide['minAmount'];?>元</td>
								<td bgcolor="#F6F6F6">赎回状态</td>
								<td bgcolor="#FFFFFF"><?= $profitGuide['sellStatusName'];?></td>
								<td bgcolor="#F6F6F6">托管费</td>
								<td bgcolor="#FFFFFF"><?= isset($profitGuide['trusteeshipChargeRate'][0]['ChargeRateDes'])?$profitGuide['trusteeshipChargeRate'][0]['ChargeRateDes']:'0%';?>（每年）</td>
							</tr>
						</tbody>
					</table>

					<div>
						<table border="0" cellspacing="1" cellpadding="0" class="tableB fL" style="width: 50%">
							<thead>
								<tr>
									<th colspan="2"><?= $profitGuide['fundStateName'];?>费率</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td width="50%"><?= $profitGuide['fundStateName'];?>金额（RMB）</td>
									<td width="50%">费率</td>
								</tr>
								<?php foreach($profitGuide['buyChargeRate'] as $key=>$value):?>
									<tr>
										<td><?= $value['DivIntervalDes']?:'任意金额';?></td>
										<td><?= $value['ChargeRateUnit']==7||$value['ChargeRateDes']<'0.6%'?$value['ChargeRateDes']:'<span class="del">'.$value['ChargeRateDes'].'</span><span>0.6%</span>';?></td>
									</tr>
								<?php endforeach;?>
							</tbody>
						</table>
						<table border="0" cellspacing="1" cellpadding="0" class="tableB fL" style="width: 50%; margin-left:-1px">
							<thead>
								<tr>
									<th colspan="2">赎回费率</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td width="50%">持有期限（Dur）</td>
									<td width="50%">费率</td>
								</tr>
								<?php foreach($profitGuide['sellChargeRate'] as $key=>$value):?>
									<tr>
										<td><?= $value['DivIntervalDes']?:'任意期限';?></td>
										<td><?= $value['ChargeRateDes'];?></td>
									</tr>
								<?php endforeach;?>
							</tbody>
						</table>
					</div>
				<?php else:?>
					<div class="noData"></div>
				<?php endif;?>
			</div>
			<!--tabCnt end-->
			<div class="tabCnt">
				<?php if($fundArchivesData):?>
					<h2 class="h2T2">基本信息</h2>
					<table border="0" cellspacing="1" cellpadding="0" class="tableD mB30">
						<tbody>
							<tr>
							    <td width="16.66%" bgcolor="#f6f6f6">成立日期</td>
								<td bgcolor="#FFFFFF"><?= $fundArchivesData['EstablishmentDate'];?></td>
							</tr>
							<tr>
							  	<td bgcolor="#f6f6f6">基金类型</td>
								<td bgcolor="#FFFFFF"><?= $fundArchivesData['FundType'];?></td>
							</tr>
							<tr>
							  	<td bgcolor="#f6f6f6">设立规模</td>
								<td bgcolor="#FFFFFF"><?= $fundArchivesData['FoundedSize'];?>份</td>
							</tr>
							<tr>
							  	<td bgcolor="#f6f6f6">基金公司</td>
								<td bgcolor="#FFFFFF"><?= $fundArchivesData['InvestAdvisorName'];?></td>
							</tr>
							<tr>
							  	<td bgcolor="#f6f6f6">托管银行</td>
								<td bgcolor="#FFFFFF"><?= $fundArchivesData['TrusteeName'];?></td>
							</tr>
						</tbody>
					</table>
					<div class="txtBlk">
						<h2 class="h2T2">基金简介</h2>
						<p><?= $fundArchivesData['BriefIntro'];?></p>								
					</div>
					<div class="txtBlk">
						<h2 class="h2T2">投资方向</h2>
						<p><?= $fundArchivesData['InvestOrientation'];?></p>
					</div>
					<div class="txtBlk">
						<h2 class="h2T2">投资目标</h2>
						<p><?= $fundArchivesData['InvestTarget'];?></p>
					</div>
					<div class="txtBlk pB0">
						<h2 class="h2T2">投资范围</h2>
						<p><?= $fundArchivesData['InvestField'];?></p>
					</div>
				<?php else:?>
					<div class="noData"></div>
				<?php endif;?>
			</div>
			<!--tabCnt end-->
			<div class="tabCnt">
				<?php if($positionInfoData && (isset($positionInfoData['assetAllocation']) && !empty($positionInfoData['assetAllocation']))):?>
					<?php if($positionInfoData['assetAllocation']):?>
						<h2 class="h2T2">资产配置</h2>
						<div class="chart mB20">
							<div class="chartpie" id="chartpie"></div>
							<!-- <img src="../images/imgCc.jpg"/> -->
						</div>
					<?php endif;?>
					<?php if($positionInfoData['investIndustry']):?>
						<h2 class="h2T2">行业配置</h2>
						<table border="0" cellspacing="1" cellpadding="0" class="tableB mB20">
							<thead>
								<tr>
									<th width="50%">行业</th>
									<th>占比</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($positionInfoData['investIndustry'] as $key=>$value):?>
									<tr>
										<td><?= $value['IndustryName'];?></td>
										<td><?= round($value['RatioInNV']*100,2);?>%</td>
									</tr>
								<?php endforeach;?>
							</tbody>
						</table>
					<?php endif;?>
					<?php if($positionInfoData['keyStockPortfolio']):?>
						<h2 class="h2T2">重仓股</h2>
						<table border="0" cellspacing="1" cellpadding="0" class="tableB">
							<thead>
								<tr>
									<th width="25%">股票名称</th>
									<th width="25%">占资产净值比例</th>
									<th width="25%">持有股数</th>
									<th width="25%">持有市值</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($positionInfoData['keyStockPortfolio'] as $key=>$value):?>
									<tr>
										<td><?= $value['SecuAbbr'].'（'.$value['SecuCode'].'）';?></td>
										<td><?= round($value['RatioInNV']*100,2);?>%</td>
										<td><?= round($value['SharesHolding'],2);?></td>
										<td><?= round($value['MarketValue'],2);?>元</td>
									</tr>
								<?php endforeach;?>
							</tbody>
						</table>
					<?php endif;?>
				<?php else:?>
					<div class="noData"></div>
				<?php endif;?>
			</div>
			<!--tabCnt end-->
			<div class="tabCnt">
				<?php if($fundBulletinData):?>
					<div class="list">
						<ul>
							<?php foreach($fundBulletinData as $key=>$value):?>
								<li>
									<span class="listDate"><?= $value['BulletinDate'];?></span>
									<span class="listT">
										<a href="<?= Url::to(['site/fund-bulletin-detail','ID'=>$value['ID']]);?>" target="_blank"><?= $value['InfoTitle'];?></a>
									</span>
								</li>
							<?php endforeach;?>
						</ul>
					</div>
				<?php endif;?>
			</div>
			<!--tabCnt end-->
			<div class="tabCnt">
				<?php if($participationProfit):?>
					<table border="0" cellspacing="1" cellpadding="0" class="tableB">
						<thead>
							<tr>
								<th>权益登记日</th>
								<th>红利发放日</th>
								<th>分红</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($participationProfit as $key=>$value):?>
								<tr>
									<td><?= $value['ExRightDate'];?></td>
									<td><?= $value['ExecuteDate'];?></td>
									<td><?= round($value['ActualRatioAfterTax'],2);?>元/10份</td>
								</tr>
							<?php endforeach;?>
						</tbody>
					</table>
				<?php else:?>
					<div class="noData"></div>
				<?php endif;?>
			</div>
			<!--tabCnt end-->
		</div>
		<!--tabCnts end-->
	</div>
	<!--tabWarp end-->
</section>
<script src="<?= Url::base();?>/js/echarts.min.js"></script>
<script type="text/javascript">
var netValueData = <?= \yii\helpers\Json::encode($netValueData);?>;
var assetData= <?= \yii\helpers\Json::encode($positionInfoData['assetAllocation']);?>;

// 折线图
var myChart;
if (netValueData) {
	myChart = echarts.init(document.getElementById('chartWarp'));
}
var option = function (dates,datas,name){
	return {
	    tooltip: {
	        trigger: 'axis',
	        // formatter: "{a} <br/>{b} : {c}%"
	    },
	    toolbox: {
	        show: true,
	        feature: {
	            magicType: {show: true, type: ['stack', 'tiled']},
	            saveAsImage: {show: true}
	        }
	    },
	    xAxis: {
	        type: 'category',
	        boundaryGap: false,
	        data: dates
	    },
	    yAxis: {
	        type: 'value',
	        axisLabel: {
              show: true,
              interval: 'auto',
              formatter: '{value}'
            }
	    },
	    series: [{
	        name: name,
	        type: 'line',
	        smooth: true,
	        data: datas
	    }]
    }
};

//饼图
var myChartPie;
if (assetData.length>0) {
	myChartPie = echarts.init(document.getElementById('chartpie'));
}
var pieOption = function(dates,datas){
	return {
		tooltip: {
	        trigger: 'item',
	        formatter: '{a} <br/>{b} : {c}%'
	    },
	    legend: {
	        orient: 'vertical',
	        left:165,
	        top:20,
	        itemWidth:15,
	        itemHeight:15,
	        data:dates
	    },
		series : [
			{
				name:'资产配置',
				type:'pie',
				center:[75, '50%'],
				radius : ['100%', '60%'],
				hoverAnimation:false,
				legendHoverLink:false,
				itemStyle : {
					normal : {
						label : {
							show : false
						},
						labelLine : {
							show : false
						}
					},
					emphasis : {
						label : {
							show : false,
							position : 'center',
							textStyle : {
								fontSize : '30',
								fontWeight : 'bold'
							}
						}
					}
				},
				data:datas
			}
		]
	}
} 


$(document).ready(function(){

	var dates = [];
	var datas = [];
	var name = '<?= $detailData['FundTypeCode']=='1109' || $detailData['FundTypeCode']=='1106' ? '万份收益' : '每日收益';?>';
	if (netValueData) {
		dates = netValueData.oneMonth.EndDate;
		datas = netValueData.oneMonth.data;
		myChart.setOption(option(dates,datas,name), true);
		// 日期切换
		$(".date").on("click",function(){
			$(this).addClass("cur").parent().siblings().children(".date").removeClass("cur");
			if($(this).hasClass("oneMonth")){
				dates = netValueData.oneMonth.EndDate;
				datas = netValueData.oneMonth.data;
				myChart.setOption(option(dates,datas,name), true);
			}else if($(this).hasClass("thrMonth")){
				dates = netValueData.thrMonth.EndDate;
				datas = netValueData.thrMonth.data;
				myChart.setOption(option(dates,datas,name), true);
			}else if($(this).hasClass("sixMonth")){
				dates = netValueData.sixMonth.EndDate;
				datas = netValueData.sixMonth.data;
				myChart.setOption(option(dates,datas,name), true);
			}else if($(this).hasClass("tweMonth")){
				dates = netValueData.tweMonth.EndDate;
				datas = netValueData.tweMonth.data;
				myChart.setOption(option(dates,datas,name), true);
			}else if($(this).hasClass("allMonth")){
				dates = netValueData.allMonth.EndDate;
				datas = netValueData.allMonth.data;
				myChart.setOption(option(dates,datas,name), true);
			};
		});
	}
	
	var dates2 = [];
	var datas2 = [];
	var color = ['#8564ab','#ae98c7','#d6cbe3'];
	if (assetData.length > 0) {
		$.each(assetData,function(i,n){
			dates2.push(n.AssetType);
			datas2.push({value:(n.RatioInNV*100).toFixed(2), name:n.AssetType,itemStyle:{normal:{color:color.shift()}}});
		});

		myChartPie.setOption(pieOption(dates2,datas2));
	}
});

/**
* Ajax处理函数
*/
// function Ajax(url, data) {
// 	$.ajax({
//         type: 'POST',
//         async: true,
//         url: url,
//         data: data,
//         dataType: 'json',
//         beforeSend: function(XMLHttpRequest){
//         	myChart.showLoading();
//         },
//         complete: function(XMLHttpRequest, textStatus){
//         	myChart.hideLoading();
//         },
//         success: function(rs){
//         	if (rs.error == 0) {
//         		$('.userDetail .tableDiv').append(viewList(rs));
//         	}else{
//         		$('.userDetail').html(viewEmpty());
//         		console.log(rs);
//         	}
//         },
//         error:function(XMLHttpRequest, textStatus, errorThrown){
//             console.log('Ajax request error!');
//         }
//     });
// }
</script>