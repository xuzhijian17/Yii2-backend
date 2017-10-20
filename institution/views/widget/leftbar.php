<div class="leftBar">
					<div class="userWarp">
						<div class="photo"><img src="/images/photo.jpg" alt=""/></div>
						<div class="userName">
							<span class="dpyIB">交易员 </span>
							<span class="dpyIB"><?php echo empty($user['userName'])?'':$user['userName']; ?></span>
						</div>
						<div class="fixed">
							<a href="/account/loginout" class="icoRound icoUser" title="退出登录"></a>
							<a href="javascript:void(0);" class="icoRound icoCo"></a>
							<a href="/account/update-pwd" class="icoRound icoset" title="修改密码"></a>
						</div>
					</div>
					<!--userWarp end-->
					<div class="subNav">
						<ul>
						<?php foreach ($menu as $val){ ?>
							<li><a href="<?php echo $val['url']; ?>" class="subA <?php echo $val['class']; ?>"><?php echo $val['menu']; ?><i class="ico icoR"></i></a></li>
					    <?php } ?>
						</ul>
					</div>
					<!--subNav end-->
				</div>
				<!--leftBar end-->