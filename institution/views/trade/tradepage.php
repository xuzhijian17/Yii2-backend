<style type='text/css'>
.table div{margin:0 5px;}.table div.column1{margin:0 5px 0  20px;}
</style>
<?php 
    use institution\components\LeftBarWidget; 
    use yii\helpers\Url;
    
    $base = Url::base();
?>
		<div class="main">
			<div class="Side">
				<?=LeftBarWidget::widget() ?>
				<div class="rightBar">
					<div class="content">
						<div class="newestNotice slideDown">
							<span class="close">X</span>
							<span class="date">2016-02-24</span>
							<span class="horn">最新公告：春节期间基金申购赎回、小金库快赎业务公告</span> 
						</div>
						<!--newestNotice end-->
						<div class="pT10">
							<div class="titBlk">
								<div class="titlist tabTag">
									<ul>
										<li class="cur"><span class="tabOption">未下单</span></li>
										<li><span class="tabOption">已下单</span></li>
									</ul>
								</div>
							</div>
							<!--titBlk end-->
						</div>
						<!--end pT20-->
						<!--没有数据时使用<div class="nodata"><span>暂无查询结果</span></div>-->
						<div class="tableWarp tabContent"> 
							<div class="tabCnts">
								<div class="tabCnt">
									<form action="" method="post">
									<?php if(empty($data['uncommittedOrder'])){ ?>
										<div class="nodata">
											<span>快来点击下面的按钮进行下单操作</span>
											<div class="dropWarp">
												<a class="buttonB btnN" href="javascript:void(0);">下单</a>
												<div class="dropList">
<!-- 												    <input name="" id="" value="" class="opacityFile" type="file"> -->
													<a class="buttonB importBtn" href="javascript:void(0);">导入下单指令</a>
													<a class="buttonB orderBtn" href="javascript:void(0);">人工下单</a>
												</div>
											</div>
										</div>
										<!--没有下单数据使用上面注释的<div class="nodata">-->
										<?php }else{ ?>
										<table border="0" cellspacing="0" cellpadding="0" class="table order">
											<thead>
												<tr>
													<th width="150"><div class="column1">指令序号</div></th>
													<th><div class="column2">产品名称</div></th>
													<th><div class="column3">基金简称</div></th>
													<th><div class="column4">委托方向</div></th>
													<th><div class="column5">指令（金额/份额）</div></th>
													<th><div class="column6">指令状态</div></th>
													<th><div class="column7">详情</div></th>
												</tr>
											</thead>
											<thead class="tipHead">
												<tr>
													<td colspan="7">
														<div class="importResult slideDown vanish"><!--下单导入提示效果请调用slideDown(".importResult",500)；-->
															<span class="close2">X</span>
															<span class="successTips" style="display: inline-block;"></span>
														</div> 
													</td>
												</tr>
											</thead>
											<!--排序样式:需要排序的字段文字后面加<i class="ico sort"></i>；在div上加样式 s_click；升序在div上添加样式sort_up、降序添加sort_down-->
											<tbody>
											<?php foreach ($data['uncommittedOrder'] as $unComVal) { ?>
												<tr>
													<td>
														<div class="column1">
															<label class="ckLabel"><input type="checkbox" name="orderCk"  value="<?php echo $unComVal['id']; ?>" class="ckInput" /><?php echo $unComVal['extOrderSeq']; ?></label>
														</div>
													</td>
													<td>
														<div class="column2">
															<span><?php echo isset($unComVal['productName'])?$unComVal['productName']:'--'; ?></span>
															<span class="colGray"><?php echo isset($unComVal['productCode'])?$unComVal['productCode']:'--'; ?></span>
														</div>
													</td>
													<td>
														<div class="column3">
															<span><?php echo $unComVal['fundName']; ?></span>
															<span class="colGray"><?php echo $unComVal['fundCode']; ?></span>
														</div>
													</td>
													<td>
														<div class="column4">
															<span class="<?php echo $unComVal['typeClass']; ?>" ><?php echo $unComVal['typeName']; ?></span>
															<!--委托方向，各个方向色值不同分别为：认购colRG、申购colSG、赎回、colSH、转换colZH、撤单colCD、修改分红方式colXG-->
														</div>
													</td>
													<td>
														<div class="column5">
															<span><?php echo $unComVal['order']; ?></span>
														</div>
													</td>
													<td>
														<div class="column6">
															<span>未执行</span>
														</div>
													</td>
													<td>
														<div class="column7">
															<i class="ico icoShow"></i>
														</div>
													</td>
												</tr>
												<tr class="hddenFields" style="display: none;">
													<!--业务类型为认购，申购，修改分红方式显示字段(分红方式)-->
													<?php if(in_array($unComVal['typeCode'],['020','022','029'])){ ?>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>
														<span class="subTh">订单日期</span>
														<div class="column6">
															<span class="singleLine"><?php echo empty($unComVal['cdate'])?'--':$unComVal['cdate']; ?></span>
														</div>
													</td>
													<td>
														<span class="subTh">分红方式</span>
														<div class="column7">
															<span class="singleLine"><?php echo $unComVal['bonus']; ?></span>
														</div>
													</td>
													<!--业务类型为赎回显示字段(巨额赎回标志)-->
													<?php }elseif ($unComVal['typeCode']=='024'){ ?>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>
														<span class="subTh">订单日期</span>
														<div class="column6">
															<span class="singleLine"><?php echo empty($unComVal['cdate'])?'--':$unComVal['cdate']; ?></span>
														</div>
													</td>
													<td>
														<span class="subTh">巨额赎回标志</span>
														<div class="column7">
															<span class="singleLine"><?php echo $unComVal['largeRedemptionFlag']; ?></span>
														</div>
													</td>
													<!--业务类型为撤单显示字段(原指令序号、原委托方向)-->
													<?php }elseif ($unComVal['typeCode']=='053'){ ?>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>
														<span class="subTh">原委托方向</span>
														<div class="column5">
															<span class="singleLine"><?php echo empty($unComVal['originalTypeName'])?'--':$unComVal['originalTypeName']; ?></span>
														</div>
													</td>
													<td>
														<span class="subTh">原指令序号</span>
														<div class="column6">
															<span class="singleLine"><?php echo empty($unComVal['originalOrderSeq'])?'--':$unComVal['originalOrderSeq']; ?></span>
														</div>
													</td>
													<td>
														<span class="subTh">订单日期</span>
														<div class="column7">
															<span class="singleLine"><?php echo empty($unComVal['cdate'])?'--':$unComVal['cdate']; ?></span>
														</div>
													</td>
													<!--业务类型为转换显示字段(巨额赎回标志、转换基金)-->
													<?php }elseif ($unComVal['typeCode']=='036') {?>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>
														<span class="subTh">转换基金</span>
														<div class="column5">
															<span><?php echo $unComVal['targetFundName']; ?></span>
															<span><?php echo $unComVal['targetFundCode']; ?></span>
														</div>
													</td>
													<td>
														<span class="subTh">巨额赎回标志</span>
														<div class="column6">
															<span class="singleLine"><?php echo $unComVal['largeRedemptionFlag']; ?></span>
														</div>
													</td>
													<td>
														<span class="subTh">订单日期</span>
														<div class="column7">
															<span class="singleLine"><?php echo empty($unComVal['cdate'])?'--':$unComVal['cdate']; ?></span>
														</div>
													</td>
													<?php } ?>
												</tr>
												<?php } ?>
											</tbody>
										</table>
										
										<div class="btmOp fixed">
											<div class="ckAll">
												<label class="ckLabel"><input type="checkbox" name="orderAllCk" id="" value="" class="ckInput ckAllInput" />全选</label>
											</div>
											<div class="btnFl">
												<a class="buttonB" href="javascript:void(0);" id="excOrder">确认执行</a>
											</div>
											<div class="btnFl">
												<a class="buttonB" href="javascript:void(0);" id="delOrder">删除指令</a>
											</div>
											<div class="btnFr dropWarp">
												<a class="buttonB btnN" href="javascript:void(0);">下单</a>
												<div class="dropList">
<!-- 												    <input name="" id="" value="" class="opacityFile" type="file"> -->
													<a class="buttonB importBtn" href="javascript:void(0);">导入下单指令</a>
													<a class="buttonB orderBtn" href="javascript:void(0);">人工下单</a>
												</div>
											</div>
										</div>
										<!--btmOp end-->
										<?php } ?>
										<input name="excel" id="fileToUpload" value="" class="opacityFile" type="file">
									</form>
								</div>
								<!--未下单tabCnt end -->
								<div class="tabCnt">
								<?php if (empty($data['committedOrder'])){ ?>
								<div class="nodata"><span>暂无查询结果</span></div>
								<?php }else{ ?>
									<table border="0" cellspacing="0" cellpadding="0" class="table order">
										<thead>
											<tr>
												<th width="150"><div class="column1">指令序号</div></th>
												<th><div class="column2">产品简称</div></th>
												<th><div class="column3">基金简称</div></th>
												<th><div class="column4">委托方向</div></th>
												<th><div class="column5">指令（金额/份额）</div></th>
												<th><div class="column6">受理状态</div></th>
												<th><div class="column7">详情</div></th>
											</tr>
										</thead>
										<!--排序样式:需要排序的字段文字后面加<i class="ico sort"></i>；在div上加样式 s_click；升序在div上添加样式sort_up、降序添加sort_down-->
										<tbody id="container">
										<!-- 已下单循环开始 -->
										<?php foreach ($data['committedOrder'] as $comVal){ ?>
											<tr>
												<td><div class="column1"><span><?php echo isset($comVal['extOrderSeq'])?$comVal['extOrderSeq']:'--'; ?></span></div></td>
												<td>
													<div class="column2">
														<span><?php echo isset($comVal['productName'])?$comVal['productName']:'--'; ?></span>
														<span class="colGray"><?php echo isset($comVal['productCode'])?$comVal['productCode']:'--'; ?></span>
													</div>
												</td>
												<td>
													<div class="column3">
														<span><?php echo isset($comVal['fundName'])?$comVal['fundName']:'--'; ?></span>
														<span class="colGray"><?php echo isset($comVal['fundCode'])?$comVal['fundCode']:'--'; ?></span>
													</div>
												</td>
												<td>
													<div class="column4">
														<span class="<?php echo $comVal['typeClass'];?>"><?php echo $comVal['typeName']; ?></span>
													</div>
												</td>
												<td>
													<div class="column5">
														<span><?php echo isset($comVal['order'])?$comVal['order']:'--'; ?></span>
													</div>
												</td>
												<td>
													<div class="column6">
														<span class="<?php echo $comVal['statusClass']; ?>"><?php echo $comVal['statusName']; ?></span>
														<!--受理状态色值为：成功colCG、失败colSB-->
													</div>
												</td>
												<td>
													<div class="column7">
														<i class="ico icoShow"></i>
													</div>
												</td>
											</tr>
											<tr class="hddenFields" style="display: none;">
											<!--业务类型为认、申购，修改分红方式显示字段(分红方式、申请日期)-->
											<?php if (in_array($comVal['typeCode'],['020','022','029'])){ ?>
											    <td>&nbsp;</td>
											    <td>&nbsp;</td>
											    <td>&nbsp;</td>
											    <td>&nbsp;</td>
											    <td>&nbsp;</td>
											    <td>
													<span class="subTh">订单日期</span>
													<div class="column6">
														<span class="singleLine"><?php echo isset($comVal['cdate'])?$comVal['cdate']:'--'; ?></span>
													</div>
												</td>
											    <td>
													<span class="subTh">分红方式</span>
													<div class="column7">
														<span class="singleLine"><?php echo isset($comVal['bonus'])?$comVal['bonus']:'--'; ?></span>
													</div>
												</td>
												<!--业务类型为赎回显示字段(巨额赎回标志、申请日期)-->
											<?php }elseif ($comVal['typeCode']=='024'){ ?>
											     <td>&nbsp;</td>
											     <td>&nbsp;</td>
											     <td>&nbsp;</td>
											     <td>&nbsp;</td>
											     <td>&nbsp;</td>
											     <td>
													<span class="subTh">巨额赎回标志</span>
													<div class="column6">
														<span class="singleLine"><?php echo isset($comVal['largeRedemptionFlag'])?$comVal['largeRedemptionFlag']:'--'; ?></span>
													</div>
												</td>
												<td>
													<span class="subTh">订单日期</span>
													<div class="column7">
														<span class="singleLine"><?php echo isset($comVal['cdate'])?$comVal['cdate']:'--'; ?></span>
													</div>
												</td>
												<!--业务类型为撤单显示字段(原委托方向、申请日期、原指令序号)-->
											<?php }elseif ($comVal['typeCode']=='053'){ ?>
											     <td>&nbsp;</td>
											     <td>&nbsp;</td>
											     <td>&nbsp;</td>
											     <td>&nbsp;</td>
											     <td>
													<span class="subTh">原委托方向</span>
													<div class="column5">
														<span class="singleLine"><?php echo isset($comVal['originalTypeName'])?$comVal['originalTypeName']:'--'; ?></span>
													</div>
												</td>
												<td>
													<span class="subTh">原指令序号</span>
													<div class="column6">
														<span class="singleLine"><?php echo isset($comVal['originalOrderSeq'])?$comVal['originalOrderSeq']:'--'; ?></span>
													</div>
												</td>
												<td>
													<span class="subTh">订单日期</span>
													<div class="column7">
														<span class="singleLine"><?php echo $comVal['cdate']; ?></span>
													</div>
												</td>
												<!--业务类型为转换显示字段(转换基金、申请日期、巨额赎回标志)-->
											<?php }elseif ($comVal['typeCode']=='036'){ ?>
											     <td>&nbsp;</td>
											     <td>&nbsp;</td>
											     <td>&nbsp;</td>
											     <td>&nbsp;</td>
											     <td>
													<span class="subTh">转换基金</span>
													<div class="column4">
														<span><?php echo isset($comVal['targetFundName'])?$comVal['targetFundName']:'--'; ?></span>
														<span><?php echo isset($comVal['targetFundCode'])?$comVal['targetFundCode']:'--'; ?></span>
													</div>
												</td>
												<td>
													<span class="subTh">巨额赎回标志</span>
													<div class="column5">
														<span class="singleLine"><?php echo isset($comVal['largeRedemptionFlag'])?$comVal['largeRedemptionFlag']:'--'; ?></span>
													</div>
												</td>
												<td>
													<span class="subTh">订单日期</span>
													<div class="column7">
														<span class="singleLine"><?php echo $comVal['cdate']; ?></span>
													</div>
												</td>
											<?php } ?>
											</tr>
											<?php } ?>
											<!-- 已下单循环结束 -->
										</tbody>
									</table>
									<?php if ($data['committedTotalPage']>1){ ?>
									<!--分页开始-->
									<div class="pagesWarp">
										<div class="pages">
											<a href="javascript:void(0);" class="page pLink firstPage" style="display: none;"><< 首页</a>
											<a href="javascript:void(0);" class="page pLink prevPage" style="display: none;">< 上一页</a>
											<span class="page pSpan curPage"></span>条，共
											<span class="page pSpan totalNum"></span>条
											<a href="javascript:void(0);" class="page pLink nextPage">下一页></a>
											<a href="javascript:void(0);" class="page pLink lastPage">尾页>></a>
										</div>
									</div>
									<!--分页结束-->
									<?php } ?>
									<?php } ?>
								</div>
								<!--已下单tabCnt end-->
							</div>
							<!--tabCnts end-->
						</div>
						<!--tableWarp end-->
					</div>
					<!--content end-->
				</div>
				<!--rightBar end-->
			</div>
			<!--Side end-->
		</div>
		<!--main end-->
