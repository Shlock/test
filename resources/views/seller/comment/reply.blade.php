@extends('seller._layouts.base')
@section('css')
    <style type="text/css">
        .star-rank{
            width: 85px;
            height: 30px;
            background: url("{{ asset('wap/community/client/images/ico/star.png') }}") left center repeat-x;
            background-size: 17px 12px;
        }
        .star-score{
            height: 30px;
            background: url("{{ asset('wap/community/client/images/ico/star1.png') }}") left center repeat-x;
            background-size: 17px 12px;
        }
    </style>
@stop
@section('content')
    @yizan_begin
        <div class="m-ydtt" style="margin-top:0px;">
            <div class="x-bbmain">
                <div class="x-bbtt">
                    评价回复
                    <a href="{{ u('Comment/index') }}" class="f-bluebtn btn x-pjfh">返回</a>
                </div>
                <div class="x-pjt" style="height:100px;">
                    <a href="javascript:;" class="x-pjimg" style="cursor:default;height:100px;">
                        <img src="{{ $data['user']['avatar'] or asset('images/default_headimg.jpg')}}"  style="max-width:80px;max-height:100px;" />
                    </a>
                    <div class="x-pjr">
                        <p class="x-pj">订单编号：{{ $data['order']['sn'] }}</p>
                        <p class="x-pjname">用户名称：{{ $data['user']['name'] }}</p>
                        <div class="x-pj ">
                            <div class="star-rank fl">
                                <div class="star-score" style="width:{{$data['star'] * 20}}%;"></div>
                            </div>
                        </div>
                        <p>
                            <span class="fl">{{ $data['content'] }}</span>
                            <span class="fr">{{ yztime($data['createTime']) }}</span>
                        </p> 
                    </div>
                </div>
                <div class="f-boxr">
                        @foreach($data['images'] as $img)
                            <a class="image-item" href="{{ $img }}" target="_blank" >
                                <img src="{{ $img }}" style="max-width:200px;max-height:200px;" />
                            </a> 
                        @endforeach
                </div>
                <div class="x-pjtxt">
                     <textarea name="content" id="Content" placeholder="评价回复"></textarea>
                </div>
                <p>
                    已回复内容：{{ $data['reply'] }}
                </p> 
                <p class="mt20 tc">
                    <a class="btn btnreply f-tj f-30btn mt5 mr15" href="javascript:;">提交</a>
                </p>
            </div>
        </div>
    @yizan_end
@stop

@section('js')
<script type="text/javascript">

    var obj = new Object(); 
    obj.id      =  "{{ $id['id'] }}";
    obj.mobile      =  "";
    obj.content      =  "";

    $(".btnreply").click(function(){ 
        obj.content =  $("#Content").val(); 
        if( obj.content == ""){
            $.ShowAlert("内容不能为空");
        }else{
            $.post("{{ u('Comment/ajaxreply') }}",obj,function(result){
                $.ShowAlert(result.msg);
                window.location="{{ u('Comment/reply',array('id'=>$id['id'])) }}";
            },'json');      
        }
    });
</script>
@include('seller._layouts.alert')
@stop
