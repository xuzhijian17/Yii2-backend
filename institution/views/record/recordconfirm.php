<?php
use institution\lib\InstCommFun;
?>
    <table border="0" cellspacing="0" cellpadding="0" class="table order">
        <thead>
        <tr>
            <th width="150"><div class="column1">账户全称</div></th>
            <th><div class="column2">产品简称</div></th>
            <th><div class="column3">业务名称</div></th>
            <th><div class="column4">确认（金额/份额）</div></th>
            <th><div class="column5">确认状态</div></th>
            <th><div class="column6">凭证</div></th>
            <th><div class="column7">详情</div></th>
        </tr>
        </thead>
        <!--排序样式:需要排序的字段文字后面加<i class="ico sort"></i>；在div上加样式 s_click；升序在div上添加样式sort_up、降序添加sort_down-->
        <tbody >
        <?php foreach($confirmlist['list'] as $k=>$value) {?>
            <tr>
                <td><div class="column1"><span><?php echo isset($tradeaccomap[$value['tradeacco']])?$tradeaccomap[$value['tradeacco']]:'';?></span></div></td>
                <td>
                    <div class="column2">
                        <span><?php echo $value['fundname'];?></span>
                        <span class="colGray"><?php echo $value['fundcode'];?></span>
                    </div>
                </td>
                <td>
                    <div class="column3">
                        <span class="<?php echo InstCommFun::getConfirmTypeColor($value['businflagStr']);?>">
                            <?php echo $value['businflagStr'];?>
                        </span>
                        <!--委托方向，各个方向色值不同分别为：认购colRG、申购colSG、赎回、colSH、转换colZH、撤单colCD、修改分红方式colXG-->
                    </div>
                </td>
                <td>
                    <div class="column4">
                        <span>
                            <?php if (strpos($value['businflagStr'], "赎回") !== false
                                || strpos($value['businflagStr'], "转换出") !== false
                                || strpos($value['businflagStr'], "调增") !== false
                                || strpos($value['businflagStr'], "调减") !== false) {
                                echo InstCommFun::money_format($value['tradeconfirmshare'],2,'.',' ').' 份';
                            }else{
                                echo InstCommFun::money_format($value['tradeconfirmsum'],2,'.',' ').' 元';
                            }?>

                        </span>
                    </div>
                </td>
                <td>
                    <div class="column5">
                        <span><?php echo !empty($value['confirmflag'])?$value['confirmflag']:'--';?></span><!--无效状态请添加样式colGray-->
                        <span class="colGray"><?php echo date("Y-m-d", strtotime($value['confirmdate']));?></span>
                    </div>
                </td>
                <td>
                    <div class="column6">
                        <a class="icopdf" href="javascript:;" onclick="viewAttach('<?php echo $value['attachmentid'];?>');">PDF</a>
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
                <td>&nbsp;</td>
                <?php
                $businflagStr = isset($value['businflagStr']) ? $value['businflagStr'] : '';
                $applydate = !empty($value['applydate'])?date("Y-m-d", strtotime($value['applydate'])):'--';
                $poundage  = !empty($value['poundage'])?$value['poundage']:"--";
                $netvalue = !empty($value['netvalue'])?$value['netvalue']:"--";
                if (!isset($value['melonmethod']) || $value['melonmethod'] ===""){
                    $melonmethod = "--";
                }else{
                    $melonmethod = $value['melonmethod'] == 1? '现金分红' : '红利再投资';
                }
                $requestshares = InstCommFun::money_format($value['requestshares'],2,'.',' ');
                ?>

                <?php if(strpos($businflagStr, "认购") !== false || strpos($businflagStr, "申购") !== false){?>
                    <td>
                        <span class="subTh">申请日期</span>
                        <div class="column2">
                            <span class="singleLine"><?php echo $applydate; ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">申请金额</span>
                        <div class="column3">
                            <span class="singleLine"><?php echo InstCommFun::money_format($value['requestbalance'],2,'.',' ').' 元';?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">手续费(元)</span>
                        <div class="column4">
                            <span class="singleLine"><?php echo $poundage;?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">单位净值(元)</span>
                        <div class="column5"><span class="singleLine"><?php echo $netvalue;?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">分红方式</span>
                        <div class="column6"><span class="singleLine"><?php echo $melonmethod;?></span>
                        </div>
                    </td>
                <?php }elseif(strpos($businflagStr, "赎回") !== false  || strpos($businflagStr, "强制") !== false){?>
                    <td>&nbsp;</td>
                    <td>
                        <span class="subTh">申请日期</span>
                        <div class="column3">
                            <span class="singleLine"><?php echo $applydate; ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">申请份额</span>
                        <div class="column4">
                            <span class="singleLine"><?php echo $requestshares;?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">手续费(元)</span>
                        <div class="column5">
                            <span class="singleLine"><?php echo $poundage;?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">单位净值(元)</span>
                        <div class="column6">
                            <span class="singleLine"><?php echo $netvalue;?></span>
                        </div>
                    </td>
                <?php }elseif(strpos($businflagStr, "转换出") !== false){?>
                    <td>&nbsp;</td>
                    <td>
                        <span class="subTh">申请日期</span>
                        <div class="column3">
                            <span class="singleLine"><?php echo $applydate; ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">转出份额</span>
                        <div class="column4">
                            <span class="singleLine"><?php echo $requestshares;?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">手续费(元)</span>
                        <div class="column5">
                            <span class="singleLine"><?php echo $poundage;?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">单位净值(元)</span>
                        <div class="column6">
                            <span class="singleLine"><?php echo $netvalue;?></span>
                        </div>
                    </td>
                <?php }elseif(strpos($businflagStr, "转换入") !== false){?>
                    <td>
                        <span class="subTh">申请日期</span>
                        <div class="column2">
                            <span class="singleLine"><?php echo $applydate; ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">转出份额</span>
                        <div class="column3">
                            <span class="singleLine"><?php echo $requestshares;?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">手续费(元)</span>
                        <div class="column4">
                            <span class="singleLine"><?php echo $poundage;?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">单位净值(元)</span>
                        <div class="column5">
                            <span class="singleLine"><?php echo $netvalue;?></span>
                        </div>
                    </td>
                    <td>
                        <span class="subTh">转出基金</span>
                        <div class="column6">
                            <span><?php echo !empty($value['targetfundname'])?$value['targetfundname']:"--";?></span>
                            <span><?php echo !empty($value['targetfundcode'])?$value['targetfundcode']:"--";?></span>
                        </div>
                    </td>
                <?php }elseif(strpos($businflagStr, "分红") !== false){?>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>
                        <span class="subTh">分红方式</span>
                        <div class="column6">
                            <span class="singleLine"><?php echo $melonmethod;?></span>
                        </div>
                    </td>
                <?php }?>
                <td>
                    <span class="subTh">申请编号</span>
                    <div class="column7">
                        <span class="singleLine"><?php echo !empty($value['applyserial'])?$value['applyserial']:"--";?></span>
                    </div>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
    <!--分页开始-->
    <div class="nodata error2" <?php if (!empty($confirmlist['list'])) {?>style="display: none;" <?php }?>>
        <span>暂无数据</span>
    </div>
    <!--分页开始-->
<?php
if (!empty($confirmlist['page'])) {
    $pager=$confirmlist['page'];?>
    <div class="pagesWarp">
        <div class="pages applypage">
            <?php if ($pager['page']>1) {?>
                <a href="javascript:void(0);" onclick="confirmListSearch(1);" class="page pLink first"><< 首页</a>
                <a href="javascript:void(0);" onclick="confirmListSearch(<?php echo $pager['page']-1; ?>);"  class="page pLink first">< 上一页</a>
            <?php }?>
            <input type="hidden" name="confirm_pagenum" value="<?php echo $pager['page']; ?>" id="confirm_pagenum">
            <span class="page pSpan curPage"><?php echo $pager['start'].'-'.$pager['end']; ?></span>条，共
            <span class="page pSpan"><?php echo $pager['total']; ?></span>条
            <?php if ($pager['page']<$pager['pagecount']) {?>
                <a href="javascript:void(0);" onclick="confirmListSearch(<?php echo $pager['page']+1; ?>);" class="page pLink last">下一页></a>
                <a href="javascript:void(0);" onclick="confirmListSearch(<?php echo $pager['pagecount']; ?>);"  class="page pLink last">尾页>></a>
            <?php }?>
        </div>
        <a class="linkT1 export2" href="javascript:void(0)" onclick="export2()">导出查询结果</a>
    </div>
    <!--分页结束-->
<?php }?>