<!--导入popUp 开始-->
<div class="popUp popUpImport">
	<div class="popUpWarp">
		<div class="popUpMain">
			<div class="popUpTop">
				<span class="popUpT">汇成基金</span>
				<a href="javascript:void(0);" class="popClose">X</a>
			</div>
			<div class="popUpCnt">
				<div class="popTip">
					<span class="popIco"><img src="../images/ico_exl.jpg"/></span>
					<span class="popTipTxt">汇成基金温馨提醒您！</span>
				</div>
			</div>
			<div class="popUpfoot">
				<a class="buttonB sureBtn" id="doImport" href="javascript:void(0);">确认导入</a>
				<span class="space"></span>
				<a class="buttonC cancelBtn" href="javascript:void(0);">重新导入</a>
			</div>
		</div>
	</div>
</div>
<!--导入popUp 结束-->
<!--下单popUp 开始-->
<div class="popUp popUpForm">
	<div class="popUpWarp">
		<div class="popUpMain">
			<div class="popUpTop">
				<span class="popUpT">人工下单</span>
				<a href="javascript:void(0);" class="popClose">X</a>
			</div>
			<form action="" method="post">
			    <input type="hidden" name="productCode" id="productCode" value=""  /><!-- 产品代码 -->
			    <input type="hidden" name="productName" id="productName" value=""  /><!-- 产品名称 -->
				<div class="popUpCnt">
					<div class="orderForm">
						<div class="sItem">
							<label class="sLabel">交易账户</label>
							<div class="sWarp s_show">
								<div class="sItemed s_select">
									<span class="sltedCnt" id="productFirst"></span>
									<i class="ico icoDown"></i>
									<input type="hidden" name=""  value="" class="valInput" />
								</div>
								<div class="s_opts">
									<ul id='productList'>
                                    <!-- js写入产品列表 -->
									</ul>
								</div>
							</div>
						</div>
						<!--sItem end-->
						<div class="sItem">
							<label class="sLabel">委托方向</label>
							<div class="sWarp">
								<div class="sItemed s_select">
									<span class="sltedCnt">基金认购</span>
									<i class="ico icoDown"></i>
									<input type="hidden" name="" id="typeCode" value="" class="valInput" />
								</div>
								<div class="s_opts fxList">
									<ul>
										<li class="s_opt s_opted" thisVal="020">基金认购</li>
										<li class="s_opt" thisVal="022">开基申购</li>
										<li class="s_opt" thisVal="024">开基赎回</li>
										<li class="s_opt" thisVal="036">基金转换</li>
