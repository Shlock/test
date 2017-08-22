@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <h1 class='title'>{{$title}}</h1>
    </header>
@stop

@section('show_refresh')@stop
@section('content')
    <div class="content p-0-085 login-register">
        <ul>
            <li class="w_b">
                <span class="icon iconfont">&#xe650;</span>
                <input type="text" id="cellphone"  placeholder="请输入手机号码" class="w_b_f_1"/>
            </li>
            <li class="w_b">
                <span class="icon iconfont">&#xe652;</span>
                <input type="password" id="password" placeholder="请输入密码" class="w_b_f_1"/>
            </li>
        </ul>
        <div class="clearfix">
            <a href="#" onclick="JumpURL('{{u('Staff/repwd',['code'=>3])}}','#staff_repwd_view',2)" class="f_r  focus-color-f  f075  lh095 mt090 ">忘记密码？</a>
        </div>
        <input class="ui-button_login_register mt090" type="submit"  id="login" value="登录">
    </div>
@stop

@section($js)
<script type="text/javascript">
    $(function(){
        $(document).on("click","#login",function(){
            var mobile = $("#cellphone").val();
            var pwd = $("#password").val();
            var reg = /^1\d{10}$/;
            if(!reg.test(mobile)){
                $.toast("请输入正确的手机号码");
                return false;
            }
            var data = {
                mobile:mobile,
                pwd:pwd
            };
            if(data.pwd == '' ){
                $.toast("请输入密码");
                return false;
            }
            $.showIndicator();
            $.post("{{ u('Staff/dologin') }}",data,function(res){
                if(res.code == 0){
                    var url = "{{u('Mine/index')}}";
                    JumpURL(url,'#mine_index_view',2);
                }else{
                    $.toast(res.msg);
                }
            $.hideIndicator();
            },"json");
        });
    });
</script>
@stop
@section('show_nav')@stop