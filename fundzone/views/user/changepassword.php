<?php
use yii\helpers\Url;
?>
<section class="content blkP">
    <div class="changePsw">
        <ul>
            <li class="talR">原密码：</li>
            <li>
                <div class="itemSize">
                    <div class="txtItem">
                        <input type="password" name="" id="old_password" value="" class="textInput" placeholder="登录密码" />
                    </div>
                </div>
            </li>
        </ul>
        <ul>
            <li class="talR">新密码：</li>
            <li>
                <div class="itemSize">
                    <div class="txtItem">
                        <input type="password" name="" id="new_password" value="" class="textInput" placeholder="登录密码" />
                    </div>
                </div>
            </li>
        </ul>
        <ul>
            <li class="talR">确认密码：</li>
            <li>
                <div class="itemSize">
                    <div class="txtItem">
                        <input type="password" name="" id="new_password2" value="" class="textInput" placeholder="登录密码" />
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="btn w150">
        <input type="submit" value="确认" name="" id="" class="submit2 changePswButton">
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function(){
        $(".changePswButton").on("click",function(){
            var params = {
                'old_password':$("#old_password").val(),
                'new_password':$("#new_password").val(),
                'new_password2':$("#new_password2").val(),
            }
            $.post("/user/changepasswordverify", params, function(r){
                if (r.code == 0) {
                    hintPop('恭喜您,修改成功！',"hintErrorIco");
                    setTimeout(function(){location.href = "/user/loginout";}, 2000);
                } else {
                    hintPop(r.message,"hintErrorIco");
                }
            }, 'json');

        });
    });
</script>