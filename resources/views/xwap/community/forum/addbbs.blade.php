@extends('xwap.community._layouts.base')

@section('css')
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading back" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">发帖</h1>
        <a class="button button-link button-nav pull-right open-popup toedit submitbbs" data-popup=".popup-about">@if($data) 保存 @else 发布 @endif</a>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <!-- 填写内容 -->
        <div class="card x-posting">
            <div class="card-header f14">
                <div class="fl">发布到：</div>
                <div>@if($data){{$data['plate']['name']}}@else{{$plate['name']}}@endif</div>
                <input type="hidden" id="plateId" value="@if($data){{$data['plate']['id']}}@else{{$plate['id']}}@endif">
            </div>
            <div class="card-header f14">
                <div class="fl"><span class="mr15">标</span><span>题：</span></div>
                <input type="text" placeholder="请填写标题" id="title" value="@if($data){{ $data['title'] }}@else{{$option['title']}}@endif">
            </div>
            <div class="card-content pr f14">
                <div class="fl"><span class="mr15">内</span><span>容：</span></div>
                <div class="postr ml0"><textarea placeholder="请填写内容" class="w100" id="content">@if($data){{$data['content']}}@else{{$option['content']}}@endif</textarea></div>
            </div>
        </div>
        <!-- 添加图片 -->
        <div class="card x-postdelst m0">
            <div class="card-content x-pjpic">
                <div class="card-content-inner oh">
                    <ul class="x-postpic clearfix">
                        @for($i = 1; $i <= 4; $i++)
                            <form>
                                <label for="image-form-{{$i}}">
                                <li  id="image-form-{{$i}}-li">
                                    <img src="@if($data['images'][$i-1]){{formatImage($data['images'][$i-1],71,71)}}@elseif($option['images'][$i-1]){{formatImage($option['images'][$i-1],71,71)}} @else{{asset('wap/community/client/images/addpic.png')}}@endif" id="img{{$i}}" class="upimage">
                                    <i class="delete tc delete @if(!$data['images'][$i-1] && !$option['images'][$i-1]) none @endif" data-index="{{$i}}"><i class="icon iconfont f20">&#xe605;</i></i>
                                </li>
                                </label>
                                <div style="display:none"><input type="text" name="images" id="upimage_{{$i}}"></div>
                                <input id="image-form-{{$i}}" type="file" onchange="image_form_{{$i}}_onchange(this)" accept="image/*" style="display:none" />
                                <script type="text/javascript">
                                    function image_form_{{$i}}_onchange(sender)
                                    {
                                        PhotoCutUpload(sender, 200, 200, "{{u('Resource/upload')}}", function (result)
                                        {
                                            if (result.status == true)
                                            {
                                                $("img", $(sender.form)).attr("src", result.data);
                                                $("#upimage_{{$i}}").val(result.data);
                                            }
                                        });
                                    }
                                </script>
                            </form>
                        @endfor
                        <!-- <li>
                            <img src="{{ asset('wap/community/newclient/images/addpic.png') }}">
                            <input type="file" class="file" />
                        </li> -->
                    </ul>
                   <!--  <div style="display:none">
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
                    </div> -->
                </div>
            </div>
        </div>
        <!-- 联系方式 -->
        <div class="content-block-title c-black">联系方式（非必填）</div>
        <!-- <div class="list-block media-list x-postmsg">
            <ul>
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-inner">
                            <div class="item-subtitle f14">
                                <i class="icon iconfont f18 add mr10">&#xe61d;</i><span class="c-red">添加联系方式</span>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
 -->
        @if(!$option['addressId'] && !$data) 
            <div class="list-block media-list x-postmsg addmsg">
                <ul>
                    <li>
                        <a href="#" class="item-link item-content">
                            <div class="item-inner">
                                <div class="item-subtitle f14">
                                    <i class="icon iconfont c-red f18 add mr10">&#xe61d;</i><span class="c-red">添加联系方式</span>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        @else
            <div class="list-block media-list x-postmsg delmsg">
                <ul>
                    <li>
                        <a href="#" class="item-link item-content">
                            <div class="item-inner f14">
                                <div class="item-title-row">
                                    <div class="item-title na">@if($data){{$data['address']['name']}}@else{{$address['name']}}@endif</div>
                                    <div class="item-after phone"> @if($data){{$data['address']['mobile']}}@else{{$address['mobile']}}@endif</div>
                                </div>
                                <div class="item-subtitle">
                                    <i class="icon iconfont c-red f18 delete">&#xe620;</i>
                                    <i class="icon iconfont c-gray fr">&#xe602;</i>
                                </div>
                                <div class="item-text ha f14">
                                    @if(empty($address)){{$data['address']['address']}}{{$data['address']['doorplate']}}@else{{$address['address']}}{{$address['doorplate']}}@endif
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
            <input type="hidden" id="addressId" value="@if(empty($address)){{$data['address']['id']}}@else{{$address['id']}}@endif">
        @endif
    </div>
@stop

@section($js)
    <!-- <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> -->
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
            $(document).on("touchend",".x-postmsg",function(){
                var data = getData();
                $.post("{{ u('Forum/savebbsData') }}",data,function(res){
                    $.router.load("{!! u('UserCenter/address',['plateId'=>$plate['id'], 'postId'=>(int)$args['postId']]) !!}", true);
                },"json");
            })
            $(document).on("touchend",".x-delico3",function(){
                $(".x-postmsg").unbind();
                var data = getData();
                data.addressId = '';
                $.post("{{ u('Forum/savebbsData') }}",data,function(res){
                    $.router.load("{!! u('Forum/addbbs',['plateId'=>$plate['id'],'postId'=>$args['postId']]) !!}", true);
                },"json");
            });
            
            //上传图片
            $(document).on("uploadsucc",".x-pjpic form",function(){
                $("#" + this.id + "-li .delete").removeClass('none');
            });
            //删除图片
            // $(document).on("touchend",".x-pjpic .delete",function(){
            // {
            //     $(this).parents("li").find("img").attr("src", "{{ asset('wap/community/newclient/images/addpic.png') }}");
            //     $(this).addClass("none");
            //     var index = $(this).data('index');
            //     $("#image-from-val-" + index).val("");
            // });

            $(document).off('touchend', '.submitbbs');
            $(document).on("touchend",".submitbbs",function(){
                    var data = getData();
                    $.post("{{ u('Forum/savebbs') }}",data,function(res){
                        if(res.code == 0){
                            var return_url = "{{ u('Forum/lists',['plateId'=>$plate['id']]) }}";
                            $.alert(res.msg, function(){
                                $.router.load(return_url, true);
                            });
                        }else{
                            $.alert(res.msg);
                        }
                    },"json");
            });

            // 删除图片
            $(document).on("touchend",".x-postpic .delete",function(){
                $(this).parents("li").remove();
                var index = $(this).data('index');
                $("#image-from-val-" + index).val("");
            });
            // 编辑用户信息
            $(document).on("touchend",".x-postmsg .delete",function(){
                $(".delmsg").addClass("none");
                $(".addmsg").removeClass("none");
            });
        });
    </script>
@stop