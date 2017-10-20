<section class="content" style="margin-bottom: 60px;">
    <div class="tableList fixed">
        <div class="doubleCol">
            <ul>
                <li class="label"><span>基金代码</span></li>
                <li><span><?php echo $detail['FundCode'];?></span></li>
                <li class="label"><span>基金名称</span></li>
                <li><span><?php echo $detail['FundName'];?></span></li>
            </ul>
            <ul>
                <li class="label"><span>交易类型</span></li>
                <li><span><?php echo $detail['trade_type_text'];?></span></li>
                <li class="label"><span>交易状态</span></li>
                <li><span><?php echo $detail['trade_status_text'];?></span></li>
            </ul>
            <ul>
                <li class="label"><span>付费方式</span></li>
                <li><span>柜台付费</span></li>
                <li class="label"><span>收费方式</span></li>
                <li><span><?php if ($detail['ShareType'] == 'A'){echo '前端收费';}elseif($detail['ShareType'] == 'B'){echo '后端收费';}else{echo '其他';}?></span></li>
            </ul>
            <?php if ($detail['TradeType'] == '0' || $detail['TradeType'] == '3') {?>
            <ul>
                <li class="label"><span>申请金额</span></li>
                <li><span><?php echo $detail['ApplyAmount'];?> 元</span></li>
                <li class="label"><span>确认份额</span></li>
                <li><span><?php echo $detail['ConfirmShare'];?> 份</span></li>
            </ul>
            <?php } else {?>
                <ul>
                    <li class="label"><span>申请份额</span></li>
                    <li><span><?php echo $detail['ApplyShare'];?> 份</span></li>
                    <li class="label"><span>确认份额</span></li>
                    <li><span><?php echo $detail['ConfirmShare'];?> 份</span></li>
                </ul>
            <?php }?>
            <ul>
                <li class="label"><span>关联银行卡</span></li>
                <li><span><?php echo !empty($detail['bankname']) ? $detail['bankname'] : '未知';?> |
                        <?php echo substr($detail['bankacco'], 0,6).str_repeat('*',strlen($detail['bankacco'])-10).substr($detail['bankacco'], -4);?></span></li>
                <li class="label"><span>下单时间</span></li>
                <li><span><?php echo $detail['ApplyTime'];?></span></li>
            </ul>
        </div>
        <!--doubleCol end-->
        <?php if (($detail['TradeType'] == 0 || $detail['TradeType'] == 1) && time() < strtotime($detail['TradeDay'].' 15:00:00') && $detail['TradeStatus']!=4) {?>
            <div class="btnR"><a class="submit2 killOrder" href="javascript:void(0);">撤单</a></div>
        <?php }?>
    </div>
    <!--tableList end-->
</section>
<script type="text/javascript">
    $(document).ready(function(){
        $(".killOrder").on("click",function(){
            popUpOPSingle(".popUpOp","撤单后，您的申请将失效，您确定要撤单吗？","提示","确认","verifySure");
            var flat = 0;
            $("#verifySure").click(function(){
                if (flat == 0) {
                    flat = 1;
                    $.post("/trade/withdraw/", {'applyserial': '<?php echo $detail['ApplySerial'];?>'}, function (r) {
                        if (r.code == 0) {
                            location.href = "/record/list/";
                        } else {
                            flat = 0;
                            hintPop(r.message, "hintErrorIco");
                            $(".close").trigger('click');
                            return false;
                        }
                    }, 'json');
                }
            });
        });
        //关闭popUp

    });
</script>