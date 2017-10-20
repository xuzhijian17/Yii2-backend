<section class="content">
    <h2 class="mainT">我的资产</h2>
    <div class="myAst fixed">
        <dl>
            <dt>
                基金总市值：<span class="totalValue"><?php echo sprintf("%.2f", $position['totalsum']+$position['unpaid']); ?>元</span>
            </dt>
            <dd>
                <span class="col96">
                    基金累计盈亏
                </span>
                <span class="<?php echo $position['sumtotalprofitloss']>=0 ?'colRed':'colGreen'; ?> mB10">
                    <?php echo sprintf("%.2f", $position['sumtotalprofitloss']); ?>元
                </span>
                <span>
                    申购中：<?php echo sprintf("%.2f", $position['buying']); ?>元
                </span>
            </dd>
            <dd>
                <a href="/position/everyday/">
                    <span class="col96">
                        昨日盈亏<i class="icoR">></i>
                    </span>
                </a>
                <span class="<?php echo $position['sumdayprofitloss']>=0 ?'colRed':'colGreen'; ?> mB10">
                    <?php echo sprintf("%.2f", $position['sumdayprofitloss']); ?>元
                </span>
                <span>
                    赎回中：<?php echo sprintf("%.2f", $position['selling']); ?>份
                </span>
            </dd>
            <dd>
                <span class="col96">
                    未付收益
                </span>
                <span class=" colRed mB10">
                    <?php echo sprintf("%.2f", $position['unpaid']); ?>元
                </span>
            </dd>
        </dl>
    </div>
</section>
<!--section content end-->
<section class="content" style="margin-bottom: 50px;">
    <div class="tabWarp fixW">
        <div class="tabList w245">
            <ul>
                <li class="tabCur"><span class="">持仓基金</span></li>
                <li><span class="">已赎回基金</span></li>
            </ul>
        </div>
        <div class="tabCnts">
            <div class="tabCnt">
                <table border="0" cellspacing="0" cellpadding="0" class="tableC myastTable">
                    <?php foreach ($position_fund['position_list'] as $k=>$v) {?>
                    <tr>
                        <td><span><?php echo $v['fundname'];?>（<?php echo $v['fundcode'];?>）</span></td>
                        <td><span>总市值</span><span><?php echo sprintf("%.2f", $v['totalsum']);?>元</span></td>
                        <td><span>累计盈亏</span><span class="<?php echo $v['totalprofitloss']>=0 ?'colRed':'colGreen'; ?>"><?php echo sprintf("%.2f", $v['totalprofitloss']);?>元</span></td>
                        <td><span>最新净值</span><span><?php echo $v['pernetvalue'];?></span></td>
                        <td><a href="/position/everyday/?fundcode=<?php echo $v['fundcode'];?>"><span>昨日盈亏<i class="icoR">&gt;</i></span></a><span class="<?php echo $v['dayprofitloss']>=0 ?'colRed':'colGreen'; ?>"><?php echo sprintf("%.2f", $v['dayprofitloss']);?>元</span></td>
                        <td>
                            <span class="setWarp">
                                <i class="ico_set"></i>
                                <span class="set">
                                    <a href="/trade/purchase-page?fundcode=<?php echo $v['fundcode'];?>" class="link">申购</a>
                                    <a href="/trade/sell-page?fundcode=<?php echo $v['fundcode'];?>" class="link">赎回</a>
                                </span>
                            </span>
                        </td>
                    </tr>
                    <?php }?>
                </table>
            </div>
            <!--tabCnt end-->
            <div class="tabCnt">
                <table border="0" cellspacing="0" cellpadding="0" class="tableC myastTable">
                    <?php foreach ($position_fund['unposition_list'] as $k=>$v) {?>
                    <tr>
                        <td><span><?php echo $v['fundname'];?>（<?php echo $v['fundcode'];?>）</span></td>
                        <td><span>总市值</span><span><?php echo sprintf("%.2f", $v['totalsum']);?>元</span></td>
                        <td><span>累计盈亏</span><span class="<?php echo $v['totalprofitloss']>=0 ?'colRed':'colGreen'; ?>"><?php echo sprintf("%.2f", $v['totalprofitloss']);?>元</span></td>
                        <td><span>最新净值</span><span><?php echo $v['pernetvalue'];?></span></td>
                        <td><a href="/position/everyday/?fundcode=<?php echo $v['fundcode'];?>"><span>昨日盈亏<i class="icoR">&gt;</i></span></a><span class="<?php echo $v['dayprofitloss']>=0 ?'colRed':'colGreen'; ?>"><?php echo sprintf("%.2f", $v['dayprofitloss']);?>元</span></td>
                        <td>
                            <span class="setWarp">
                                <i class="ico_set"></i>
                                <span class="set" style="top: 0;">
                                    <a href="/trade/purchase-page?fundcode=<?php echo $v['fundcode'];?>" class="link">申购</a>
                                </span>
                            </span>
                        </td>
                    </tr>
                    <?php }?>
                </table>
            </div>
            <!--tabCnt end-->
        </div>
        <!--tabCnts end-->
    </div>
</section>