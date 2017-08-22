@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>意见反馈</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div role="main" class="ui-content">
        <div class="y-yjfk">
            <div class="y-yjtxt f14">
                <textarea name="content" id="content" placeholder="请输入您的宝贵意见，我们会更加完善的…"></textarea>
            </div>
            <button id="submit">提交意见</button>
        </div>
    </div>
@stop
@section('js')
    <script type="text/javascript">
        $(function() {
            $(document).on("touchend","#submit",function(){
                var content = $("#content").val();
                $.post("{{ u('UserCenter/addfeedback') }}",{content:content},function(res){
                    if(res.code == 0) {
                        $.showSuccess(res.msg,"{{ u('UserCenter/index') }}");
                    }else{
                        $.showError(res.msg);
                    }
                },"json");
            })
        })
    </script>
@stop

