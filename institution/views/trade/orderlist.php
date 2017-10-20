                                    <!-- 已下单循环开始 渲染嵌入tbody内 -->
										<?php foreach ($data as $comVal){ ?>
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
														<span class="singleLine"><?php echo $comVal['cdate']; ?></span>
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