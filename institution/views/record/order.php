<?php
use yii\helpers\Url;
use institution\components\LeftBarWidget;
use institution\lib\InstCommFun;
$base = Url::base();
$this->title = '汇成基金-交易查询';
?>
<div class="main">
    <div class="Side">
        <?php echo LeftBarWidget::widget();?>
        <!--leftBar end-->
        <div class="rightBar">
            <div class="content">
                <!--
                <div class="newestNotice slideDown">
                    <span class="close">X</span>
                    <span class="date">2016-02-24</span>
                    <span class="horn">最新公告：春节期间基金申购赎回、小金库快赎业务公告</span>
                </div>
                -->
                <!--newestNotice end-->
                <div class="pT10">
                    <div class="titBlk">
                        <div class="titlist tabTag">
                            <ul>
                                <li class="cur"><span class="tabOption">交易申请</span></li>
                                <li><span class="tabOption">交易确认</span></li>
                                <li><span class="tabOption">分红确认</span></li>
                            </ul>
                        </div>
                        <i class="curLine"></i>
                    </div>
                    <!--titBlk end-->
                </div>
                <!--end pT20-->
                <!--没有数据时使用<div class="nodata"><span>暂无查询结果</span></div>-->
                <div class="tableWarp tabContent">
                    <div class="tabCnts">
                        <div class="tabCnt">
                            <!--没有数据使用上面注释的<div class="nodata">-->
                            <div class="ptype dlBlk">
                                <dl>
                                    <dt>账户全称：</dt>
                                    <dd>
                                        <div class="dropBox">
                                            <div class="dbSlted">
                                                <span class="dbSltedVal">全部</span>
                                                <input type="hidden" name="tradeacco1" id="tradeacco1" value="">
                                                <i class="ico icoDown"></i>
                                            </div>
                                            <div class="options options1 optBox">
                                                <ul>
                                                    <?php foreach($accomap as $k=>$v) {?>
                                                    <li tradeacco="<?php echo $k;?>"><?php echo $v;?></li>
                                                    <?php }?>
                                                    <li tradeacco="">全部</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <!--dropBox end-->
                                    </dd>
                                    <dd class="label">日历</dd>
                                    <dd>
                                        <div class="itemWarp">
                                            <div class="txtitem">
                                                <div class="item">
                                                    <input type="text" name="" id="startDate" value="" placeholder="起始日期" class="txtInput startDate" />
                                                    <i class="ico icoDate"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--itemWarp end-->
                                    </dd>
                                    <dd class="label">至</dd>
                                    <dd>
                                        <div class="itemWarp">
                                            <div class="txtitem">
                                                <div class="item">
                                                    <input type="text" name="" id="endDate" value="" placeholder="结束日期" class="txtInput endDate" />
                                                    <i class="ico icoDate"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--itemWarp end-->
                                    </dd>
                                </dl>
                                <dl class="business1">
                                    <dt>业务名称：</dt>
                                    <dd><a class="linkT2 colCur" name="" href="javascript:void(0);">全部</a></dd>
                                    <?php foreach($apply_busname_map as $k=>$value) {?>
                                    <dd><a class="linkT2" name="<?php echo $k;?>" href="javascript:void(0);"><?php echo $value;?></a></dd>
                                    <?php }?>
                                </dl>
                                <dl class="state1">
                                    <dt>申请状态：</dt>
                                    <dd><a class="linkT2 colCur" name="" href="javascript:void(0);">全部</a></dd>
                                    <dd><a class="linkT2" name="valid" href="javascript:void(0);">有效</a></dd>
                                    <dd><a class="linkT2" name="invalid" href="javascript:void(0);">无效</a></dd>
                                    <dd><a class="linkT2" name="uncheck" href="javascript:void(0);">未校验</a></dd>
                                </dl>
                                <dl>
                                    <dt>基金代码：</dt>
                                    <dd>
                                        <div class="itemWarp">
                                            <div class="txtitem">
                                                <div class="item">
                                                    <input type="text" name="product" id="product" value="" placeholder="输入产品代码" class="txtInput" />
                                                </div>
                                            </div>
                                        </div>
                                        <!--itemWarp end-->
                                    </dd>
                                    <dt>下单指令：</dt>
                                    <dd>
                                        <div class="itemWarp">
                                            <div class="txtitem">
                                                <div class="item">
                                                    <input type="text" name="orderseq" id="orderseq" value="" placeholder="输入指令序号" class="txtInput" />
                                                </div>
                                            </div>
                                        </div>
                                        <!--itemWarp end-->
                                    </dd>
                                    <dd><a href="javascript:void(0);" onclick="applyListSearch(1);" class="buttonB" id="tradeApplySearch">查询</a></dd>
                                </dl>

                            </div>
                            <div class="dataarea">
                                <!--ptype end-->
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

                                <div class="nodata error1" <?php if (!empty($applylist['list'])) {?>style="display: none;" <?php }?>>
                                    <span>暂无数据</span>
                                </div>
                                <!--分页开始-->
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
                                    <a class="linkT1 export1" href="javascript:void(0)" onclick="export1();">导出查询结果</a>
                                </div>
                                <!--分页结束-->
                                <?php }?>
                            </div>
                        </div>
                        <!--交易申请tabCnt end -->

                        <!--交易确认tabCnt start-->
                        <div class="tabCnt">
                            <div class="ptype dlBlk">
                                <dl>
                                    <dt>账户全称：</dt>
                                    <dd>
                                        <div class="dropBox">
                                            <div class="dbSlted">
                                                <span class="dbSltedVal">全部</span>
                                                <input type="hidden" name="tradeacco2" id="tradeacco2" value="">
                                                <i class="ico icoDown"></i>
                                            </div>
                                            <div class="options options2 optBox">
                                                <ul>
                                                    <?php foreach($accomap as $k=>$v) {?>
                                                        <li tradeacco="<?php echo $k;?>"><?php echo $v;?></li>
                                                    <?php }?>
                                                    <li tradeacco="">全部</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <!--dropBox end-->
                                    </dd>
                                    <dd class="label">日历</dd>
                                    <dd>
                                        <div class="itemWarp">
                                            <div class="txtitem">
                                                <div class="item">
                                                    <input type="text" name="" id="startDate2" value="" placeholder="起始日期" class="txtInput startDate2" />
                                                    <i class="ico icoDate"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--itemWarp end-->
                                    </dd>
                                    <dd class="label">至</dd>
                                    <dd>
                                        <div class="itemWarp">
                                            <div class="txtitem">
                                                <div class="item">
                                                    <input type="text" name="" id="endDate2" value="" placeholder="结束日期" class="txtInput endDate2" />
                                                    <i class="ico icoDate"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--itemWarp end-->
                                    </dd>
                                </dl>
                                <dl class="business2">
                                    <dt>业务名称：</dt>
                                    <dd><a class="linkT2 colCur" name="" href="javascript:void(0);">全部</a></dd>
                                    <dd><a class="linkT2" name="subscribe" href="javascript:void(0);">认购</a></dd>
                                    <dd><a class="linkT2" name="purchase" href="javascript:void(0);">申购</a></dd>
                                    <dd><a class="linkT2" name="sale" href="javascript:void(0);">赎回</a></dd>
                                    <dd><a class="linkT2" name="convert_in" href="javascript:void(0);">转换入</a></dd>
                                    <dd><a class="linkT2" name="convert_out" href="javascript:void(0);">转换出</a></dd>
                                    <dd><a class="linkT2" name="force_add" href="javascript:void(0);">强制调橧</a></dd>
                                    <dd><a class="linkT2" name="force_red" href="javascript:void(0);">强制调减</a></dd>
                                    <dd><a class="linkT2" name="dividend" href="javascript:void(0);">修改分红方式</a></dd>
                                </dl>
                                <dl class="state2">
                                    <dt>申请状态：</dt>
                                    <dd><a class="linkT2 colCur" name="" href="javascript:void(0);">全部</a></dd>
                                    <?php foreach($confirm_state as $k=>$value) {?>
                                    <dd><a class="linkT2" name="<?php echo $k;?>" href="javascript:void(0);"><?php echo $value;?></a></dd>
                                    <?php }?>
                                </dl>
                                <dl>
                                    <dt>基金代码：</dt>
                                    <dd>
                                        <div class="itemWarp">
                                            <div class="txtitem">
                                                <div class="item">
                                                    <input type="text" name="" id="product2" value="" placeholder="输入产品代码" class="txtInput" />
                                                </div>
                                            </div>
                                        </div>
                                        <!--itemWarp end-->
                                    </dd>
                                    <dt>下单指令：</dt>
                                    <dd>
                                        <div class="itemWarp">
                                            <div class="txtitem">
                                                <div class="item">
                                                    <input type="text" name="" id="orderseq2" value="" placeholder="输入指令序号" class="txtInput" />
                                                </div>
                                            </div>
                                        </div>
                                        <!--itemWarp end-->
                                    </dd>
                                    <dd><a href="javascript:void(0);" class="buttonB" onclick="confirmListSearch();">查询</a></dd>
                                </dl>
                            </div>
                            <!--ptype end-->
                            <div class="dataarea2">
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
                                            <td><div class="column1"><span><?php echo isset($accomap[$value['tradeacco']])?$accomap[$value['tradeacco']]:'';?></span></div></td>
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
                                            <?php }elseif(strpos($businflagStr, "赎回") !== false || strpos($businflagStr, "强制") !== false){?>
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
                                <!--分页结束-->
                            </div>
                        </div>
                        <!--交易确认tabCnt end-->

                        <!--分红确认tabCnt start -->
                        <div class="tabCnt">
                            <!--没有数据使用上面注释的<div class="nodata">-->
                            <div class="ptype dlBlk">
                                <dl>
                                    <dt>账户全称：</dt>
                                    <dd>
                                        <div class="dropBox">
                                            <div class="dbSlted">
                                                <span class="dbSltedVal">全部</span>
                                                <input type="hidden" name="tradeacco3" id="tradeacco3" value="">
                                                <i class="ico icoDown"></i>
                                            </div>
                                            <div class="options options3 optBox">
                                                <ul>
                                                    <?php foreach($accomap as $k=>$v) {?>
                                                        <li tradeacco="<?php echo $k;?>"><?php echo $v;?></li>
                                                    <?php }?>
                                                    <li tradeacco="">全部</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <!--dropBox end-->
                                    </dd>
                                    <dd class="label">日历</dd>
                                    <dd>
                                        <div class="itemWarp">
                                            <div class="txtitem">
                                                <div class="item">
                                                    <input type="text" name="" id="startDate3" value="" placeholder="起始日期" class="txtInput startDate3" />
                                                    <i class="ico icoDate"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--itemWarp end-->
                                    </dd>
                                    <dd class="label">至</dd>
                                    <dd>
                                        <div class="itemWarp">
                                            <div class="txtitem">
                                                <div class="item">
                                                    <input type="text" name="" id="endDate3" value="" placeholder="结束日期" class="txtInput endDate3" />
                                                    <i class="ico icoDate"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--itemWarp end-->
                                    </dd>
                                    <dd><a href="javascript:void(0);" class="buttonB" onclick="bonusListSearch();">查询</a></dd>
                                </dl>
                            </div>
                            <!--ptype end-->
                            <div class="dataarea3">
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
                                            <td><div class="column1"><span><?php echo isset($accomap[$value['tradeacco']])?$accomap[$value['tradeacco']]:'';?></span></div></td>
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
                                            <td>&nbsp;</td>
                                            <td>
                                                <span class="subTh">确认日期</span>
                                                <div class="column5">
                                                    <span><?php echo date("Y-m-d", strtotime($value['confirmdate']));?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="subTh">权益登记日</span>
                                                <div class="column6">
                                                    <span class="singleLine"><?php echo date("Y-m-d", strtotime($value['enrolldate']));?></span>
                                                </div>
                                            </td>

                                            <td>
                                                <span class="subTh">登记份额</span>
                                                <div class="column7">
                                                    <span class="singleLine">
                                                        <?php echo !empty($value['enrollshare'])?InstCommFun::money_format($value['enrollshare'],2,'.',','):"--";?>
                                                    </span>
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
                                        <a class="linkT1 export3" href="javascript:void(0)" onclick="export3()">导出查询结果</a>
                                    </div>
                                    <!--分页结束-->
                                <?php }?>
                            </div>
                        </div>
                        <!--分红确认tabCnt end -->
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
<style type='text/css'>
    .table div{margin:0 5px;}.table div.column1{margin:0 5px 0  20px;}
