@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">修改密码</h1>
    </header>
@stop

@section('content')
        <div class="content" id=''>
            <div class="list-block">
                <ul class="y-wdzh y-sz">
                    <li></li>
                    <li>
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label f14">原 密 码</div>
                                <div class="item-input">
                                    <input type="password" name="oldpwd" id="oldpwd" class="tel" value="" placeholder="请输入原密码">
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label f14">新 密 码</div>
                                <div class="item-input">
                                    <input type="password" name="newpwd" id="newpwd" class="tel" value="" placeholder="请输入新密码">
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label f14">重复密码</div>
                                <div class="item-input">
                                    <input type="password" name="pwd" id="pwd" class="tel" value="" placeholder="请再次输入新密码">
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="d-input c-gray"><p>密码长度在6-20位，建议数字、字母、符号组合</p></div>
            <p class="y-bgnone mb10"><a class="y-paybtn f16" id="submit">确定</a></p>
        </div>
        
@stop

@section($js)
<script type="text/javascript">
    $("#submit").on("touchend",function(){
        var oldpwd = $("#oldpwd").val();
        var newpwd = $("#newpwd").val();
        var pwd = $("#pwd").val();
        if(oldpwd == ""){
            $.alert("请输入原密码");
            return false;
        }
        if(oldpwd == pwd || oldpwd == newpwd){
            $.alert("新密码与原密码相同<br/>请重新输入");
            return false;
        }
        if(newpwd.length > 20 || newpwd.length < 6){
            $.alert("新密码长度错误<br/>请重新输入");
            return false;
        }
        if(newpwd != pwd){
            $.alert("新密码两次输入不一致<br/>请重新输入");
            return false;
        }
        $.post("{{ u('UserCenter/dorepwd') }}",{oldpwd:oldpwd,pwd:pwd},function(res){
            if(res.code > 0){
                $.alert(res.msg);
                return false;
            }else{
                $.alert(res.msg, function(){
                    $.router.load("{{ u('UserCenter/info') }}", true);
                });
            }
        },"json");
    })
</script>
@stop
