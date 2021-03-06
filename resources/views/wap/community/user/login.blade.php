@extends('wap.community._layouts.base')
@section('show_top')
<div data-role="header" data-position="fixed" class="x-header">
    <h1>登录</h1>
    <a href="{{ u('Index/index') }}" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    <a class="x-sjr ui-btn-right" href="{{ u('User/reg') }}">注册</a>
</div>
@stop
@section('css')
<style type="text/css">
    .x-imgverify .ui-input-text{border: 0; margin: 0 0 0 10px; float: left;}
    .x-imgverify .ui-input-text input{height: 40px; width:120px;border: 1px solid #ced6dc;}
</style>
@stop
@section('content')
@if($quicklogin == 2)
<ul class="y-dlqh">
    <li class="on"><a href="javascript:;">普通登录</a></li>
    <li><a href="{{ u('User/login',array('quicklogin' => 1)) }}">短信快捷登录</a></li>
</ul>
<div data-role="content" style="padding-top:0;">
    <form class="d-box">
        <div class="d-input">
            <input type="number" name="cellphone" id="cellphone" class="tel" placeholder="请输入手机号码" maxlength="11">
        </div>
        <div class="d-input">
            <input type="password" name="password" id="password" placeholder="请输入密码">
            <!-- <img src="{{ asset('wap/community/client/images/ico/passwordimg.png') }}"> -->
        </div>
        <div class="y-buttoncol">
            <a href="javascript:;" data-role="button" id="login">登录</a>
        </div>
        <div class="d-yyyzm clearfix">
            <!-- <a href="" class="fl y-wxkjdl"><img src="images/ico/wxdl.png">微信快捷登录</a> -->
            <a href="{{ u('User/repwd') }}" class="fr">忘记密码？</a>
        </div>
    </form>
</div>

@else
<ul class="y-dlqh">
    <li><a href="{{ u('User/login',array('quicklogin' => 2)) }}">普通登录</a></li>
    <li class="on"><a href="javascript:;">短信快捷登录</a></li>
</ul>
<div data-role="content" style="padding-top:0;">
    <form class="d-box">
        <div class="d-input">
            <input type="number" name="cellphone" id="cellphone" class="tel" value="" maxlength="11" placeholder="请输入手机号码">
        </div>
        <div class="d-sjyzm">
            <div class="d-input d-identify" style="clear: both;">
                <input type="text" name="identify" id="identify" value="" maxlength="6" placeholder="请输入验证码">
            </div>
            <div class="d-codebtn d-btn">

                <a href="javascript:;" data-role="button" id="getCode">获取验证码</a>
                <!-- <a href="" data-role="button" class="none" style="font-size: 12px; background-color: gray;">59秒后重新发送</a>
                    <a href="" data-role="button" class="none">重新发送</a> -->
            </div>
        </div>
        <div class="d-yyyzm clearfix">
            <!--  <a href="" class="fl">注册</a>-->
            <a href="{{ u('User/repwd') }}" class="fr">忘记密码？</a>
        </div>
        <div class="y-buttoncol">
            <a href="javascript:;" data-role="button" id="login">登录</a>
        </div>
    </form>
</div>
@endif
<!-- content end -->
<!-- 图形验证码框 -->
<div class="yz_color_style none verifynotice">
    <div class="m-tkbg">
        <div class="x-tkbg">
            <div class="x-tkbgi">
                <div class="m-tkny">
                    <p class="m-tktt">
                        <span class="">请输入图片验证码</span>
                    </p>
                    <div class="m-tkinfor">
                        <p class="x-tkfont m-tktextare x-imgverify">
                            <input type="text" name="imgverify" class="fl">
                            <img src="{{ u('User/imgverify')}}" class="fl ml10" id="imgverify">
                            <span id="changeimg" class="fl ml10 mt15" style="cursor:pointer;"> 换一张</span>
                        </p> 
                        <ul class="x-tkbtns x-tkbtnstip clearfix">
                            <li class="x-tksure"><a href="javascript:;" class="x-btns ui-btns checkverify" data-ajax="false">确定</a></li>
                            <li class="x-tkcansel canver"><a href="javascript:;" class="x-btns ui-btns u-stop">返回</a></li>   
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var getcode_url = "{{ u('User/verify') }}";
    var path = "{!! u('User/imgverify')!!}?random=" + Math.random();

    $("#getCode").click(function () {
        sendCode();
        //$(".checkverify").trigger('click');
    });
    $("#changeimg").click(function () {
        $("#imgverify").attr('src', path);
    });
    $(".canver").click(function () {
        $('.verifynotice').addClass('none').hide();
        $("#imgverify").attr('src', path);
    });
    $(".checkverify").click(function () {
        mobile = $("#cellphone").val();
        if (mobile != "") {
            var reg = /^1[\d+]{10}$/;
            if (!reg.test(mobile)) {
                alert('请输入正确的手机号码');
                return false;
            }
        } else {
            alert("手机号码不能为空");
            return false;
        }
        
        $(".verifynotice").removeClass('none').show();
        var imgverify = $("input[name=imgverify]").val();
        $.post(getcode_url, {mobile: mobile, imgverify: imgverify}, function (result) {
            if (result.code == 0) {
                $.lastTime();
                $("#imgverify").attr('src', path);
                $("input[name=imgverify]").empty();
                $('.verifynotice').addClass('none').hide();
                sendCode();
            } else {
                $("#imgverify").attr('src', path);
                $("input[name=imgverify]").empty();
                alert(result.msg);
            }
        }, 'json');
    });

    //倒计时
    var wait = 60;//获取验证码等待时间(秒)
    $.lastTime = function () {
        if (wait == 0) {
            $("#getCode").data("disabled", "true").css("background-color", "rgb(82, 178, 246)").css("font-size", "0.875em").removeClass("last-time");
            $("#getCode").html("重新发送");
            wait = 60;
        } else {
            if ($("#getCode").hasClass("last-time") == false) {
                $("#getCode").css("font-size", "14px").css("background-color", "gray");
                $("#getCode").addClass("last-time")
            }

            $("#getCode").data("disabled", "false");//倒计时过程中禁止点击按钮
            $('#getCode').html(wait + " 秒后重新获取");//改变按钮中value的值
            wait--;
            setTimeout(function () {
                $.lastTime();//循环调用
            }, 1000)
        }
    }
    $(document).on("click", "#login", function () {
        var mobile = $("#cellphone").val();
        var verify = $("#identify").val();
        var pwd = $("#password").val();
        var reg = /^1\d{10}$/;
        if (!reg.test(mobile)) {
            $.showError("请输入正确的手机号码");
            return false;
        }
        var type = "{{ $quicklogin }}";
        if (type != 2) {
            var data = {
                mobile: mobile,
                verifyCode: verify
            };
            if (data.verifyCode == '') {
                $.showError("请输入验证码");
                return false;
            }
        } else {
            var data = {
                mobile: mobile,
                pwd: pwd
            };
            if (data.pwd == '') {
                $.showError("请输入密码");
                return false;
            }
        }
        $.post("{{ u('User/dologin') }}", data, function (res) {
            if (res.code == 0) {
                // $.showSuccess(res.msg,"{!! $return_url !!}");
                window.location.href = "{!! $return_url !!}";
            } else {
                $.showError(res.msg);
            }
        }, "json");
    })

    function sendCode() {
        var mobile = $("#cellphone").val();
        if (mobile != "") {
            var reg = /^1[\d+]{10}$/;
            if (!reg.test(mobile)) {
                alert('请输入正确的手机号码');
                return false;
            }
        } else {
            alert("手机号码不能为空");
            return false;
        }
        var imgverify = $("input[name=imgverify]").val();
        $.ajax({
            'url': 'http://api.51mycai365.com/buyer/v1/user.mobileverify2',
            'type': 'post',
            'dataType': 'json',
            'data': {mobile: mobile,verify:imgverify},
            'success': function (response) {
                if (response.code == 0 || 1) {
                    alert('发送成功，请查收短信验证码！');
                    var wait = 60;
                    $.lastTime = function () {
                        if (wait == 0) {
                            $("#getCode").data("disabled", "true").css("background-color", "#ff2d4b").css("font-size", "0.875em").removeClass("last-time");
                            $("#getCode").html("重新发送");
                            wait = 60;
                        } else {
                            if ($("#getCode").hasClass("last-time") == false) {
                                $("#getCode").css("font-size", "10px").css("background-color", "gray");
                                $("#getCode").addClass("last-time")
                            }
                            $("#getCode").data("disabled", "false");//倒计时过程中禁止点击按钮
                            $('#getCode').html(wait + " 秒后重新获取");//改变按钮中value的值
                            wait--;
                            setTimeout(function () {
                                $.lastTime();//循环调用
                            }, 1000)
                        }
                    }

                    $.lastTime();
                } else {
                    alert('短信验证码发送失败，请重新发送');
                }
            },
            'error': function () {
                alert('发送成功，请查收短信验证码！');
                var wait = 60;
                $.lastTime = function () {
                    if (wait == 0) {
                        $("#getCode").data("disabled", "true").css("background-color", "#ff2d4b").css("font-size", "0.875em").removeClass("last-time");
                        $("#getCode").html("重新发送");
                        wait = 60;
                    } else {
                        if ($("#getCode").hasClass("last-time") == false) {
                            $("#getCode").css("font-size", "10px").css("background-color", "gray");
                            $("#getCode").addClass("last-time")
                        }
                        $("#getCode").data("disabled", "false");//倒计时过程中禁止点击按钮
                        $('#getCode').html(wait + " 秒后重新获取");//改变按钮中value的值
                        wait--;
                        setTimeout(function () {
                            $.lastTime();//循环调用
                        }, 1000)
                    }
                }
                $.lastTime();
            }
        });
    }
</script>
@stop