<!-- 										<li class="s_opt" thisVal="053">撤单</li> -->
										<li class="s_opt" thisVal="029">分红设置</li>
									</ul>
								</div>
							</div>
						</div>
						<!--sItem end-->
						<div class="fxTpyeWarp">
							<!--委托方向-认购-->
							<div class="fxTpye dpyB type020">
								<!--sItem end-->
								<div class="sItem">
									<label class="sLabel">基金代码</label>
									<div class="sWarp">
										<div class="sItemed noPd">
										    <input type="hidden" name="fundName" value="" />
											<input type="text" name="fundCode" id="" value="" class="sInput" placeholder="请输入基金代码/简称/拼音首字母" />
										</div>
									</div>
								</div>
								<!--sItem end-->
								<div class="sItem">
									<label class="sLabel">认购金额(元)</label>
									<div class="sWarp">
										<div class="sItemed">
											<input type="text" name="amount" id="" value="" class="sInput" placeholder="请输入认购金额" />
										</div>
									</div>
								</div>
								<div class="s_tip">最大认购限额5000,000.00元</div><!--提示文本需要显示的请添加class“show” -->
								<!--sItem end-->
								<div class="sItem">
									<label class="sLabel">分红方式</label>
									<div class="sWarp">
										<div class="sItemed s_select">
											<span class="sltedCnt">现金分红</span>
											<i class="ico icoDown"></i>
											<input type="hidden" name="bonus" id="" value="现金分红" class="valInput" />
										</div>
										<div class="s_opts">
											<ul>
												<li class="s_opt" thisVal="现金分红">现金分红</li>
												<li class="s_opt" thisVal="红利再投资">红利再投资</li>
											</ul>
										</div>
									</div>
								</div>
								<!--sItem end-->
							</div>	
							<!--fxTpye end-->
							<!--委托方向-申购-->
							<div class="fxTpye type022">
								<div class="sItem">
									<label class="sLabel">基金代码</label>
									<div class="sWarp">
										<div class="sItemed noPd">
										    <input type="hidden" name="fundName" value="" />
											<input type="text" name="fundCode" id="" value="" class="sInput" placeholder="请输入基金代码/简称/拼音首字母" />
										</div>
									</div>
								</div>
								<!--sItem end-->
								<div class="sItem">
									<label class="sLabel">申购金额(元)</label>
									<div class="sWarp">
										<div class="sItemed">
											<input type="text" name="amount" id="" value="" class="sInput" placeholder="请输入申购金额" />
										</div>
									</div>
								</div>
								<!--sItem end-->
								<div class="s_tip">最大申购限额5000,000.00元</div><!--提示文本需要显示的请添加class“show”<-->
								<div class="sItem">
									<label class="sLabel">分红方式</label>
									<div class="sWarp">
										<div class="sItemed s_select">
											<span class="sltedCnt">现金分红</span>
											<i class="ico icoDown"></i>
											<input type="hidden" name="bonus" id="" value="现金分红" class="valInput" />
										</div>
										<div class="s_opts">
											<ul>
												<li class="s_opt" thisVal="现金分红">现金分红</li>
												<li class="s_opt" thisVal="红利再投资">红利再投资</li>
											</ul>
										</div>
									</div>
								</div>
								<!--sItem end-->
							</div>	
							<!--fxTpye end-->
							<!--委托方向-赎回-->
							<div class="fxTpye type024">
							    <input type="hidden" name="usable" value="" /><!-- 当前可用份额 -->
							    <input type="hidden" name="fundCode" value="" /><!-- 基金代码 -->
							    <input type="hidden" name="fundName" value="" /><!-- 基金名称 -->
								<div class="sItem">
									<label class="sLabel">持仓产品</label>
									<div class="sWarp">
										<div class="sItemed s_select">
											<span class="sltedCnt" id="positionFirst024"> </span>
										</div>
										<div class="s_opts">
											<ul id="positionlist024">
												<!-- js填充持仓列表数据 -->
											</ul>
										</div>
									</div>
								</div>
								<!--sItem end-->
								<div class="sItem">
									<label class="sLabel">赎回份额</label>
									<div class="sWarp">
										<div class="sItemed">
											<input type="text" name="shares" id="" value="" class="sInput" placeholder="请输入赎回份额" />
										</div>
									</div>
								</div>
								<div class="s_tip shares024 show"></div><!--提示文本需要显示的请添加class“show”<-->
								<!--sItem end-->
								<div class="sItem">
									<label class="sLabel">巨额赎回标志</label>
									<div class="sWarp">
										<div class="sItemed s_select">
											<span class="sltedCnt">继续赎回</span>
											<i class="ico icoDown"></i>
											<input type="hidden" name="largeRedemptionFlag" id="" value="继续赎回" class="valInput" />
										</div>
										<div class="s_opts">
											<ul>
												<li class="s_opt" thisVal="继续赎回">继续赎回</li>
												<li class="s_opt" thisVal="放弃超额部分">放弃超额部分</li>
											</ul>
										</div>
									</div>
								</div>
								<!--sItem end-->
							</div>	
							<!--fxTpye end-->
							<!--委托方向-转换-->
							<div class="fxTpye type036">
							    <input type="hidden" name="usable" value="" /><!-- 当前可用份额 -->
							    <input type="hidden" name="fundCode" value="" /><!-- 基金代码 -->
							    <input type="hidden" name="fundName" value="" /><!-- 基金名称 -->
								<div class="sItem">
									<label class="sLabel">转出产品</label>
									<div class="sWarp">
										<div class="sItemed s_select">
											<span class="sltedCnt" id="positionFirst036"> </span>
										</div>
										<div class="s_opts">
											<ul id="positionlist036">
												<!-- js填充持仓列表数据 -->
											</ul>
										</div>
									</div>
								</div>
								<!--sItem end-->
								<div class="sItem">
									<label class="sLabel">转入基金</label>
									<div class="sWarp">
										<div class="sItemed noPd">
											<input type="text" name="targetFundCode" id="" value="" class="sInput" placeholder="请输入基金代码/简称/拼音首字母" />
											<input type="hidden" name="targetFundName" value="" /> 
										</div>
									</div>
								</div>
								<!--sItem end-->
								<div class="sItem">
									<label class="sLabel">转换份额</label>
									<div class="sWarp">
										<div class="sItemed">
											<input type="text" name="shares" id="" value="" class="sInput" placeholder="请输入转换份额" />
										</div>
									</div>
								</div>
								<!--sItem end-->
								<div class="s_tip shares036 show"></div><!--提示文本需要显示的请添加class“show-->
								<div class="sItem">
									<label class="sLabel">巨额赎回标志</label>
									<div class="sWarp">
										<div class="sItemed s_select">
											<span class="sltedCnt">继续赎回</span>
											<i class="ico icoDown"></i>
											<input type="hidden" name="largeRedemptionFlag" id="" value="继续赎回" class="valInput" />
										</div>
										<div class="s_opts">
											<ul>
												<li class="s_opt" thisVal="继续赎回">继续赎回</li>
												<li class="s_opt" thisVal="放弃超额部分">放弃超额部分</li>
											</ul>
										</div>
									</div>
								</div>
								<!--sItem end-->
								
							</div>	
							<!--fxTpye end-->
							<!--委托方向-撤单-->
