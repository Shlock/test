@extends('wap.community._layouts.base_order')

@section('js')

@stop
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>评价</h1>
        <a href="javascript:history.back(-1);" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div role="main" class="ui-content x-sorta">
        <div class="y-ddbh" style="border:0;">
            <span>订单编号：{{$order['sn']}}</span>
        </div>
        <ul class="y-wdddmain">
            <li style="margin:0;" onclick="window.location.href='{!! u("Order/detail",["id" => Input::get("orderId")]) !!}'">
                <div class="y-ddcontent" style="border:0">
                    <div class="y-imgcont">
                        @foreach($order['cartSellers'] as $vo)
                        <div><img src="{{ $vo['goodsImages'] }}"></div>
                        @endforeach
                    </div>
                    <i class="c-green">共<span class="c-red">{{$order['count']}}</span>件<b class="x-rightico"></b></i>
                </div>
            </li>
        </ul>
        <div class="x-pjstar clearfix">
            <span>评分</span>
            <i class="x-star"></i>
            <i class="x-star"></i>
            <i class="x-star"></i>
            <i class="x-star"></i>
            <i class="x-star"></i>
        </div>
        <div class="x-pjbot">
            <div class="x-pjtxt">
                <textarea placeholder="您的意见很重要！来点评一下吧…" id="content"></textarea>
            </div>
            <div class="x-pjpic clearfix">
                <ul>
                    <li id="image-form-1-li"><div><label for="image-form-file-1"><img src="{{asset('wap/community/client/images/addpic.png')}}"  id="img1"></label><a href="javascript:;" class="delete none" data-index="1"></a></div></li>
                    <li id="image-form-2-li"><div><label for="image-form-file-2"><img src="{{asset('wap/community/client/images/addpic.png')}}"  id="img2"></label><a href="javascript:;" class="delete none" data-index="2"></a></div></li>
                    <li id="image-form-3-li"><div><label for="image-form-file-3"><img src="{{asset('wap/community/client/images/addpic.png')}}"  id="img3"></label><a href="javascript:;" class="delete none" data-index="3"></a></div></li>
                    <li id="image-form-4-li"><div><label for="image-form-file-4"><img src="{{asset('wap/community/client/images/addpic.png')}}"  id="img4"></label><a href="javascript:;" class="delete none" data-index="4"></a></div></li>
                    <li id="image-form-5-li"><div><label for="image-form-file-5"><img src="{{asset('wap/community/client/images/addpic.png')}}"  id="img5"></label><a href="javascript:;" class="delete none" data-index="5"></a></div></li>
                    <li id="image-form-6-li"><div><label for="image-form-file-6"><img src="{{asset('wap/community/client/images/addpic.png')}}"  id="img6"></label><a href="javascript:;" class="delete none" data-index="6"></a></div></li>
                    <li id="image-form-7-li"><div><label for="image-form-file-7"><img src="{{asset('wap/community/client/images/addpic.png')}}"  id="img7"></label><a href="javascript:;" class="delete none" data-index="7"></a></div></li>
                    <li id="image-form-8-li"><div><label for="image-form-file-8"><img src="{{asset('wap/community/client/images/addpic.png')}}"  id="img8"></label><a href="javascript:;" class="delete none" data-index="8"></a></div></li>
                </ul>
                <div style="display:none">
                    @yizan_begin
                        <yz:imageFrom name="images" toimg="img1" iscropper="1"></yz:imageFrom>
                        <yz:imageFrom name="images" toimg="img2" iscropper="1"></yz:imageFrom>
                        <yz:imageFrom name="images" toimg="img3" iscropper="1"></yz:imageFrom>
                        <yz:imageFrom name="images" toimg="img4" iscropper="1"></yz:imageFrom>
                        <yz:imageFrom name="images" toimg="img5" iscropper="1"></yz:imageFrom>
                        <yz:imageFrom name="images" toimg="img6" iscropper="1"></yz:imageFrom>
                        <yz:imageFrom name="images" toimg="img7" iscropper="1"></yz:imageFrom>
                        <yz:imageFrom name="images" toimg="img8" iscropper="1"></yz:imageFrom>
                    @yizan_end
                </div>
            </div>
            <div class="x-pjna on">匿名评价<i class="x-nmico"></i></div>
        </div>
    </div>
    <!-- content end -->
    <div data-role="footer" data-position="fixed" data-tap-toggle="false" class="x-footer">
        <div class="x-choicefw c-bg" id="submit">提交</div>
    </div>
    </div>
    <script type="text/javascript">
        $(function() {
            var star = 0;
            var isAno = 1;
            var orderId = "{{ Input::get('orderId') }}";

            //评价星级选择
            $(document).on("touchend",".x-pjstar i",function(){
                var arri = $(".x-pjstar i");
                var index = $(".x-pjstar i").index(this);
                star = parseInt(index)+1;
                $(".x-pjstar i").addClass("on");
                $(".x-pjstar i:gt(" + index + ")").removeClass("on");
            }).on("uploadsucc",".x-pjpic form",function(){
                        $("#" + this.id + "-li .delete").removeClass('none');
                    });
            $(".x-pjna").touchend(function(){
                if($(this).hasClass("on")){
                    isAno = 0;
                    $(this).removeClass("on");
                }else{
                    isAno = 1;
                    $(this).addClass("on");
                }
            });
            //评价页面照片删除
            $(".x-pjpic .delete").touchend(function ()
            {
                $(this).parents("li").find("img").attr("src", "{{asset('wap/community/client/images/addpic.png')}}");
                $(this).addClass("none");
                $("#image-from-val-" + index).val("");
            });

            $("#submit").touchend(function(){
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
                        $.showSuccess(res.msg);
                        window.location.href = "{{ u('Order/detail',['id'=>Input::get('orderId')]) }}";
                    }else if(res.code == '99996'){
                        window.location.href = "{{ u('User/login') }}";
                    }else{
                        $.showError(res.msg);
                    }
                },"json");
            })

        })
    </script>
@stop