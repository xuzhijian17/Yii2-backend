	<div class="app_page pT75 fixBut">
		<div class="app_top_fid">
			<div class="app_topbar">
				<div class="app_back"><a href="javascript:history.go(-1);">返回</a></div>
				<div class="app_title">定投详情</div>
				<!--<div class="app_Rlink"><a href="javascript:void(0);" class="cancelOrder">撤单</a></div><!--能否撤单分别用 cancelOrder unCancelOrder-->-->
			</div>
		</div>
		<?php if (!empty($data) && is_array($data)) {
		    foreach ($data as $val){
		    ?>
		    <a class="app_section lineLink" href="/trade/valuavgr-detail?xyh=<?php echo $val['xyh']; ?>">
			<div class="rstWarp">
				<div class="payName">
					<span class="payNameT fzS28"><?php echo $val['fundname']; ?></span>
				</div>
				<?php if ($val['state'] =='A'){ ?>
				<span class="fR fall">正常</span>
				<?php }elseif ($val['state'] =='P'){ ?>
				<span class="fR rise">暂停</span>
				<?php }elseif ($val['state'] == 'H'){ ?>
				<span class="fR flat">终止</span>
				<?php } ?>
				<!--正常fall 暂停rise 终止 flat-->
			</div>
			<!--rstWarp end-->
			<div class="surelySuss">
				<ul class="w50">
					<li><span class="col6 pR5">每期定投</span><span class="col3"><?php echo sprintf('%01.2f',$val['applysum']); ?>元</span></li>
					<li><span class="col6 pR5">累计定投</span><span class="col3"><?php echo sprintf('%01.2f',$val['totalcfmmoney']); ?>元</span></li>
					<li><span class="col6 pR5">扣款时间</span><span class="col3">每月<?php echo $val['jyrq']; ?>日</span></li>
					<li><span class="col6 pR5">下次扣款</span><span class="col3"><?php echo date('Y-m-d',strtotime($val['nextdate'])); ?></span></li>
				</ul>
			</div>
		</a>
		<?php }} ?>
		<!--app_section end-->
		<a href="javascript:alert('建设中')" class="lineLink buttonA_bottom">
			<span class="addIco">新增定投计划</span>
		</a>
	</div>