<!-- 							<div class="fxTpye"> -->
<!-- 								<div class="sItem"> -->
<!-- 									<label class="sLabel">选择交易</label> -->
<!-- 									<div class="sWarp"> -->
<!-- 										<div class="sItemed s_select"> -->
<!-- 											<span class="sltedCnt">认购&nbsp;|&nbsp;60001&nbsp;华夏证券&nbsp;|&nbsp;2000.00元</span> -->
<!-- 											<i class="ico icoDown"></i> -->
<!-- 										</div> -->
<!-- 										<div class="s_opts"> -->
<!-- 											<ul> -->
<!-- 												<li class="s_opt s_opted">认购&nbsp;|&nbsp;60001&nbsp;华夏证券&nbsp;|&nbsp;2000.00元</li> -->
<!-- 												<li class="s_opt">申购&nbsp;|&nbsp;60001&nbsp;华夏证券&nbsp;|&nbsp;2000.00元</li> -->
<!-- 												<li class="s_opt">赎回&nbsp;|&nbsp;60001&nbsp;华夏证券&nbsp;|&nbsp;2000.00元</li> -->
<!-- 												<li class="s_opt">转换&nbsp;|&nbsp;60001&nbsp;华夏证券&nbsp;|&nbsp;2000.00元</li> -->
<!-- 											</ul> -->
<!-- 										</div> -->
<!-- 									</div> -->
<!-- 								</div> -->
								<!--sItem end-->
								
