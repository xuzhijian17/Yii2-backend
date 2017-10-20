<section class="content" style="margin-bottom: 60px;">
    <div class="tableList">
        <table border="0" cellspacing="1" cellpadding="0" class="tableB">
            <thead>
            <tr>
                <th>日期</th>
                <th>收益（元）</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($everyday_list as $k=>$v) {?>
            <tr>
                <td><?php echo $v['TradeDay'];?></td>
                <td>
                    <?php if ($v['dayprofit']>=0) {?>
                        <span class="colRed">+<?php echo $v['dayprofit'];?></span>
                    <?php }else{?>
                        <span class="colGreen"><?php echo $v['dayprofit'];?></span>
                    <?php }?>
                </td>
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