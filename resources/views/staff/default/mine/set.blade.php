@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('css')
@stop
@section('contentcss')hasbottom @stop
@section('content')
    <div class="item">
        <a href="#" onclick="JumpURL('{{u('Mine/feedback')}}','#mine_feedback_view',2)">
            <i class="iconfont left bj74c9c2">&#xe663;</i>
            <i class="iconfont right">&#xe64b;</i>
            <div class="con">
                意见反馈
            </div>
        </a>
        <a href="#" id="show_alert">
            <i class="iconfont left bj6ab2fe">&#xe664;</i>
            <i class="iconfont right">&#xe64b;</i>
            <div class="con">
                版本检测
                <span class="memo">当前版本V1.0</span>
            </div>
        </a>
        <a href="#" onclick="JumpURL('{{u('More/detailAll',['code'=>3])}}','#more_detailAll_view',2)">
            <i class="iconfont left bjffa70f">&#xe665;</i>
            <i class="iconfont right">&#xe64b;</i>
            <div class="con">
                关于我们
            </div>
        </a>
    </div>
    <div class="pd050">
        <a class="ui-button_login_out ui-button_login_out-logout" href="#">退出登录</a>
    </div>
@stop
@section($js)
    <script type="text/javascript">
        $(function(){
            $(document).on("click","#show_alert",function(){
                $.toast('暂未开放');
            })
        });
    </script>
@stop
