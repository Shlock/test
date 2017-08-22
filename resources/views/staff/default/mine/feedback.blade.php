@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a href="#" class="button button-link button-nav f_r" onclick="$.save()">
            保存
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('content')
    <!-- 下面是正文 -->
    <div class="edit-feedback">
        <textarea class="feedbacktxt bdr5" id="content" placeholder="请输入您的宝贵意见和建议,我们会那努力为您做到更好···"></textarea>
    </div>
    <!-- 正文结束 -->
@stop
@section($js)
<script type="text/javascript">
$(function() {
        $.save = function(){
            var content = $("#content").val();
            $.post("{{ u('Mine/addfeedback') }}",{content:content},function(res){
                if(res.code == 0) {
                    $.toast(res.msg);
                    JumpURL("{{ u('Mine/index') }}",'#mine_index_view',2);
                }else{
                    $.toast(res.msg);
                }
            },"json");
        }
    });
</script>
@stop