<!-- 							</div>	 -->
							<!--fxTpye end-->
							<!--委托方向-修改分红方式-->
							<div class="fxTpye type029">
							    <input type="hidden" name="usable" value="" /><!-- 当前可用份额 -->
							    <input type="hidden" name="fundCode" value="" /><!-- 基金代码 -->
							    <input type="hidden" name="fundName" value="" /><!-- 基金名称 -->
								<div class="sItem">
									<label class="sLabel">持仓产品</label>
									<div class="sWarp">
										<div class="sItemed s_select">
											<span class="sltedCnt" id="positionFirst029"></span>
										</div>
										<div class="s_opts" id="positionlist029">
											<ul>
												<!-- js填充持仓列表数据 -->
											</ul>
										</div>
									</div>
								</div>
								<!--sItem end-->
								<div class="sItem">
									<label class="sLabel">修改分红方式</label>
									<div class="sWarp">
										<div class="sItemed s_select">
											<span class="sltedCnt" id="bonusFirst"></span>
											<input type="hidden" name="bonus" id="modifybonus" value="" class="valInput" />
										</div>
										<div class="s_opts">
											<ul>
												<li class="s_opt" thisVal="现金分红">现金分红</li>
												<li class="s_opt" thisVal="红利再投资">红利再投资</li>
											</ul>
										</div>
									</div>
								</div>
								<!--sItem end-->
							</div>	
							<!--fxTpye end-->
						</div>
						<!--fxTpyeWarp end-->
					</div>	
					<!--orderForm end-->
				</div>
				<!--popUpCnt end-->
				<div class="popUpfoot">
					<a class="buttonB sureBtn ordersave" href="javascript:void(0);">确认</a>
				</div>
			</form>
		</div>
	</div>
