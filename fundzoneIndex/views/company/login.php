<?php
use yii\helpers\Url;
?>
<section class="banner loginWarp" style="margin-bottom: 0;">
    <div class="maxW">
        <div class="loginBlk">
            <h2>企业账户</h2>
            <div class="formLogin">

                    <div class="itemWarp">
                        <div class="loginItem">
                            <div class="txtItem">
                                <input type="text" name="user_name" id="user_name" value="" class="textInput" placeholder="交易账号" />
                            </div>
                        </div>
                        <div class="errorTxt user_name"> <!--error-->
                            *账号错误
                        </div>
                    </div>
                    <!--itemWarp end-->
                    <div class="itemWarp">
                        <div class="loginItem">
                            <div class="txtItem">
                                <input type="password" name="password" id="password" value="" class="textInput" placeholder="登录密码" />
                            </div>
                        </div>
                        <div class="errorTxt password">
                            *密码错误
                        </div>
                    </div>
                    <div class="loginButton">
                        <input type="hidden" name="login_token" id="login_token" value="<?php echo $login_token?>">
                        <input type="submit" class="submit" id="login" name="" value="登陆" />
                    </div>
                    <!--itemWarp end-->

            </div>
            <!--formLogin end-->
        </div>
        <!--loginBlk end-->
    </div>
    <!--end maxW-->
</section>
<script>
    $("#login").click(function(){
        var username = $("#user_name").val();
        var password = $("#password").val();
        var logintoken = $("#login_token").val();
        var referrer_url = '<?php echo $refer_url; ?>';
        if (username == ""){
            $(".user_name").addClass("error");
            $(".user_name").text("*交易账号不能为空");
            return false;
        }
        if (password == ""){
            $(".password").addClass("error");
            $(".password").text("*交易密码不能为空");
            return false;
        }
        $('.user_name').removeClass("error");
        $('.password').removeClass('error');
        $.post("/company/loginverify", {'user_name':username,'password':password,'login_token':logintoken}, function(r){
            if (r.code == 0) {
                if (r.first_login == 1) {
                    location.href = "/";
                } else {
                    location.href = referrer_url;
                }
            } else {
                if (r.type == 1) {
                    $(".password").addClass("error");
                    $(".password").text("*"+r.message);
                } else {
                    $(".user_name").addClass("error");
                    $(".user_name").text("*"+r.message);
                }
            }
        }, 'json');
    })
</script>