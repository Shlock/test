@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>注册账号</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('css')
    <style type="text/css">
        .x-imgverify .ui-input-text{border: 0; margin: 0 0 0 10px; float: left;}
        .x-imgverify .ui-input-text input{height: 40px; width:120px;border: 1px solid #ced6dc;}
    </style>
@stop
@section('content')
    <div data-role="content" style="padding-top:0;">
        <form class="d-box" id="reg-form">
            <div class="d-input">
                <input type="number" name="cellphone" id="cellphone" class="tel" value="" placeholder="请输入手机号码">
            </div>
            <div class="d-input">
                <input type="password" name="password" id="password" value="" placeholder="请输入新密码">
                <!-- <img src="{{ asset('default') }}"> -->
            </div>
            <div class="d-input">
                <input type="password" name="password_new" id="password" value="" placeholder="请确认新密码">
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
            <p>注册视为同意<a href="{{ u('More/detail',['code'=>1]) }}" class="d-declare c-red">《用户注册协议》</a></p>
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
<script type="text/javascript">
    var getcode_url = "{{ u('User/verify') }}";
    var path = "{!! u('User/imgverify')!!}?random=" + Math.random();
    $("#getCode").click(function(){
        if($(this).data("disabled") == "false"){
            return false;
        }
        $(".verifynotice").removeClass('none').show();
    }); 
    $("#changeimg").click(function(){
        $("#imgverify").attr('src',path);
    }); 
    $(".canver").click(function(){
        $('.verifynotice').addClass('none').hide();
        $("input[name=imgverify]").empty();
        $("#imgverify").attr('src',path);
    });
    $(".checkverify").click(function(){
        var imgverify = $("input[name=imgverify]").val();
        mobile = $("#cellphone").val();
        if(mobile != ""){
            var reg = /^1[\d+]{10}$/;
            if(!reg.test(mobile)){
                alert('请输入正确的手机号码'); 
                return false;
            }
        }else{
            alert("手机号码不能为空");
            return false;
        }  
        $.post(getcode_url,{mobile:mobile, type:'reg_check',imgverify:imgverify},function(result){
            if(result.code == 0){
                $.lastTime();
                $("#imgverify").attr('src',path);
                $("input[name=imgverify]").empty();
                $('.verifynotice').addClass('none').hide(); 
               // $.showSuccess(res.msg);
            }else{
                $("#imgverify").attr('src',path);
                $.showError(res.msg);
            }
        },'json');
    });

    //倒计时
    var wait = 60;//获取验证码等待时间(秒)
    $.lastTime = function(){
        if (wait == 0) {
            $("#getCode").data("disabled","true").css("background-color","rgb(82, 178, 246)").css("font-size","0.875em").removeClass("last-time");
            $("#getCode").html("重新发送");
            wait = 60;
        } else {
            if($("#getCode").hasClass("last-time") == false){
                $("#getCode").css("font-size","10px").css("background-color","gray");
                $("#getCode").addClass("last-time")
            }
            $("#getCode").data("disabled","false");//倒计时过程中禁止点击按钮
            $('#getCode').html(wait + " 秒后重新获取");//改变按钮中value的值
            wait--;
            setTimeout(function() {
                $.lastTime();//循环调用
            },1000)
        }
    }
    $(document).on("touchend","#reg",function(){
        var mobile = $("#cellphone").val();
        var verify = $("#identify").val();
        var pwd = $("#password").val();
        var pwds = $("input[name=password_new]").val();
        var reg = /^1[3|4|5|8][0-9]\d{8}$/;
        if(!reg.test(mobile)){
            $.showError("请输入正确的手机号码");
            return false;
        }
		var data = {
            mobile:mobile,
            pwd:pwd,
            verifyCode:verify,
            pwds:pwds
        };

        if(data.verifyCode ==  ''){
            $.showError("请输入验证码");
            return false;
        }

		if(data.pwd == '' || data.pwds == ''){
            $.showError("密码不能为空");
            return false;
        }
        if(data.pwd != data.pwds){
            $.showError("两次密码密码不一致");
            return false;
        }
		$.post("{{ u('User/doreg') }}",data,function(res){
			if(res.code == 0){
				// $.showSuccess(res.msg,"{!! $return_url !!}");
                window.location.href = "{!! $return_url !!}";
			}else{
				$.showError(res.msg);
			}
		},"json");
    })
</script>
@stop

