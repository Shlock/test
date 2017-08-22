@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>举报帖子</h1>
        <a href="{{ u('Forum/detail',['id'=>$id]) }}" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div role="main" class="ui-content">
        <div class="y-yjfk">
            <div class="y-yjtxt f14">
                <textarea name="content" id="content" placeholder="请输入举报理由，我们会尽快核实…"></textarea>
            </div>
            <button id="submit">提交</button>
        </div>
    </div>
@stop
@section('js')
    <script type="text/javascript">
        $(function() {
            $(document).on("touchend","#submit",function(){
                var content = $("#content").val();
                var id = "{{$id}}";
                $.post("{{ u('Forum/addcomplain') }}",{'content':content,'id': id},function(res){
                    if(res.code == 0) {
                        $.showSuccess(res.msg,"{!! u('Forum/detail',['id'=>$id]) !!}");
                    }else{
                        $.showError(res.msg);
                    }
                },"json");
            })
        })
    </script>
@stop

