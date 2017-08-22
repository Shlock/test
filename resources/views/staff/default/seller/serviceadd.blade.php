@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left show_external external" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <span class="button button-link button-nav f_r" onclick="$.goodssave()">
            完成
        </span>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('distance')id="service-add" @stop
@section('preview')
    <a href="#" class="preview_but"><i class="icon iconfont right-ico">&#xe680;</i>&nbsp;预览</a>
@stop
@section('content')

@stop
@section($js)
    <script type="text/javascript">
        $(function(){
            $(".add_goods_specifications").on('click',function(){
                if($(".add-block").length == 0)
                {
                    $(".add-b").addClass("add-block");
                    $(".add_goods_specifications").css({"margin-top":"0.75rem"});
                }
        })

        $(".delete-but").on("click",function(){

            if($(".add-block").length>1)
            {
                $(this).parent(".add-block").remove();

            }
            else
            {
                $(this).parent(".add-block").removeClass("add-block");
                $(".add_goods_specifications").css({"margin-top":"-0.75rem"});
            }

        });

            //图片上传预览    IE是用了滤镜。
            $.previewImage = function (file)
            {
                var MAXWIDTH  = 260;
                var MAXHEIGHT = 180;
                var div = document.getElementById('preview');
                var upload_but=document.getElementById('upload_but');
                if (file.files && file.files[0])
                {
                    div.innerHTML ='<img id=imghead><div class=upload_again>点击上传商品图片</div>';
                    var img = document.getElementById('imghead');
                    img.onload = function(){
                        var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
                        // img.width  =  rect.width;
                        img.style.width = "100%";
                        //img.height =  img.width;

                        div.style.height=img.width+'px';
                        upload_but.style.width=img.width+'px';
                        upload_but.style.height=img.width+'px';
//                 img.style.marginLeft = rect.left+'px';
                        //  img.style.marginTop = rect.top+'px';
                    }
                    var reader = new FileReader();
                    reader.onload = function(evt){img.src = evt.target.result;}
                    reader.readAsDataURL(file.files[0]);

                }
                else //兼容IE
                {
                    var sFilter='filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="';
                    file.select();
                    var src = document.selection.createRange().text;
                    div.innerHTML = '<img id=imghead>';
                    var img = document.getElementById('imghead');
                    img.filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = src;
                    var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
                    status =('rect:'+rect.top+','+rect.left+','+rect.width+','+rect.height);
                    div.innerHTML = "<div id=divhead style='width:"+rect.width+"px;height:"+rect.height+"px;margin-top:"+rect.top+"px;"+sFilter+src+"\"'></div>";
                }
            }
            function clacImgZoomParam( maxWidth, maxHeight, width, height ){
                var param = {top:0, left:0, width:width, height:height};
                if( width>maxWidth || height>maxHeight )
                {
                    rateWidth = width / maxWidth;
                    rateHeight = height / maxHeight;

                    if( rateWidth > rateHeight )
                    {
                        param.width =  maxWidth;
                        param.height = Math.round(height / rateWidth);
                    }else
                    {
                        param.width = Math.round(width / rateHeight);
                        param.height = maxHeight;
                    }
                }

                param.left = Math.round((maxWidth - param.width) / 2);
                param.top = Math.round((maxHeight - param.height) / 2);
                return param;
            }
            $.goodssave = function(){
                var name   = $("#name").val();
                var price  = $("#price").val();
                var stock  = $("#stock").val();
                var imgs    = $("#imghead").attr('src');
                var brief    = $("#brief").val();
                var id    = "{{$data['id']}}";
                var data = {
                    'id':id ,
                    'name':name ,
                    'price':price,
                    'stock':stock,
                    'imgs':imgs ,
                    'brief':brief
                }
                $.showIndicator();
                $.post("{{ u('Seller/goodsSave') }}",data,function(res){

                    $.hideIndicator();
                },"json");
            }
     });
</script>
@stop

@section('show_nav')@stop
@section('preloader')@stop
