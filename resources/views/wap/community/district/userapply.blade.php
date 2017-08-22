@extends('wap.community._layouts.base')
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>小区身份认证</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div role="main">
        <ul class="y-orderlst f14 y-authentication">
            <li class="clearfix">
                <span class="fl">小区名称：</span>
                <span class="y-xxxx">{{$data['name']}}</span>
            </li>
            <li class="clearfix">
                <span class="fl">楼宇：</span>
                <span class="y-xxxx">
                    <div class="y-select" id="building">请选择</div>
                    <div class="y-option none" id="buildingid">
                        @foreach($list as $v)
                        <p data-id="{{$v['id']}}">{{$v['name']}}</p>
                        @endforeach
                    </div>
                </span>
            </li>
            <li class="clearfix">
                <span class="fl">房间：</span>
                <span class="y-xxxx">
                    <div class="y-select" id="room">请选择</div>
                    <div class="y-option none" id="roomid"></div>
                </span>
            </li>
            <li class="clearfix">
                <span class="fl">业主姓名：</span>
                <span class="y-xxxx"><input type="text" placeholder="请输入业主名" name="username"></span>
            </li>
            <li class="clearfix">
                <span class="fl">联系电话：</span>
                <span class="y-xxxx" id="usertel">{{$usertel}}</span>
            </li>
        </ul>
        <div class="y-submit">
            <input type="hidden" name="villagesid" value="{{$data['id']}}">
            <a href="javascript:;" class="ui-btn" id="submit">提交</a>
            <p class="f12">提交身份认证后，即可开通物业版块</p>
        </div>
    </div>
@stop
@section('js')
<script type="text/javascript">
var buildId = 0;
$(function() {
    $(".y-select").touchend(function(){
        $(".y-option").addClass("none");
        if($(this).siblings(".y-option").hasClass("none")){
            $(this).siblings(".y-option").removeClass("none");
        }else{
            $(this).siblings(".y-option").addClass("none");
        }
        return false;
    });
    $(".y-option p").touchend(function(){
        $(this).parent().siblings(".y-select").text($(this).text());
        $(this).parent().addClass("none");
        $(this).parent().siblings(".y-select").attr('data-id', $(this).data('id')); 
        var buildingid = $(this).data('id');
        var u_id = new Array(); 
        $.post("{{u('District/searchrooms')}}",{'buildingid':buildingid},function(result){
            var html = '';
            var data = result.data;
            $.each(data, function(index,e){
                if (u_id.indexOf(data[index].id) == -1){
                    html += "<p data-id='" + e.id + "'>" + e.roomNum + "</p>";                     
                }
            });
            //console.log(html);
            $('#roomid').html(html);
            $(".y-option p").touchend(function(){
                $(this).parent().siblings(".y-select").text($(this).text());
                $(this).parent().addClass("none");
                $(this).parent().siblings(".y-select").attr('data-id', $(this).data('id')); 
            });
        },'json');

        //记录当前新的楼宇编号
        if(buildId != buildingid){
            buildId = buildingid;  //更新楼宇编号
            $("div#room").removeAttr("data-id").text("请选择"); //清除房间编号
        }
        return false;
    });
    
    $("#submit").click(function() {
        buildingid = $("#building").data('id');
        roomid = $("#room").data('id');
        username = $("input[name=username]").val();
        usertel = $.trim($("#usertel").text());
        villagesid = $("input[name=villagesid]").val();
        var data = {
            buildingid : buildingid,
            roomid : roomid,
            username : username,
            usertel : usertel,
            villagesid : villagesid
        }; 
        //console.log(data);
        $.post("{{u('District/villagesauth')}}", data, function(result){
            if(result.code == 0){
                window.location.href="{!! u('Property/index') !!}?districtId=" + villagesid;
            } else {
                $.showError(result.msg);
            }
        },'json');
    })


    // $(document).bind("click",function(e){
    //     var target = $(e.target);
    //     if(target.closest(".y-option").length == 0){
    //         $(".y-option").addClass("none");
    //         return false;
    //     }
    // })
})
    
</script>

@stop