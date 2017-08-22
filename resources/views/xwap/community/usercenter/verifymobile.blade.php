@extends('xwap.community._layouts.base')

@section('css')
<style type="text/css">
    #code{width: 63%}
    #getCode{width: 35%;border-radius: 5px;text-align: center;background-color: #ff2b4b;color: #fff}
    .d-input{line-height: 1.5;padding-top: 10px;}
</style>
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">换绑手机号</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <div class="list-block">
            <ul class="y-wdzh y-sz">
                <li>
                    <div class="d-input" style="text-align: center;">
                        <p style="font-size:14px;">已发送验证码短信到</p>
                        <p><h1>{{$user['mobile']}}</h1></p>
                    </div>
                </li>
                <li>
                    <div class="item-content">
                        <div class="item-inner y-yzm">
                            <!-- <div class="item-title label f14">手机验证码</div> -->
                            <div class="item-input">
                                <input type="text" placeholder="输入验证码" class="" id="code">
                                <a href="#" class="item-link list-button f14" id="getCode">点击获取</a>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <p class="y-bgnone mb10"><a class="y-paybtn f16" id="next">下一步</a></p>
    </div>
@stop

@section($js)
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
                    $.alert(res.msg);
                }
            }else{
                if(type != 1) {
                    $.alert(res.msg);
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
                $.router.load("{{ u('UserCenter/changemobile') }}", true);
            }else{
                $.alert(res.msg);
            }
        },"json");
    })
</script>
@stop
