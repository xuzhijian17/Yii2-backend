<?php
use institution\lib\InstCommFun;
?>
<table border="0" cellspacing="0" cellpadding="0" class="table order">
    <thead>
    <tr>
        <th width="150"><div class="column1">账户全称</div></th>
        <th><div class="column2">产品简称</div></th>
        <th><div class="column3">分红方式</div></th>
        <th><div class="column4">红利（金额/份额）</div></th>
        <th><div class="column5">发放日</div></th>
        <th><div class="column6">凭证</div></th>
        <th><div class="column7">详情</div></th>
    </tr>
    </thead>
    <!--排序样式:需要排序的字段文字后面加<i class="ico sort"></i>；在div上加样式 s_click；升序在div上添加样式sort_up、降序添加sort_down-->
    <tbody>
    <?php foreach($bonuslist['list'] as $k=>$value) {?>
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
                    <span>
                        <?php if (!isset($value['melonmethod']) || $value['melonmethod'] ===""){
                            echo "--";
                        }else{
                            echo $value['melonmethod'] == 1? '现金分红' : '红利再投资';
                        }?>
                    </span>
                    <!--委托方向，各个方向色值不同分别为：认购colRG、申购colSG、赎回、colSH、转换colZH、撤单colCD、修改分红方式colXG-->
                </div>
            </td>
            <td>
                <div class="column4">
                    <span>
                        <?php echo $value['bonusshare'];?>
                    </span>
                </div>
            </td>
            <td>
                <div class="column5">
                    <span class="colGray"><?php echo date("Y-m-d", strtotime($value['meloncutting']));?></span>
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
            <td>&nbsp;</td>
            <td>
                <span class="subTh">确认日期</span>
                <div class="column4">
                    <span><?php echo date("Y-m-d", strtotime($value['confirmdate']));?></span>
                </div>
            </td>
            <td>
                <span class="subTh">权益登记日</span>
                <div class="column5">
                    <span class="singleLine"><?php echo date("Y-m-d", strtotime($value['enrolldate']));?></span>
                </div>
            </td>

            <td>
                <span class="subTh">登记份额</span>
                <div class="column6">
                    <span class="singleLine">
                        <?php echo !empty($value['enrollshare'])?InstCommFun::money_format($value['enrollshare'],2,'.',','):"--";?>
                    </span>
                </div>
            </td>
            <td>
                <span class="subTh">实发金额（元）</span>
                <div class="column7">
                    <span class="singleLine"><span><?php echo !empty($value['factbonussum'])?InstCommFun::money_format($value['factbonussum'],2,'.',','):"--";?></span></span>
                </div>
            </td>
        </tr>
    <?php }?>
    </tbody>
</table>
<!--分页开始-->
<div class="nodata error3" <?php if (!empty($bonuslist['list'])) {?>style="display: none;" <?php }?>>
    <span>暂无数据</span>
</div>
<?php
if (!empty($bonuslist['page'])) {
    $pager=$bonuslist['page'];?>
    <div class="pagesWarp">
        <div class="pages applypage">
            <?php if ($pager['page']>1) {?>
                <a href="javascript:void(0);" onclick="bonusListSearch(1);" class="page pLink first"><< 首页</a>

                <a href="javascript:void(0);" onclick="bonusListSearch(<?php echo $pager['page']-1; ?>);"  class="page pLink first">< 上一页</a>
            <?php }?>
            <input type="hidden" name="bonus_pagenum" value="<?php echo $pager['page']; ?>" id="bonus_pagenum">
            <span class="page pSpan curPage"><?php echo $pager['start'].'-'.$pager['end']; ?></span>条，共
            <span class="page pSpan"><?php echo $pager['total']; ?></span>条
            <?php if ($pager['page']<$pager['pagecount']) {?>
                <a href="javascript:void(0);" onclick="bonusListSearch(<?php echo $pager['page']+1; ?>);" class="page pLink last">下一页></a>
                <a href="javascript:void(0);" onclick="bonusListSearch(<?php echo $pager['pagecount']; ?>);"  class="page pLink last">尾页>></a>
            <?php }?>
        </div>
        <a class="linkT1 export3 " href="javascript:void(0)" onclick="export3()">导出查询结果</a>
    </div>
    <!--分页结束-->
<?php }?>