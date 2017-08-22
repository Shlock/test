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
                <div class="d-input" style="text-align: center;">
                    <p style="font-size:14px;">已发送验证码短信到</p>
                    <p><h1>{{$user['mobile']}}</h1></p>
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
                    <a href="javascript:;" data-role="button" id="next">下一步</a>
                </div>
            </form>
        </div>
                <!-- content end -->
        <script type="text/javascript">
            $(document).ready(function(){
                $.getCode(1);
            })
            var getcode_url = "{{ u('UserCenter/verify') }}";
            var mobile = "{{ $user['mobile'] }}";
            //获取验证码
            $.getCode = function(type){
                $.post(getcode_url,{mobile:mobile},function(res){
                    if(res.code == 0){
                        $.lastTime();
                        if(type != 1){
                            $.showSuccess(res.msg);
                        }
                    }else{
                        if(type != 1) {
                            $.showError(res.msg);
                        }
                    }
                },"json");
            }
            $(document).on("touchend","#getCode",function(event){
                if($(this).data("disabled") == "false"){
                    return false;
                }
                event.preventDefault();
                event.stopPropagation();
                event.isImmediatePropagationStopped();
                $.getCode(2);
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
            $(document).on("click","#next",function(){
                var code = $("#code").val();
                $.post("{{ u('UserCenter/doverifymobile') }}",{mobile:mobile,code:code},function(res){
                    if(res.code == 0){
                        window.location.href = "{{ u('UserCenter/changemobile') }}";
                    }else{
                        $.showError(res.msg);
                    }
                },"json");
            })
        </script>
@stop
