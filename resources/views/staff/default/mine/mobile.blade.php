@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop

@section('contentcss') p-0-085 login-register @stop
@section('content')
    <ul>
        <li class="w_b">
            <span class="icon iconfont">&#xe651;</span>
            <input type="text"  placeholder="请输入原手机号" id="oldMobile" class="w_b_f_1"/>
        </li>
        <li class="w_b">
            <span class="icon iconfont">&#xe651;</span>
            <input type="text"  placeholder="请输入新手机号" id="newMobile" class="w_b_f_1"/>
        </li>
        <div class="mobile_code_block w_b">
            <input type="text" id="identifys"  placeholder="请输入验证码" class="w_b_f_1"/>
            <input type="button" class="ui-button_getcode"  id="getCode" onclick="$.time()"  value="获取验证码" >
        </div>
    </ul>
    <input class="ui-button_login_register mt090" type="submit" id="submits" value="确定">
@stop
@section($js)
    <script type="text/javascript">
        $(function(){
           var getcode_url = "{{ u('Staff/verify') }}";
            $.time = function(){
                var mobile = $("#newMobile").val(); //{{$staff['mobile'] or ""}};
                if(mobile != ""){
                    var reg = /^1[\d+]{10}$/;
                    if(!reg.test(mobile)){
                        $.toast('请输入正确的手机号码');
                        return false;
                    }
                }else{
                    $.toast("新手机号码不能为空");
                    return false;
                }
                $("#getCode").attr({"disabled":"disabled"});//倒计时过程中禁止点击按钮
                $.post(getcode_url,{mobile:mobile},function(result){
                    if(result.code == 0){
                        $.lastTime();
                        $("input[name=identify]").empty();
                    }else{
                        $.toast(result.msg);
                        $("#getCode").removeAttr("disabled");
                    }
                },'json');
            };
            //倒计时
            var wait = 60;//获取验证码等待时间(秒)
            $.lastTime = function(){
                if (wait == 0) {
                    $("#getCode").removeAttr("disabled").css("background","#ff2c4c").removeClass("last-time");
                    $("#getCode").val("重新发送");
                    wait = 60;
                } else {
                    if($("#getCode").hasClass("last-time") == false){
                        $("#getCode").css("font-size","10px").css("background","#ccc");
                        $("#getCode").addClass("last-time")
                    }
                    $("#getCode").attr({"disabled":"disabled"});//倒计时过程中禁止点击按钮
                    $('#getCode').val(wait + " 秒后重新获取");//改变按钮中value的值
                    wait--;
                    setTimeout(function() {
                        $.lastTime();//循环调用
                    },1000)
                }
            }
            $(document).on("touchend","#submits",function(){
                var oldmobile = $("#oldMobile").val();
                var newMobile = $("#newMobile").val(); //{{$staff['mobile'] or ""}};
                var verify = $("#identifys").val();
                var reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
                if(!reg.test(oldmobile)){
                    $.toast("请输入正确的原手机号码");
                    return false;
                }
                if(!reg.test(newMobile)){
                    $.toast("请输入正确的新手机号码");
                    return false;
                }
                var data = {
                    oldmobile:oldmobile,
                    mobile:newMobile,
                    verifyCode:verify,
                };

                if(data.verifyCode ==  ''){
                    $.toast("请输入验证码");
                    return false;
                }
                $.post("{{ u('Staff/updateMobile') }}",data,function(res){
                    if(res.code == 0){
                        window.location.href = "{{ u('Staff/login') }}";
                    }else{
                        $.toast(res.msg);
                    }
                },"json");
            });
        });
    </script>
@stop
@section('show_nav')@stop