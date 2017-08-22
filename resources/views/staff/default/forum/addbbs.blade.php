@extends('wap.community._layouts.base')

@section('css')
<style>
    a.x-sjr.ui-btn-right.ui-link.ui-btn.ui-corner-all {color: #000;}
</style>
@stop

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>发帖</h1>
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <a class="x-sjr ui-btn-right submitbbs">@if($data) 保存 @else 发布 @endif</a>
    </div>
@stop

@section('content')
    <div role="main" class="ui-content">
        <ul class="x-postedit">
            <li class="x-posted1">
                <div class="fl">发布到：</div>
                <div class="x-postr">@if($data){{$data['plate']['name']}}@else{{$plate['name']}}@endif</div>
                <input type="hidden" id="plateId" value="@if($data){{$data['plate']['id']}}@else{{$plate['id']}}@endif">
            </li>
            <li class="x-posted2">
                <div class="fl"><span class="mr15">标</span>题：</div>
                <div class="x-postr"><input type="text" placeholder="请填写标题" id="title" value="@if($data){{ $data['title'] }}@else{{$option['title']}}@endif"/></div>
            </li>
            <li class="x-posted3">
                <div class="fl"><span class="mr15">内</span>容：</div>
                <div class="x-postr"><textarea placeholder="请填写内容" id="content">@if($data){{$data['content']}}@else{{$option['content']}}@endif</textarea></div>
            </li>
        </ul>
        <div class="x-pjpic x-postpic clearfix">
            <ul>
                @for($i = 1; $i <= 4; $i++)
                <li id="image-form-{{$i}}-li">
                    <div>
                        <label for="image-form-file-{{$i}}">
                            <img src="@if($data['images'][$i-1]){{formatImage($data['images'][$i-1],71,71)}}@elseif($option['images'][$i-1]){{formatImage($option['images'][$i-1],71,71)}} @else{{asset('wap/community/client/images/addpic.png')}}@endif"  id="img{{$i}}">
                        </label><a href="javascript:;" class="delete @if(!$data['images'][$i-1] && !$option['images'][$i-1]) none @endif" data-index="{{$i}}"></a></div>
                </li>
                @endfor
            </ul>
            <div style="display:none">
                @yizan_begin
                <php>
                    $images_data = array();
                    for($j = 1; $j <= 4; $j++) {
                        if($data['images'][$j-1]) {
                            array_push($images_data, $data['images'][$j-1]);
                        }
                        if($option[images][$j-1]) {
                            array_push($images_data, $option['images'][$j-1]);
                        }
                    }
                    $images_data = array_unique($images_data);
                </php>
                    <yz:imageFrom name="images" iscropper="1" toimg="img1" image="$images_data[0]"></yz:imageFrom>
                    <yz:imageFrom name="images" iscropper="1" toimg="img2" image="$images_data[1]"></yz:imageFrom>
                    <yz:imageFrom name="images" iscropper="1" toimg="img3" image="$images_data[2]"></yz:imageFrom>
                    <yz:imageFrom name="images" iscropper="1" toimg="img4" image="$images_data[3]"></yz:imageFrom>
                @yizan_end
            </div>
        </div>
        <div class="x-lh45">联系方式（非必填）</div>
        <div class="x-postmsg">
            @if(!$option['addressId'] && !$data)
            <p class="c-red"><i class="x-addico2"></i>添加联系方式</p>
            @else
            <p class="m30"><span class="na">@if($data){{$data['address']['name']}}@else{{$address['name']}}@endif</span><span class="phone">@if($data){{$data['address']['mobile']}}@else{{$address['mobile']}}@endif</span></p>
            <p class="clearfix"><i class="x-delico3"></i><i class="x-rightico fr"></i></p>
            <p class="m30">@if($data){{$data['address']['address']}}{{$data['address']['doorplate']}}@else{{$address['address']}}{{$address['doorplate']}}@endif</p>
            <input type="hidden" id="addressId" value="@if($data){{$data['address']['id']}}@else{{$address['id']}}@endif">
            @endif
        </div>
    </div>
@stop
@section('js')
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
    <script type="text/javascript">
        // 列表
        function getData(){
            var obj = new Object();
            images = new Array();
            $("input[name=images]").each(function(index,val){
                if($(this).val() != "" ){
                    images.push($(this).val());
                }
            })
            obj.images = images;
            obj.plateId = "{{ $plate['id'] }}";
            obj.postId = "{{ $args['postId'] }}";
            obj.addressId = $("#addressId").val();
            obj.title = $("#title").val();
            obj.content = $("#content").val();
            
            return obj;
        }

        $(function() {
            $(".x-postmsg").click(function() {
                var data = getData();
                $.post("{{ u('Forum/savebbsData') }}",data,function(res){
                    window.location.href="{!! u('UserCenter/address',['plateId'=>$plate['id'], 'postId'=>(int)$args['postId']]) !!}";
                },"json");
            })
            $(".x-delico3").touchend(function ()
            {
                $(".x-postmsg").unbind();
                var data = getData();
                data.addressId = '';
                $.post("{{ u('Forum/savebbsData') }}",data,function(res){
                    window.location.href="{!! u('Forum/addbbs',['plateId'=>$plate['id'],'postId'=>$args['postId']]) !!}";
                },"json");
            });
            
            //上传图片
            $(document).on("uploadsucc",".x-pjpic form",function(){
                $("#" + this.id + "-li .delete").removeClass('none');
            });
            //删除图片
            $(".x-pjpic .delete").touchend(function ()
            {
                $(this).parents("li").find("img").attr("src", "{{asset('wap/community/client/images/addpic.png')}}");
                $(this).addClass("none");
                var index = $(this).data('index');
                $("#image-from-val-" + index).val("");
            });

            $(".submitbbs").touchend(function ()
            {      
                var data = getData();
               // console.log(data);
                $.post("{{ u('Forum/savebbsData') }}",data,function(res){
                    $.post("{{ u('Forum/savebbs') }}",data,function(res){
                        if(res.code == 0){
                            var return_url = "{{ u('Forum/lists',['plateId'=>$plate['id']]) }}";
                            $.showSuccess(res.msg,return_url);
                        }else{
                            $.showError(res.msg);
                        }
                    },"json");
                });
            });

        });
    </script>
@stop