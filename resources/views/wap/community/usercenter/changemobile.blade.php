@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>换绑手机号</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')


        <div data-role="content" style="padding-top:0;">
            <form class="d-box">
                <div class="d-input">
                    <input type="number" name="mobile" id="mobile" class="tel" value="" placeholder="请输入手机号码">
                </div>
                <div class="d-sjyzm">
                    <div class="d-input d-identify" style="clear: both;">
                        <input type="text" name="code" id="code" value="" placeholder="请输入验证码">
                    </div>
                    <div class="d-codebtn d-btn">
                        <a href="javascript:;" data-role="button" class="" id="getCode">获取验证码</a>
                    </div>
                </div>
                <div class="y-buttoncol">
                    <a href="javascript:;" data-role="button" id="submit">确定</a>
                </div>
            </form>
        </div>
                <!-- content end -->
        <script type="text/javascript">
            var getcode_url = "{{ u('User/verify') }}";
            $(document).on("touchend","#getCode",function(event){
                if($(this).data("disabled") == "false"){
                    return false;
                }
                event.preventDefault();
                event.stopPropagation();
                event.isImmediatePropagationStopped();
                var mobile = $("#cellphone").val();
                var reg = /^1\d{10}$/;
                if(!reg.test(mobile)){
                    $.showError("请输入正确的手机号码");
                    return false;
                }
                $.post(getcode_url,{mobile:mobile,type:"reg"},function(res){
                    if(res.code == 0){
                        $.lastTime();
                        $.showSuccess(res.msg);
                    }else{
                        $.showError(res.msg);
                    }
                },"json");
            })
            //倒计时
            var wait = 60;//获取验证码等待时间(秒)
            $.lastTime = function(){
                if (wait == 0) {
                    $("#getCode").data("disabled","true").css("background-color","rgb(82, 178, 246)").css("font-size","0.875em").removeClass("last-time");
                    $("#getCode").html("重新发送");
                    wait = 60;
                } else {
                    if($("#getCode").hasClass("last-time") == false){
                        $("#getCode").css("font-size","14px").css("background-color","gray");
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
            $(document).on("touchend","#submit",function(){
                var mobile = $("#mobile").val();
                var verify = $("#code").val();
                var reg = /^1\d{10}$/;
                if(!reg.test(mobile)){
                    $.showError("请输入正确的手机号码");
                    return false;
                }
                $.post("{{ u('UserCenter/dochangemobile') }}",{mobile:mobile,code:verify},function(res){
                    if(res.code == 0){
                        window.location.href = "{{ u('UserCenter/info') }}";
                    }else{
                        $.showError(res.msg);
                    }
                },"json");
            })
        </script>
@stop
