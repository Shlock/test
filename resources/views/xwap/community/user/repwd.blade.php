@extends('xwap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
      <a class="button button-link button-nav pull-left back" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>
      </a>
      <h1 class="title f16">找回密码</h1>
    </header>
@stop

@section('css')
<style type="text/css">
    input#vcode_input{height: 40px;width: 100px;border: 1px solid #ced6dc;}
    .x-tkfont{padding: 2em 10px;font-size: .875em;color: #999;max-height: 260px;overflow: auto;}
</style>
@stop

@section('content')
    <div class="content" id=''>
        <div class="y-box">
            <div class="y-input y-account">
                <!-- <i class="bg"></i> -->
                <i class="icon iconfont">&#xe614;</i>
                <input type="text" name="cellphone" id="cellphone" class="tel" value="" placeholder="请输入手机号码" maxlength="11">
            </div>
            <div class="y-input y-yzm">
                <!-- <div class="y-yzminput"> -->
                    <input type="text" name="identify" id="identify" value="" placeholder="请输入验证码">
                <!-- </div> -->
                <a href="javascript:getCode()" class="button button-big button-fill button-danger" id="getCode">获取验证码</a><!--  y-ddbtn   (class)  59秒重新发送   (文字) -->
            </div>
            <div class="y-input y-password">
                <!-- <i class="bg"></i> -->
                <i class="icon iconfont">&#xe63b;</i>
                <input type="password" name="password" id="password" class="password" value="" placeholder="请输入新密码">
            </div>
            <div class="y-input y-password">
                <!-- <i class="bg"></i> -->
                <i class="icon iconfont">&#xe63b;</i>
                <input type="password" name="password_new" id="password_new" class="password" value="" placeholder="请确认新密码">
            </div>
            <div class="mt15">
                <a href="#" class="button button-big button-fill button-danger" id="repwd">确定</a>
            </div>
        </div>
    </div>
@stop

@section($js)
<script type="text/javascript">
$(function(){
    var getcode_url = "{{ u('User/verify') }}";
    var path = "{!! u('User/imgverify')!!}?random=" + Math.random();
    
    //发送验证码
    function getCode() {
        if($(this).data("disabled") == "false"){
            return false;
        }
        var modal = $.modal({
          title: '请输入图片验证码',
          text: '',
          afterText:  '<div class="yz_color_style verifynotice">'+
                        '<div class="m-tkbg">'+
                            '<div class="x-tkbg">'+
                                '<div class="x-tkbgi">'+
                                    '<div class="m-tkny">'+
                                        '<div class="m-tkinfor">'+
                                            '<p class="x-tkfont m-tktextare x-imgverify">'+
                                                '<input id="vcode_input" type="text" name="imgverify" class="fl">'+
                                                '<img src="{{ u('User/imgverify')}}" class="fl ml10" id="imgverify">'+
                                                '<span id="changeimg" class="fl ml10 mt15" style="cursor:pointer;"> 换一张</span>'+
                                            '</p>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>',
          buttons: [
            {
                text: '返回'
            },
            {
                text: '确定',
                bold: true,
                onClick: function () {
                    // $.alert($("#vcode_input").val());
                    $.checkverify();
                }
            },
          ]
        })
    }

    $(document).on("touchend","#changeimg",function(){
        $("#imgverify").attr('src',path);
    });


    $(document).on("touchend",".canver",function(){
        $("input[name=imgverify]").empty();
        $("#imgverify").attr('src',path);
    });


    $.checkverify = function(){
        var imgverify = $("input[name=imgverify]").val();
        mobile = $("#cellphone").val();
        if(mobile != ""){
            var reg = /^1[\d+]{10}$/;
            if(!reg.test(mobile)){
                $.toast('请输入正确的手机号码'); 
                return false;
            }
        }else{
            $.toast("手机号码不能为空");
            return false;
        }  
        $.post(getcode_url,{mobile:mobile,imgverify:imgverify},function(result){
            if(result.code == 0){
                $.lastTime();
                $("#imgverify").attr('src',path);
                $("input[name=imgverify]").empty();
                $.toast(result.msg);
            }else{
                $("#imgverify").attr('src',path);
                $.toast("验证码错误");
            }
        },'json');
    };

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
            $('#getCode').html(wait + "s 重新发送");//改变按钮中value的值
            wait--;
            setTimeout(function() {
                $.lastTime();//循环调用
            },1000)
        }
    }
    $(document).on("touchend","#repwd",function(){
        var mobile = $("#cellphone").val();
        var verify = $("#identify").val();
        var pwd = $("#password").val();
        var pwds = $("input[name=password_new]").val();
        var reg = /^1[3|4|5|8][0-9]\d{8}$/;
        if(!reg.test(mobile)){
            $.toast("请输入正确的手机号码");
            return false;
        }
        var data = {
            mobile:mobile,
            pwd:pwd,
            verifyCode:verify,
            pwds:pwds
        };

        if(data.verifyCode ==  ''){
            $.toast("请输入验证码");
            return false;
        }

        if(data.pwd == '' || data.pwds == ''){
            $.toast("密码不能为空");
            return false;
        }
        if(data.pwd != data.pwds){
            $.toast("两次密码密码不一致");
            return false;
        }
        $.post("{{ u('User/dorepwd') }}",data,function(res){
            if(res.code == 0){
                // $.showSuccess(res.msg,"{!! $return_url !!}");
                $.router.load("{!! $return_url !!}", true);
            }else{
                $.toast(res.msg);
            }
        },"json");
    });

})
</script>
@stop