</style>
<script type="text/javascript" src="<?php echo $base; ?>/js/laydate.dev.js"></script>
<script type="text/javascript">
    $(document).ready(function(e){
        /*
         * 日历
         * 交易申请
         */
        laydate({
            elem: '.startDate',
            choose: function(datas){ //选择日期完毕的回调
                console.log('得到：'+startDate);
            }
        });

        laydate({
            elem: '.endDate',
            choose: function(datas){ //选择日期完毕的回调
                console.log('得到：'+endDate);
            }
        });
        /*
         * 日历
         * 交易确认
         */
        laydate({
            elem: '.startDate2',
            choose: function(datas){ //选择日期完毕的回调
                console.log('得到：'+startDate);
            }
        });

        laydate({
            elem: '.endDate2',
            choose: function(datas){ //选择日期完毕的回调
                console.log('得到：'+endDate);
            }
        });
        /*
         * 日历
         * 交易确认
         */
        laydate({
            elem: '.startDate3',
            choose: function(datas){ //选择日期完毕的回调
                console.log('得到：'+startDate);
            }
        });

        laydate({
            elem: '.endDate3',
            choose: function(datas){ //选择日期完毕的回调
                console.log('得到：'+endDate);
            }
        });

        //分类选择
        $(".options1 ul li").on("click",function(e){
            var sltVal = $(this).html();
            var tradeacco = $(this).attr("tradeacco");
            $(this).parents(".options").hide().siblings(".dbSlted").children(".dbSltedVal").html(sltVal);
            $("#tradeacco1").val(tradeacco);
        });

        //业务名称
        $(".business1 dd a").on("click",function(e){
            $(".business1 dd a").removeClass("colCur");
            $(this).addClass("colCur");
        });

        //申请状态
        $(".state1 dd a").on("click",function(e){
            $(".state1 dd a").removeClass("colCur");
            $(this).addClass("colCur");
        });

        //分类选择
        $(".options2 ul li").on("click",function(e){
            var sltVal = $(this).html();
            $(this).parents(".options").hide().siblings(".dbSlted").children(".dbSltedVal").html(sltVal);
            var tradeacco = $(this).attr("tradeacco");
            $("#tradeacco2").val(tradeacco);
        });
        //业务名称
        $(".business2 dd a").on("click",function(e){
            $(".business2 dd a").removeClass("colCur");
            $(this).addClass("colCur");
        });
        //申请状态
        $(".state2 dd a").on("click",function(e){
            $(".state2 dd a").removeClass("colCur");
            $(this).addClass("colCur");
        });

        //分类选择
        $(".options3 ul li").on("click",function(e){
            var sltVal = $(this).html();
            $(this).parents(".options").hide().siblings(".dbSlted").children(".dbSltedVal").html(sltVal);
            var tradeacco = $(this).attr("tradeacco");
            $("#tradeacco3").val(tradeacco);
        });

        //弹出的公告
        setTimeout(function(){slideDown(".newestNotice",1000)},1000);
        //导入成功提示
        setTimeout(function(){slideDown(".importResult",500)},2000);

    });

    //交易申请导出
    function export1()
    {
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var tradeacco = $("#tradeacco1").val();
        var business = $(".business1 dd a.colCur").attr('name');
        var state = $(".state1 dd a.colCur").attr('name');
        var product = $("#product").val();
        var page = $("#apply_pagenum").val();
        page = page == null ? 1 : page;
        var url = "<?= \yii\helpers\Url::to(['record/exportapply/']);?>?"+'page='+page+'&startdate='+startDate+'&enddate='+endDate+'&tradeacco='+tradeacco;
        location.href = url+'&business='+business+'&state='+state+'&product='+product;
    }

    //交易确认导出
    function export2()
    {
        var  startDate = $("#startDate2").val();
        var endDate = $("#endDate2").val();
        var tradeacco = $("#tradeacco2").val();
        var business = $(".business2 dd a.colCur").attr('name');
        var state = $(".state2 dd a.colCur").attr('name');
        var product = $("#product2").val();
        var page = $("#confirm_pagenum").val();
        page = page == null ? 1 : page;
        var url = "<?= \yii\helpers\Url::to(['record/exportconfirm/']);?>?"+'page='+page+'&startdate='+startDate+'&enddate='+endDate+'&tradeacco='+tradeacco;
        location.href = url+'&business='+business+'&state='+state+'&product='+product;
    }

    //分红导出
    function export3()
    {
        var  startDate = $("#startDate3").val();
        var endDate = $("#endDate3").val();
        var tradeacco = $("#tradeacco3").val();
        var page = $("#bonus_pagenum").val();
        page = page == null ? 1 : page;
        var url = "<?= \yii\helpers\Url::to(['record/exportbonus/']);?>?"+'page='+page+'&startdate='+startDate+'&enddate='+endDate+'&tradeacco='+tradeacco;
        location.href = url;
    }

    function eachLoadingAmt() {
        var treq;
        $("table tbody").each(function(){
            if(!$(this).hasClass("noanim")){
                $(this).children("tr").each(function(){
                    treq = $(this).index();
                    if($(this).hasClass("hddenFields")){
                        treq=treq-1
                    }else{

                        $(this).addClass("animation animation-delay-"+treq)
                    }
                });
            }
        });

    }
    //交易申请
    function applyListSearch (page) {
        var  startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var tradeacco = $("#tradeacco1").val();
        var business = $(".business1 dd a.colCur").attr('name');
        var state = $(".state1 dd a.colCur").attr('name');
        var product = $("#product").val();
        var orderseq = $("#orderseq").val();
        var param = {'page':page,'startdate':startDate,'enddate':endDate,'tradeacco':tradeacco,'business':business,'state':state,'product':product,'orderseq':orderseq};

        $.ajax({
            type: 'POST',
            async: true,
            url: '<?= \yii\helpers\Url::to(['record/tradeapply/']);?>',
            dataType: 'json',
            data: param,
            success: function(r){
                if (r.code == 1) {
                    $(".dataarea").html(r.html);
                    eachLoadingAmt();
                }
            }
        });
    }
    //交易确认
    function confirmListSearch (page) {
        var  startDate = $("#startDate2").val();
        var endDate = $("#endDate2").val();
        var tradeacco = $("#tradeacco2").val();
        var business = $(".business2 dd a.colCur").attr('name');
        var state = $(".state2 dd a.colCur").attr('name');
        var product = $("#product2").val();
        var orderseq = $("#orderseq2").val();
        var param = {'page':page,'startdate':startDate,'enddate':endDate,'tradeacco':tradeacco,'business':business,'state':state,'product':product,'orderseq':orderseq};

        $.ajax({
            type: 'POST',
            async: true,
            url: '<?= \yii\helpers\Url::to(['record/tradeconfirm/']);?>',
            dataType: 'json',
            data: param,
            success: function(r){
                if (r.code == 1) {
                    $(".dataarea2").html(r.html);
                    eachLoadingAmt();
                }
            }
        });
    }
    //分红数据
    function bonusListSearch (page) {
        var  startDate = $("#startDate3").val();
        var endDate = $("#endDate3").val();
        var tradeacco = $("#tradeacco3").val();
        var param = {'page':page,'startdate':startDate,'enddate':endDate,'tradeacco':tradeacco};

        $.ajax({
            type: 'POST',
            async: true,
            url: '<?= \yii\helpers\Url::to(['record/tradebonus/']);?>',
            dataType: 'json',
            data: param,
            success: function(r){
                if (r.code == 1) {
                    $(".dataarea3").html(r.html);
                    eachLoadingAmt();
                }
            }
        });
    }
    //查看附件
    function viewAttach(attach)
    {
        if (attach == undefined ||attach == null || attach == "") {
            hintPop('不存在附件');
            return false;
        }
        window.open("<?= \yii\helpers\Url::to(['account/attach']);?>?id="+attach);
    }
</script>