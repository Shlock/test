@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading back" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">意见反馈</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <div class="y-yjtxt f14">
            <textarea id="content" placeholder="请输入您的宝贵意见，我们会更加完善的…"></textarea>
        </div>
        <p class="y-bgnone"><a href="javascript:addfeedback()" class="y-paybtn f16" id="submit">提交意见</a></p>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        function addfeedback() {
            var content = $("#content").val();
            $.post("{{ u('UserCenter/addfeedback') }}",{content:content},function(res){
                if(res.code == 0) {
                    $.alert(res.msg, function(){
                        $.router.load("{{ u('UserCenter/index') }}", true);
                    });
                }else{
                    $.alert(res.msg);
                }
            },"json");
        }
    </script>
@stop

