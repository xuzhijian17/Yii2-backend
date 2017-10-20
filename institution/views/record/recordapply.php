<?php
use institution\lib\InstCommFun;
?>
<table border="0" cellspacing="0" cellpadding="0" class="table order">
    <thead>
    <tr>
        <th width="150"><div class="column1">账户全称</div></th>
        <th><div class="column2">产品简称</div></th>
        <th><div class="column3">业务名称</div></th>
        <th><div class="column4">申请（金额/份额）</div></th>
        <th><div class="column5">申请状态</div></th>
        <th><div class="column6">凭证</div></th>
        <th><div class="column7">详情</div></th>
    </tr>
    </thead>
    <!--排序样式:需要排序的字段文字后面加<i class="ico sort"></i>；在div上加样式 s_click；升序在div上添加样式sort_up、降序添加sort_down-->
    <tbody>
    <?php foreach($applylist['list'] as $k=>$value) {?>
        <tr>
            <td><div class="column1"><span><?php echo isset($accomap[$value['tradeacco']])?$accomap[$value['tradeacco']]:'';?></span></div></td>
            <td>
                <div class="column2">
                    <span><?php echo $value['fundname'];?></span>
                    <span class="colGray"><?php echo $value['fundcode'];?></span>
                </div>
            </td>
            <td>
                <div class="column3">
                    <span class="<?php echo InstCommFun::getApplyTypeColor($value['businflagStr']);?>">
                        <?php echo $value['businflagStr'];?>
                    </span>
                    <!--委托方向，各个方向色值不同分别为：认购colRG、申购colSG、赎回、colSH、转换colZH、撤单colCD、修改分红方式colXG-->
                </div>
            </td>
            <td>
                <div class="column4">
                    <span>
                        <?php if (strpos($value['businflagStr'], "赎回") !== false || strpos($value['businflagStr'], "转换") !== false) {
                            echo InstCommFun::money_format($value['applyshare'],2,'.',' ').' 份';
                        }else{
                            echo InstCommFun::money_format($value['applysum'],2,'.',' ').' 元';
                        }?>

                    </span>
                </div>
            </td>
            <td>
                <div class="column5">
                    <span><?php echo !empty($value['kkstat'])?$value['kkstat']:'--';?></span><!--无效状态请添加样式colGray-->
                    <!--<span class="colGray"><?php echo date("Y-m-d", strtotime($value['applydate']));?></span>-->
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
            <td>&nbsp;</td>
            <?php
            $businflagStr = isset($value['businflagStr']) ? $value['businflagStr'] : '';
            $applyserial = !empty($value['applyserial'])?$value['applyserial']:"--";
            $orderseq = !empty($value['orderseq'])?$value['orderseq']:"--";
            if (!isset($value['melonmethod']) || $value['melonmethod'] ===""){
                $melonmethod = "--";
            }else{
                $melonmethod = $value['melonmethod'] == 1? '现金分红' : '红利再投资';
            }
            if (!isset($value['mintredeem']) || $value['mintredeem'] ===""){
                $mintredeem = "--";
            }else{
                $mintredeem = $value['mintredeem'] == 1? '继续赎回' : '取消';
            }
            ?>

            <?php if(strpos($businflagStr, "认购") !== false || strpos($businflagStr, "申购") !== false) {?>
                <td>&nbsp;</td>
                <td>
                    <span class="subTh">申请编号</span>
                    <div class="column4">
                        <span class="singleLine"><?php echo $applyserial;?></span>
                    </div>
                </td>
                <td>
                    <span class="subTh">指令序号</span>
                    <div class="column5">
                        <span class="singleLine"><?php echo $orderseq;?></span>
                    </div>
                </td>
                <td>
                    <span class="subTh">分红方式</span>
                    <div class="column6">
                        <span class="singleLine"><?php echo $melonmethod;?></span>
                    </div>
                </td>
            <?php }elseif(strpos($businflagStr, "赎回") !== false) {?>
                <td>&nbsp;</td>
                <td>
                    <span class="subTh">申请编号</span>
                    <div class="column4">
                        <span class="singleLine"><?php echo $applyserial;?></span>
                    </div>
                </td>
                <td>
                    <span class="subTh">指令序号</span>
                    <div class="column5">
                        <span class="singleLine"><?php echo $orderseq;?></span>
                    </div>
                </td>
                <td>
                    <span class="subTh">赎回标志</span>
                    <div class="column6">
                        <span class="singleLine"><?php echo $mintredeem;?></span>
                    </div>
                </td>
            <?php }elseif(strpos($businflagStr, "撤销") !== false) {?>
                <td>&nbsp;</td>
                <td>
                    <span class="subTh">申请编号</span>
                    <div class="column4">
                        <span class="singleLine"><?php echo $applyserial;?></span>
                    </div>
                </td>
                <td>
                    <span class="subTh">指令序号</span>
                    <div class="column5">
                        <span class="singleLine"><?php echo $orderseq;?></span>
                    </div>
                </td>
                <td>
                    <span class="subTh">原申请编号</span>
                    <div class="column6">
                        <span class="singleLine"><?php echo !empty($value['originalapplyserial'])?$value['originalapplyserial']:"--";?></span>
                    </div>
                </td>
            <?php }elseif(strpos($businflagStr, "转换") !== false) {?>
                <td>
                    <span class="subTh">申请编号</span>
                    <div class="column3">
                        <span class="singleLine"><?php echo $applyserial;?></span>
                    </div>
                </td>
                <td>
                    <span class="subTh">指令序号</span>
                    <div class="column4">
                        <span class="singleLine"><?php echo $orderseq;?></span>
                    </div>
                </td>
                <td>
                    <span class="subTh">转入基金简称</span>
                    <div class="column5">
                        <span><?php echo !empty($value['targetfundname'])?$value['targetfundname']:"--";?></span>
                        <span><?php echo !empty($value['targetfundcode'])?$value['targetfundcode']:"--";?></span>
                    </div>
                </td>
                <td>
                    <span class="subTh">赎回标志</span>
                    <div class="column6">
                        <span class="singleLine"><?php echo $mintredeem;?></span>
                    </div>
                </td>
            <?php }elseif(strpos($businflagStr, "分红") !== false) {?>
                <td>&nbsp;</td>
                <td>
                    <span class="subTh">申请编号</span>
                    <div class="column4">
                        <span class="singleLine"><?php echo $applyserial;?></span>
                    </div>
                </td>
                <td>
                    <span class="subTh">指令序号</span>
                    <div class="column5">
                        <span class="singleLine"><?php echo $orderseq;?></span>
                    </div>
                </td>
                <td>
                    <span class="subTh">分红方式</span>
                    <div class="column6">
                        <span class="singleLine"><?php echo $melonmethod;?></span>
                    </div>
                </td>
            <?php } ?>
            <td>
                <span class="subTh">申请日期</span>
                <div class="column7">
                    <span class="singleLine">
                        <?php echo date("Y-m-d", strtotime($value['applydate']));?>
                    </span>
                </div>
            </td>
        </tr>
    <?php }?>
    </tbody>
