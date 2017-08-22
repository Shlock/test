@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>修改昵称</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')


        <div data-role="content" style="padding-top:0;">
            <form class="d-box">
                <div class="d-input">
                    <input type="text" name="nick" id="nick" class="tel" value="{{ $user['name'] }}" style="background: url({{ asset('wap/community/client/images/ico/ddxq-img.png') }}) 10px no-repeat;background-size:12px 16px;">
                </div>
                <div class="y-buttoncol">
                    <a href="javascript:;" data-role="button" id="submit">确定</a>
                </div>
            </form>
        </div>
                <!-- content end -->
        <script type="text/javascript">
            $("#submit").on("touchend",function(){
                var nick = $("#nick").val();
                $.post("{{ u('UserCenter/updateinfo') }}",{name:nick},function(res){
                        if(res.code == 0){
                            $.showSuccess(res.msg,"{{ u('UserCenter/info') }}");
                        }else{
                            $.showError(res.msg);
                        }
                },"json");
            })
        </script>
@stop
