@extends('wap.community._layouts.base_no_header')

@section('css')
<style type="text/css">
    .x-imgverify .ui-input-text{border: 0; margin: 0 0 0 10px; float: left;}
    .x-imgverify .ui-input-text input{height: 40px; width:120px;border: 1px solid #ced6dc;}

    .container{
        height: 100%;
        width: 100%;
        background:url('/wap/community/client/images/share/content_bg.jpg') no-repeat;
        overflow:scroll;
        background-size:100% 100%;
        margin: 0px;
        padding: 0px;
    }
    .content{
        margin-top: 120px;
        padding: 15px;
    }
    .d-input{
        opacity: 0.8;
    }
    .buttoncol{
        opacity: 0.8;
    }
</style>
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
@stop
@section('content')
<div class="container">
    <div data-role="content" style="padding-top:0;" class="content">
        <form class="d-box" id="reg-form">
            <input type="hidden" id="sharedByUserId" value="<?php echo Input::get('sharedByUserId'); ?>" />
            <div class="d-input">
                <input type="number" name="cellphone" id="cellphone" class="tel" value="" placeholder="请输入手机号码">
            </div>
            <div class="d-input">
                <input type="password" name="password" id="password" value="" placeholder="请输入密码">
                <!-- <img src="{{ asset('wap/community/client/images/ico/passwordimg.png') }}"> -->
            </div>
            <div class="d-sjyzm">
                <div class="d-input d-identify" style="clear: both;">
                    <input type="text" name="identify" id="identify" value="" placeholder="请输入验证码">
                </div>
                <div class="d-codebtn d-btn">
                    <a href="javascript:;" data-role="button" id="getCode">获取验证码</a>
                    <!-- <a href="" data-role="button" class="" style="font-size: 10px; background-color: gray;">59秒后重新发送</a>
                    <a href="" data-role="button" class="none">重新发送</a> -->
                </div>
            </div>
            <div class="y-buttoncol">
                <a href="javascript:;" id="reg" data-role="button">确定</a>
            </div>
            <p>注册视为同意<a href="/More/detail?code=1&sharedByUserId=<?php echo Input::get('sharedByUserId'); ?> " class="d-declare c-red">《用户注册协议》</a></p>

        </form>
    </div>
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
</div>
<script type="text/javascript">
$(document).ready(function () {
    $('.container').height($(window).height());
});
var getcode_url = "{{ u('User/verify') }}";
var path = "{!! u('User/imgverify')!!}?random=" + Math.random();


var reg_lat = 0.0;//position.coords.latitude;
var reg_lng = 0.0;//position.coords.longitude;
var reg_address = '未知';

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (pos) {
        reg_lat = pos.coords.latitude;
        reg_lng = pos.coords.longitude;

        var geocoder = new qq.maps.Geocoder({
            complete: function (result) {
                reg_address = result.detail.address;
            }
        });
        var coord = new qq.maps.LatLng(reg_lat, reg_lng);
        geocoder.getAddress(coord);

    }, function (err) {
        // 错误的回调  
        // https://developer.mozilla.org/cn/docs/Web/API/PositionError 错误参数  
    }, {
        enableHighAccuracy: true, // 是否获取高精度结果  
        timeout: 5000, //超时,毫秒  
        maximumAge: 0 //可以接受多少毫秒的缓存位置  
                // 详细说明 https://developer.mozilla.org/cn/docs/Web/API/PositionOptions  
    });
}




$("#getCode").click(function () {
    $(".checkverify").trigger('click');
});
$("#changeimg").click(function () {
    $("#imgverify").attr('src', path);
});
$(".canver").click(function () {
    $('.verifynotice').addClass('none').hide();
    $("input[name=imgverify]").empty();
    $("#imgverify").attr('src', path);
});
$(".checkverify").click(function () {
    var imgverify = $("input[name=imgverify]").val();
    mobile = $("#cellphone").val();
    if (mobile != "") {
        var reg = /^1[\d+]{10}$/;
        if (!reg.test(mobile)) {
            alert('请输入正确的手机号码');
        } else {
            if (imgverify.length === 4) {
                $.post(getcode_url, {mobile: mobile, type: 'reg_check', imgverify: imgverify}, function (result) {
                    if (result.code == 0) {
                        $("#imgverify").attr('src', path);
                        $("input[name=imgverify]").empty();
                        $('.verifynotice').addClass('none').hide();
                        counter();
                    } else {
                        $("#imgverify").attr('src', path);
                        $("input[name=imgverify]").attr('placeholder', '图片验证码').val('');
                        $(".verifynotice").removeClass('none').show();
                    }
                }, 'json');
            } else {
                $("input[name=imgverify]").attr('placeholder', '图片验证码').val('');
                $(".verifynotice").removeClass('none').show();
                return false;
            }
        }
    } else {
        alert("手机号码不能为空");
    }
});

$(document).on("touchend", "#reg", function () {
    var mobile = $("#cellphone").val();
    var verify = $("#identify").val();
    var pwd = $("#password").val();
    var pwds = $("input[name=password_new]").val();
    var reg = /^1[\d+]{10}$/;
    var sharedByUserId = $('#sharedByUserId').val();
    if (!reg.test(mobile)) {
        $.showError("请输入正确的手机号码");
        return false;
    }
    var data = {
        mobile: mobile,
        pwd: pwd,
        verifyCode: verify,
        sharedByUserId: sharedByUserId,
        regLat: reg_lat,
        regLng: reg_lng,
        regAddress: reg_address
    };


    if (data.verifyCode == '') {
        $.showError("请输入验证码");
        return false;
    }

    if (data.pwd == '' || data.pwds == '') {
        $.showError("密码不能为空");
        return false;
    }
    $.post("{{ u('User/doreg') }}", data, function (res) {
        if (res.code == 0) {
            alert('注册成功，立即下载My菜app领取优惠券！')
            window.location.href = "http://a.app.qq.com/o/simple.jsp?pkgname=com.fiftyonemycai365.buyer";
        } else {
            $.showError(res.msg);
        }
    }, "json");
});
 function counter() {
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
                }, 1000);
            }
        };
        $.lastTime();
    }
</script>
@stop

