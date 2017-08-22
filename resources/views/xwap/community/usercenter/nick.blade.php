@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">修改昵称</h1>
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
                            <div class="item-title label f14">会员名称</div>
                            <div class="item-input">
                                <input type="text" name="nick" id="nick" class="tel" value="{{ $user['name'] }}">
                                <i class="icon iconfont c-gray2 f18">&#xe63e;</i>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <p class="y-bgnone mb10"><a class="y-paybtn f16" id="submit">保存</a></p>
    </div>
        
@stop

@section($js)
<script type="text/javascript">
    $("#submit").on("touchend",function(){
        var nick = $("#nick").val();
        $.post("{{ u('UserCenter/updateinfo') }}",{name:nick},function(res){
                if(res.code == 0){
                    $.alert(res.msg, function(){
                        $.router.load("{{ u('UserCenter/info') }}", true);
                    });
                }else{
                    $.alert(res.msg);
                }
        },"json");
    })
</script>
@stop

