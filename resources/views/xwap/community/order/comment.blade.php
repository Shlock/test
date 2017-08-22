@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav y-barnav">
        <a class="button button-link button-nav pull-left" href="{{$nav_back_url}}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">评价</h1>
    </header>
@stop

@section('content')
    <nav class="bar bar-tab">
        <p class="buttons-row x-bombtn">
            <a class="button" id="submit">提交</a>
        </p>
    </nav>
    <div class="content">
        <div class="c-bgfff pt15 pb15">
            <div class="y-ddbh f12">订单编号：{{$order['sn']}}</div>
        </div>
        <div class="list-block media-list y-qrdd">
            <ul>
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">
                            @foreach($order['cartSellers'] as $vo)
                                <span><img src="{{ $vo['goodsImages'] }}"></span>
                            @endforeach
                        </div>
                        <div class="item-after c-gray f12 mt10" onclick="$.href('{!! u('Order/detail',['id' => Input::get('orderId')]) !!}')">共<span class="c-red">{{$order['count']}}</span>件<i class="icon iconfont">&#xe602;</i></div>
                      </div>
                    </div>
                  </a>
                </li>
            </ul>
        </div>
        <div class="content-block-title x-pjstar">
            <span class="f14 c-black mr5">评分</span>
            <div class="y-starcont c-red">
                <div class="y-star">
                    <i class="icon iconfont vat mr10 f18">&#xe653;</i>
                    <i class="icon iconfont vat mr10 f18">&#xe653;</i>
                    <i class="icon iconfont vat mr10 f18">&#xe653;</i>
                    <i class="icon iconfont vat mr10 f18">&#xe653;</i>
                    <i class="icon iconfont vat mr10 f18">&#xe653;</i>
                </div>
                <div class="y-startwo">
                    <i class="icon iconfont vat mr10 f18">&#xe654;</i>
                    <i class="icon iconfont vat mr10 f18">&#xe654;</i>
                    <i class="icon iconfont vat mr10 f18">&#xe654;</i>
                    <i class="icon iconfont vat mr10 f18">&#xe654;</i>
                    <i class="icon iconfont vat mr10 f18">&#xe654;</i>
                </div>
            </div>
        </div>
        <div class="c-bgfff p10">
            <textarea class="x-pjtxt f14" placeholder="您的意见很重要！来点评一下吧..." id="content"></textarea>
        </div>
        <div class="card x-postdelst x-pjpic m0">
            <div class="card-content">
                <div class="card-content-inner oh">
                    <ul class="x-postpic clearfix">
                        @for($i=1; $i<=8; $i++)
                            <form>
                                <label for="image-form-{{$i}}">
                                    <li id="li_{{$i}}">
                                        <img src="{{ asset('wap/community/newclient/images/addpic.png') }}">
                                        <i class="delete tc none" data-id="{{$i}}"><i class="icon iconfont f20">&#xe605;</i></i>
                                    </li>
                                </label>

                                <input type="text" name="images" id="upimage_{{$i}}" style="display:none">
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
                                                $("#li_{{$i}} .delete").removeClass("none");
                                            }
                                        });
                                    }
                                </script>
                            </form>
                        @endfor
                    </ul>
                </div>
            </div>
        </div>
        <div class="c-bgfff tr x-pjna pr10">
            <span class="mr5">匿名评价</span>
            <i class="icon iconfont c-red f17 on">&#xe612;</i>
        </div>
    </div>
@stop

@section($js)
<script type="text/javascript">
    $(function() {
        var star = 0;
        var isAno = 1;
        var orderId = "{{ Input::get('orderId') }}";

        //评价星级选择
        $(document).on("uploadsucc",".x-pjpic form",function(){
            $("#" + this.id + "-li .delete").removeClass('none');
        });

        $(document).on("touchend",".x-pjna i",function(){
            if($(this).hasClass("on")){
                isAno = 0;
                $(this).removeClass("on");
            }else{
                isAno = 1;
                $(this).addClass("on");
            }
        });
        //评价页面照片删除
        $(document).on("touchend",".x-pjpic .delete",function(){
            $(this).parents("li").find("img").attr("src", "{{asset('wap/community/client/images/addpic.png')}}");
            $(this).addClass("none");
            // $("#image-from-val-" + index).val("");
            $("#upimage_" + $(this).data('id')).val("");
            return false;
        });
        
        $(document).on("touchend","#submit",function(){
            var images = new Array();
            $("input[name=images]").each(function(index,val){
                if($(this).val() != "" ){
                    images.push($(this).val());
                }
            })
            var content = $("#content").val();
            var data = {
                orderId: orderId,
                content: content,
                images: images,
                isAno: isAno,
                star: star
            };

            $.post("{{ u('Order/docomment') }}", data, function(res){
                if(res.code == 0) {
                    $.alert(res.msg, function(){
                        $.router.load("{!! u('Order/detail',['id'=>Input::get('orderId'),'pid'=>$tid]) !!}", true);
                    });
                }else if(res.code == '99996'){
                    $.router.load("{{ u('User/login') }}", true);
                }else{
                    $.alert(res.msg);
                }
            },"json");
            
            return false;
        })

        // 评价
        $(document).on("touchend",".x-pjstar .y-star i, .x-pjstar .y-startwo i",function(){
            var arri = $(this).parent().children();
            var index = $(this).parent().children().index(this);
            var redstar_w = (index+1) / 5 * 100;
            star = parseInt(index)+1;
            $(".x-pjstar .y-star i").removeClass("on");
            $(".x-pjstar .y-startwo").css("width", "0");
            $(".x-pjstar .y-startwo").css("width", redstar_w + "%");
            for (i = 0; i < arri.length; i++){
                arri[i].className = i < index+1 ? "icon iconfont vat mr10 f18 on" : "icon iconfont vat mr10 f18";
            }
        });

    })
</script>
@stop