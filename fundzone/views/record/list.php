<section class="content" style="margin-bottom: 60px;">
    <h2 class="mainT">交易记录</h2>
    <div class="tableList">
        <table border="0" cellspacing="1" cellpadding="0" class="tableB">
            <thead>
            <tr>
                <th>申请日期</th>
                <th>基金代码</th>
                <th>基金名称</th>
                <th>基金类型</th>
                <th>操作类型</th>
                <th>申请金额</th>
                <th>申请份额</th>
                <th>关联银行卡</th>
                <th>汇款状态</th>
                <th>交易状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($trade_list as $k=>$v){ ?>
            <tr>
                <td><?php echo date("Y-m-d H:i", strtotime($v['ApplyTime']));?></td>
                <td><?php echo $v['FundCode'];?></td>
                <td><?php echo $v['FundName'];?></td>
                <td><?php echo $v['FundType'];?></td>
                <td><?php echo $v['trade_type_text'];?></td>
                <td><?php echo $v['ApplyAmount'];?></td>
                <td><?php echo $v['ApplyShare'];?></td>
                <td><?php echo !empty($v['bankname']) ? $v['bankname'] : '未知';?> | <?php echo empty($v['bankacco']) ? '未知' : substr($v['bankacco'], -4);?></td>
                <td><?php echo $v['deduct_money_text'];?></td>
                <td><?php echo $v['trade_status_text'];?></td>
                <td><a href="/record/detail/?id=<?php echo $v['id'];?>" class="link">详情</a></td>
            </tr>
            <?php }?>
            </tbody>
        </table>
    </div>
    <div class="pages">
        <a href="<?php echo $pager['page_prev']; ?>" class="pagePrev page">&nbsp;</a>
        <?php foreach($pager['page_number'] as $k=>$v) {?>
            <a href="<?php echo $v;?>" class="page <?php if ($page==$k) {echo 'cur';}?>"><?php echo $k; ?></a>
        <?php }?>
        <a href="<?php echo $pager['page_next']; ?>" class="pageNext page">&nbsp;</a>
    </div>
</section>