</table>
<!--分页开始-->
<div class="nodata error1" <?php if (!empty($applylist['list'])) {?>style="display: none;" <?php }?>>
    <span>暂无数据</span>
</div>
<?php
if (!empty($applylist['page'])) {
$pager=$applylist['page'];?>
<div class="pagesWarp">
    <div class="pages applypage">
        <?php if ($pager['page']>1) {?>
            <a href="javascript:void(0);" onclick="applyListSearch(1);" class="page pLink first"><< 首页</a>

        <a href="javascript:void(0);" onclick="applyListSearch(<?php echo $pager['page']-1; ?>);"  class="page pLink first">< 上一页</a>
        <?php }?>
        <input type="hidden" name="apply_pagenum" value="<?php echo $pager['page']; ?>" id="apply_pagenum">
        <span class="page pSpan curPage"><?php echo $pager['start'].'-'.$pager['end']; ?></span>条，共
        <span class="page pSpan"><?php echo $pager['total']; ?></span>条
        <?php if ($pager['page']<$pager['pagecount']) {?>
        <a href="javascript:void(0);" onclick="applyListSearch(<?php echo $pager['page']+1; ?>);" class="page pLink last">下一页></a>
            <a href="javascript:void(0);" onclick="applyListSearch(<?php echo $pager['pagecount']; ?>);"  class="page pLink last">尾页>></a>
        <?php }?>
    </div>
    <a class="linkT1 export1" href="javascript:void(0)" onclick="export1()">导出查询结果</a>
</div>
<!--分页结束-->
<?php }?>