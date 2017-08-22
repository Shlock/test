@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>修改密码</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')


        <div data-role="content" style="padding-top:0;">
            <form class="d-box">
                <div class="d-input">
                    <input type="password" name="oldpwd" id="oldpwd" class="tel" value="" placeholder="请输入原密码">
                </div>
                <div class="d-input">
                    <input type="password" name="newpwd" id="newpwd" class="tel" value="" placeholder="请输入新密码">
                </div>
                <div class="d-input">
                    <input type="password" name="pwd" id="pwd" class="tel" value="" placeholder="请再次输入新密码">
                </div>
                <div class="d-input"><p>密码长度在6-20位，建议数字、字母、符号组合</p></div>

                <div class="y-buttoncol">
                    <a href="javascript:;" data-role="button" id="submit">确定</a>
                </div>
            </form>
        </div>
                <!-- content end -->
        <script type="text/javascript">
            $("#submit").on("touchend",function(){
                var oldpwd = $("#oldpwd").val();
                var newpwd = $("#newpwd").val();
                var pwd = $("#pwd").val();
                if(oldpwd == ""){
                    $.showError("请输入原密码");
                    return false;
                }
                if(oldpwd == pwd || oldpwd == newpwd){
                    $.showError("新密码与原密码相同,请重新输入");
                    return false;
                }
                if(newpwd.length > 20 || newpwd.length < 6){
                    $.showError("新密码长度错误,请重新输入");
                    return false;
                }
                if(newpwd != pwd){
                    $.showError("新密码两次输入不一致,请重新输入");
                    return false;
                }
                $.post("{{ u('UserCenter/dorepwd') }}",{oldpwd:oldpwd,pwd:pwd},function(res){
                    if(res.code > 0){
                        $.showError(res.msg);
                        return false;
                    }else{
                        $.showSuccess(res.msg,"{{ u('UserCenter/info') }}");
                    }
                },"json");
            })
        </script>
@stop
