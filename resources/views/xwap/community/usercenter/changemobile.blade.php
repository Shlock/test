@extends('xwap.community._layouts.base')

@section('css')
<style type="text/css">
    #mobile{width: 100%}
    #code{width: 63%}
    #getCode{width: 35%;border-radius: 5px;text-align: center;background-color: #ff2b4b;color: #fff}
    .d-input{line-height: 1.5;padding-top: 10px;}
</style>
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">换绑手机号</h1>
    </header>
@stop

@section('content')
    <!-- <div data-role="content" style="padding-top:0;">
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
    </div> -->

    <div class="content" id=''>
        <div class="list-block">
            <ul class="y-wdzh y-sz">
                <li></li>
                <li>
                    <div class="item-content">
                        <div class="item-inner y-yzm">
                            <div class="item-input">
                                <input type="text" name="mobile" id="mobile" class="tel" value="" placeholder="请输入手机号码" maxlength="11">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item-content">
                        <div class="item-inner y-yzm">
                            <div class="item-input">
                                <input type="text" placeholder="输入验证码" class="" id="code">
                                <a href="#" class="item-link list-button f14" id="getCode">获取验证码</a>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <p class="y-bgnone mb10"><a class="y-paybtn f16" id="submit">确定</a></p>
    </div>
@stop

@section($js)
<script type="text/javascript">
    var getcode_url = "{{ u('User/verify') }}";
    $(document).on("touchend","#getCode",function(event){
        if($(this).data("disabled") == "false"){
            return false;
        }
        event.preventDefault();
        event.stopPropagation();
        event.isImmediatePropagationStopped();
        var mobile = $("#mobile").val();
        var reg = /^1\d{10}$/;
        if(!reg.test(mobile)){
            $.alert("请输入正确的手机号码");
            return false;
        }
        $.post(getcode_url,{mobile:mobile,type:"reg"},function(res){
            if(res.code == 0){
                $.lastTime();
            }
            $.alert(res.msg);
        },"json");
    })
    //倒计时
    var wait = 60;//获取验证码等待时间(秒)
    $.lastTime = function(){
        alert(123)
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
            $.alert("请输入正确的手机号码");
            return false;
        }
        $.post("{{ u('UserCenter/dochangemobile') }}",{mobile:mobile,code:verify},function(res){
            if(res.code == 0){
                $.router.load("{{ u('UserCenter/info') }}", true);
            }else{
                $.alert(res.msg);
            }
        },"json");
    })
</script>
@stop
