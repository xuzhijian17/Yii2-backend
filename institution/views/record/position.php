<!--登陆后结束-->
<?php
use institution\lib\InstCommFun;
$order = Yii::$app->request->get('order');
$by = Yii::$app->request->get('by');
$mv = $ui = "";
if (!empty($order) && $order == 'mv'){
    if ($by == 'up') { $mv='sort_up';}elseif ($by == 'down') {$mv='sort_down';}
}
if (!empty($order) && $order == 'ui'){
    if ($by == 'up') { $ui='sort_up';}elseif ($by == 'down') {$ui='sort_down';}
}
$this->title = '汇成基金-持仓查询';
?>
<div class="main">
    <div class="Side">
        <?php echo \institution\components\LeftBarWidget::widget();?>
        <!--leftBar end-->
        <div class="rightBar">
            <div class="content">
                <!--
                <div class="newestNotice slideDown">
                    <span class="close">X</span>
                    <span class="date">2016-02-24</span>
                    <span class="horn">最新公告：春节期间基金申购赎回、小金库快赎业务公告</span>
                </div>
                newestNotice end-->
                <div class="pT20">
                    <div class="titBlk">
                        <div class="titlist">
                            <ul>
                                <li>持仓查询</li>
                            </ul>
                        </div>
                        <div class="select">
                            <div class="sltLabel">
                                <?php $temp_ta = isset($_GET['tradeacco']) ? trim($_GET['tradeacco']) : '';?>
                                <span class="sltT"><?php if (isset($product_list[$temp_ta])) {echo $product_list[$temp_ta];}else{echo '全部产品';}?><i class="ico icoDown"></i></span>
                            </div>
                            <div class="sltOption">
                                <ul>
                                    <?php foreach($product_list as $k=>$v) {?>
                                    <li onclick="location.href='<?php echo $product_url;?>tradeacco=<?php echo $k;?>'"><?php echo $v;?></li>
                                    <?php }?>
                                    <li onclick="location.href='<?php echo $product_url;?>'">全部产品</li>

                                </ul>
                            </div>
                        </div>
                        <!--select end-->
                    </div>
                    <!--titBlk end-->
                </div>
                <!--end pT20-->
                <!--没有数据时使用<div class="nodata"><span>暂无查询结果</span></div>-->
                <?php if (empty($position)) {?>
                    <div class="nodata"><span>暂无查询结果</span></div>
                <?php } else {?>
                <div class="tableWarp">
                    <table border="0" cellspacing="0" cellpadding="0" class="table query">
                        <thead>
                        <tr>
                            <th><div class="column1">账户全称</div></th>
                            <th><div class="column2">产品简称</div></th>
                            <th><div class="column3">持仓&nbsp;|&nbsp;可用份额</div></th>
                            <th><div class="column4 order_click <?php echo $mv;?>" style="cursor:pointer" order="mv">参考市值(元)<i class="ico sort"></i></div></th>
                            <th><div class="column5 order_click <?php echo $ui;?>" style="cursor:pointer" order="ui">未付收益(元)<i class="ico sort"></i></div></th>
                            <th><div class="column6">参考盈亏</div></th>
                        </tr>
                        </thead>
                        <!--排序样式:需要排序的字段文字后面加<i class="ico sort"></i>；在div上加样式 s_click；升序在div上添加样式sort_up、降序添加sort_down-->
                        <tbody>
                        <?php foreach ($position as $k=>$v) {?>
                        <tr>
                            <td><div class="column1"><?php echo $v['extname']; ?></div></td>
                            <td>
                                <div class="column2">
                                    <span><?php echo $v['fundname']; ?></span>
                                    <span class="colGray"><?php echo $v['fundcode']; ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="column3">
                                    <span><?php echo InstCommFun::number_format($v['currentremainshare']); ?></span>
                                    <span><?php echo InstCommFun::number_format($v['usableremainshare']); ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="column4">
                                    <span><?php echo InstCommFun::number_format($v['marketvalue']);?></span>
                                </div>
                            </td>
                            <td>
                                <div class="column5">
                                    <span><?php echo sprintf("%.2f", $v['unpaidincome']);?></span>
                                </div>
                            </td>
                            <td>
                                <div class="column6">
                                    <?php $floating = str_replace(',', '', $v['floating']); ?>
                                    <span class="<?php echo $floating>=0 ? 'colRed': 'colGreen';?>"><?php echo InstCommFun::number_format($v['floating'], 1);?></span>
                                    <?php ?>

                                </div>
                            </td>
                        </tr>
                        <?php }?>
                        </tbody>
                    </table>
                    <a href="<?php echo $export_url;?>" class="linkT1 ">导出查询结果</a>
                </div>
                <!--tableWarp end-->
                <?php }?>
            </div>
            <!--content end-->
        </div>
        <!--rightBar end-->
    </div>
    <!--Side end-->
</div>
<!--main end-->
<script>
    $(document).on("click",".order_click",function(){
        var order = $(this).attr('order');
        var url = '<?php echo $order_url;?>'+'order='+order+"&";
        if($(this).hasClass("sort_down")){
            $(this).removeClass("sort_down");
            $(this).addClass("sort_up");
            url += 'by=up';
        }else if($(this).hasClass("sort_up")){
            $(this).removeClass("sort_up");
            $(this).addClass("sort_down");
            url += 'by=down';
        }else{
            $(this).addClass("sort_up");
            url += 'by=up';
        }
        location.href = url;
    });
</script>