</div>
<link href="/css/autocmp.css" rel="stylesheet">
<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="/js/ajaxfileupload.js"></script>
<!--下单popUp 结束-->
<script type="text/javascript">
var pageFlag = true;//翻页标示
var delFlag = true;//删除标志
var excFlag = true;//执行标志
var formFlag = true;//下单标志
$(document).ready(function(e){
	//导入下单按钮
	$(document).on("click",".importBtn",function(){
		$("#fileToUpload").click();
	});
	//重新导入
	$(document).on("click",".popUpImport .cancelBtn",function(){
		closePopUp();
		$("#fileToUpload").click();
	});
	//打开本地文件事件
	$(document).on("change","#fileToUpload",function(){
		if(validateSuffix($(this).val())){
			popUp(".popUpImport",$(this).val(),"导入下单指令","","ico");//导入成功
			$(".popUpImport .sureBtn,.popUpImport .space").show();
		}else{
			popUp(".popUpImport","您导入的文件格式不正确，请重新导入","导入下单指令","warn");//导入失败
			$(".popUpImport .sureBtn,.popUpImport .space").hide();
		}
	});
	//点击确认导入
	$(document).on("click","#doImport",function(){
		ajaxFileUpload();
		closePopUp();
	});
	//人工下单
	$(document).on("click",".orderBtn",function(){
		popUp(".popUpForm");//导入失败
	});
	//关闭popUp
	$(document).on("click",".popClose",function(){
		closePopUp();
	});
	//弹出的公告
// 	setTimeout(function(){slideDown(".newestNotice",1000)},1000);
	
	//下拉选择
	$(document).on("click",".s_opt",function(){
		var thisVal = $(this).attr("thisval");
		var thisShow = $(this).html();
		if($(this).hasClass('product')){
			$(this).addClass("s_opted").siblings().removeClass("s_opted").parents(".s_opts").hide().siblings(".sItemed").children(".sltedCnt").html(thisShow);
			$("#productCode").val(thisVal);//产品代码
			$("#productName").val($(this).text());//产品名称
			$("li[thisval='020']").click();
		}else if($(this).hasClass('position')){
			$(this).addClass("s_opted").siblings().removeClass("s_opted").parents(".s_opts").hide().siblings(".sItemed").children(".sltedCnt").html(thisShow);
			//赎回持仓选择 填写fundcode fundname shares
			$(this).parents(".fxTpye").children("input[name='fundCode']").val($(this).attr("fundcode"));//基金代码
			$(this).parents(".fxTpye").children("input[name='fundName']").val($(this).attr("fundname"));//基金名称
			$(this).parents(".fxTpye").children(".s_tip").html("可赎回份额"+$(this).attr("shares"));//可用份数显示
			$(this).parents(".fxTpye").children("input[name='usable']").val($(this).attr("shares"));//可用份数
			$("#modifybonus").val($(this).attr("melonmethod"));//添加分红input val
			$("#bonusFirst").html($(this).attr("melonmethod"));//显示分红描述
		}else{
			$(this).addClass("s_opted").siblings().removeClass("s_opted").parents(".s_opts").hide().siblings(".sItemed").children(".sltedCnt").html(thisShow).siblings(".valInput").val(thisVal);
		}
		if(thisVal=="024" || thisVal=="036" || thisVal=='029'){
			getPosition(thisVal);
		}
	})
	/*级联*/
	$(".fxList").on("click","li",function(){
		var thisLi = $(this).index();
		$(".fxTpyeWarp").children(".fxTpye").eq(thisLi).show().siblings().hide();
	});
	/*分页*/
	var totalNum = <?php echo $data['committedTotalNum']; ?>;//总条数
	var totalPage = <?php echo $data['committedTotalPage']; ?>//总页数
	var curPage = 1;//当前页数
	var start=1;//分页开始偏移量
	var offset = 20;//每页条数
	var end;//分页结束偏移量
	//初始化分页数据
	$('.curPage').html('1-'+(start+offset-1));
	$('.totalNum').html(totalNum);
	$(".pLink").on("click",function(){
		if(pageFlag)
		{
			pageFlag = false;//关闭事件触发
			if($(this).hasClass("prevPage"))//上一页
			{
				curPage = curPage-1;
				if($(".lastPage,.nextPage").is(":hidden")){
					$(".lastPage,.nextPage").show();
				}
				if(curPage==1){
					$(".firstPage,.prevPage").hide();
				}
			}else if($(this).hasClass("nextPage"))//下一页
			{
				curPage = curPage+1;
				if($(".firstPage,.prevPage").is(":hidden")){
					$(".firstPage,.prevPage").show();
				}
				if(curPage==totalPage){
					$(".lastPage,.nextPage").hide();
				}
			}else if($(this).hasClass("firstPage"))//首页
			{
				if($(".lastPage,.nextPage").is(":hidden")){
					$(".lastPage,.nextPage").show();
				}
				curPage = 1;
				$(".firstPage,.prevPage").hide();
			}else if($(this).hasClass("lastPage"))//末页
			{
				curPage = totalPage;
				$(".lastPage,.nextPage").hide();
				if($(".firstPage,.prevPage").is(":hidden")){
					$(".firstPage,.prevPage").show();
				}
			}
			$.post('<?php echo $base; ?>/trade/get-pagedata',{page:curPage},
				function(data){
    				//填充数据
    				$('#container').html(data);
    				//重置事件触发
    				pageFlag = true;
    				//更改分页偏移量
    				start = (curPage-1)*offset+1;
    				if(curPage==totalPage){
    					end = totalNum;
    				}else{
    					end = start+offset-1;
    				}
    				$('.curPage').html(start+'-'+end);
				},'html'
			);
		}
	});
	//删除指令
	$("#delOrder").on("click",function(){
		if(delFlag){
			$(this).addClass("noClick").html('删除中...');
			delFlag = false;
    		var ids = [];
    		$("input[name='orderCk']:checked").each(function (i){
    			ids.push($(this).val());
    		});
    		$.post('/trade/del-order',{ids:ids},function(data){
    		     if(data.code=='111'){
    		         hintPop('删除成功');
    		         RemoveObj();
    			 }else{
    				 hintPop('删除失败'+data.desc);
    			 }
    		     delFlag=true;
    		     $("#delOrder").removeClass("noClick").html('删除指令');
    	    },'json'
    	    );
		}
	});
	//执行指令
	$("#excOrder").on("click",function(){
		if(excFlag){
			$(this).addClass("noClick").html('执行中...');
			excFlag = false;
    		var ids = [];
    		$("input[name='orderCk']:checked").each(function (i){
    			ids.push($(this).val());
    		});
    		$.post('/trade/exc-order',{ids:ids},function(data){
    			if(data.code=='111')
    		    {
    			    hintPop('执行成功');
    				RemoveObj();
    			}else{
    				hintPop('执行失败'+data.desc);
    			}
    			excFlag = true;
    			$("#excOrder").removeClass("noClick").html('确认执行');
    			setTimeout("location.reload()",800);
    	    },'json'
    	    );
		}
	});
	function RemoveObj()
	{
		$("input[name='orderCk']:checked").each(function (i){
			$(this).parents("tr").next("tr").remove();
			$(this).parents("tr").remove();
		});
	}
	//获取产品
	$.post('/trade/get-product',null,
			function(data){
	            htmlStr = '';
	            k=0;
		        $.each(data,function(i,item) 	    	    
				{
					console.log(i,item);
					if(k==0){
						 $("#productFirst").html(item);
						  $("#productCode").val(i);
						  $("#productName").val(item);
					} 	 
					htmlStr +='<li class="s_opt product" thisVal="'+i+'">'+item+'</li>';
					k++;
				});
				$('#productList').html(htmlStr);
        	},'json'
    );
    //人工下单提交
	$(document).on("click",".ordersave",function(){
		var productCode = $("#productCode").val();//产品代码
		var productName = $("#productFirst").html();//产品名称
		var amount='';//金额
		var shares='';//份额
		var fundCode='';//基金代码
		var fundName='';//基金名称
		var largeRedemptionFlag='';//大额赎回标记
		var targetFundCode='';//转入基金代码
		var targetFundName='';//转入基金名称
		var type='';//业务类型(中文)
		var bonus='';//分红方式
		typeCode = $("#typeCode").val();
		
	    if(typeCode=='020'){
	    	//认购
	    	type = '基金认购';
	    	amount = $(".type020 input[name='amount']").val();
	    	fundCode = $(".type020 input[name='fundCode']").val();
	    	fundName = $(".type020 input[name='fundName']").val();
	    	bonus = $(".type020 input[name='bonus']").val();
	    	if(isNull(amount)){
	    		hintPop('金额不能为空');return false;
		    } 
		}else if(typeCode=='022'){
			//申购
			type = '开基申购';
			amount = $(".type022 input[name='amount']").val();
	    	fundCode = $(".type022 input[name='fundCode']").val();
	    	fundName = $(".type022 input[name='fundName']").val();
	    	bonus = $(".type022 input[name='bonus']").val();
	    	if(isNull(amount)){
	    		hintPop('金额不能为空');return false;
		    } 
		}else if(typeCode=='024'){
			//赎回
			type='开基赎回';
			shares = $(".type024 input[name='shares']").val();
	    	fundCode = $(".type024 input[name='fundCode']").val();
	    	fundName = $(".type024 input[name='fundName']").val();
	    	largeRedemptionFlag = $(".type024 input[name='largeRedemptionFlag']").val();
	    	if(isNull(shares)){
	    		hintPop('份额不能为空');return false;
		    }
		    usable = $(".type024 input[name='usable']").val();
		    if(shares > usable){
		    	hintPop('赎回份额不能大于可用份额');return false;
			}
		}else if(typeCode=='036'){
			//转换
			type='基金转换';
			shares = $(".type036 input[name='shares']").val();
	    	fundCode = $(".type036 input[name='fundCode']").val();
	    	fundName = $(".type036 input[name='fundName']").val();
	    	largeRedemptionFlag = $(".type036 input[name='largeRedemptionFlag']").val();
	    	targetFundCode = $(".type036 input[name='targetFundCode']").val();//转入基金代码
	    	targetFundName = $(".type036 input[name='targetFundName']").val();//转入基金名称
	    	if(isNull(shares)){
	    		hintPop('份额不能为空');return false;
		    }
		    usable = $(".type036 input[name='usable']").val();
		    if(shares > usable){
		    	hintPop('转换份额不能大于可用份额');return false;
			}
		}else if(typeCode=='029'){
			type = '分红设置';
	    	fundCode = $(".type029 input[name='fundCode']").val();
	    	fundName = $(".type029 input[name='fundName']").val();
	    	bonus = $(".type029 input[id='modifybonus']").val();
	    	shares = $(".type029 input[name='usable']").val();
		}
		else{
			type = '基金认购';
	    	amount = $(".type020 input[name='amount']").val();
	    	fundCode = $(".type020 input[name='fundCode']").val();
	    	fundName = $(".type020 input[name='fundName']").val();
	    	bonus = $(".type020 input[name='bonus']").val();
	    	if(isNull(amount)){
	    		hintPop('金额不能为空');return false;
		    } 
		}
    	if(isNull(fundCode)){
    		hintPop('基金不能为空');return false;
	    }
		//下单提交
		if(formFlag){
			$(this).addClass("noClick").html('处理中...');
			formFlag = false;
        	$.ajax({
    			type : "POST",
    			url : '/trade/save-form',
    			data : {productCode:productCode,productName:productName,amount:amount,shares:shares,fundCode:fundCode,
    				fundName:fundName,largeRedemptionFlag:largeRedemptionFlag,targetFundCode:targetFundCode,targetFundName:targetFundName,
    				type:type,bonus:bonus},
    			dataType :'json',
    			beforeSend : function(){
    				//发送请求前，效果
    			},
    			complete : function(){
    			    //请求完成后，效果消失
    			    $(".ordersave").removeClass("noClick").html('确认');
    				formFlag = true;
    				closePopUp();//关闭人工下单
    				setTimeout("location.reload()",500);
    			},
    			success : function(data){
    				//请求成功后，数据处理
    				if(data.code == '111')
    				{
    					//成功处理..跳转
    					hintPop('下单成功');
    				}else{
    					hintPop('下单失败'+data.desc);
    				}
    			},
    			error : function(){
    			    //请求失败后，数据处理
    				hintPop('下单失败');
    			},
    			statusCode: {
    				404: function() {alert('请求地址不存在');},
    				400: function() {alert('错误请求');},
    				500: function() {alert('服务器开小差');},
    				302: function() {alert('服务器302了');},
    			}
    		});
		}//if 标志end
	});
	//基金下拉自动完成
	//自动完成
	$("input[name='fundCode'],input[name='targetFundCode']").autocomplete({
		  source: "/trade/search-fund",
		  minLength: 1,
		  autoFocus: true,
		  select: function( event, ui ) {
			  if($(this).attr("name")=='fundCode'){
				  $(this).siblings("input[name='fundName']").val(ui.item.name);//填充fundName
			  }else if($(this).attr("name")=='targetFundCode'){
				  $(this).siblings("input[name='targetFundName']").val(ui.item.name);//填充fundName
			  }
		  }
	});
	//为空判断
	function isNull(data){ 
		return (data == "" || data == undefined || data == null || data==0); 
	}
	//获取持仓 typecode 024赎回 
	function getPosition(typecode)
	{
		//获取产品
		$.post('/trade/position',{tradeacco:$("#productCode").val()},
				function(data){
    			    htmlStr = '';
    	            k=0;
    		        $.each(data,function(i,item){
        		        //初始化信息
        				if(i==0){
        					$("#positionFirst"+typecode).html(item.fundcode+'&nbsp;'+item.fundname);//初始选中基金信息
        					$(".shares"+typecode).html("可赎回份额"+item.usableremainshare);//初始可用份额字符串
        					$(".type"+typecode).children("input[name='usable']").val(item.usableremainshare);//可用份额填写
        					$(".type"+typecode).children("input[name='fundCode']").val(item.fundcode);//基金代码
        					$(".type"+typecode).children("input[name='fundName']").val(item.fundname);//基金名称
        					$("#bonusFirst").html(item.melonmethod);//分红方式(初始显示)
        					$("#modifybonus").val(item.melonmethod);//提交input
            			} 	
        				htmlStr +='<li class="s_opt position" fundcode='+item.fundcode+' fundname="'+item.fundname+'" '+
        				'shares="'+item.usableremainshare+'" melonmethod="'+item.melonmethod+'" title="'+item.fundname+'">'+item.fundcode+'&nbsp;'+item.fundname+'</li>';       
    				});

    				$('#positionlist'+typecode).html(htmlStr);
	        	},'json'
	    );
	}
	//ajax上传下单指令
	function ajaxFileUpload()
	{
	   	$.ajaxFileUpload
	   	(
	   		{
	   			url:'/trade/upload-execute',
	   			secureuri:false,
	   			fileElementId:'fileToUpload',
	   			dataType: 'json',
	   			data:{},
	   			success: function (data)
	   			{
	   				if(data.code=='111'){
	   					hintPop('执行成功');
	   					$(".importResult").children(".successTips").html("提示：成功导入"+data.data+"条指令");
	   					slideDown(".importResult",500);
	   					setTimeout("location.reload()",900);
	   				}else{
	   					hintPop(data.desc);
	   				}
	   			},
	   			complete: function(xmlHttpRequest) {
	   				//其他效果
	   			},
	   			error: function (data, status, e)
	   			{
		   			 hintPop('上传失败');
	   			}
	   		}
	   	)
	}
	//验证上传文件是否excel
	function validateSuffix(filename)
	{
		 s = filename.split('.').pop().toLowerCase();
		 if(s=='xls' || s=='xlsx'){
			 return true;
	     }else{
		     return false;
		 }
	}
});
</script>

