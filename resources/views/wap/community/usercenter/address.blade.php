@extends('wap.community._layouts.base_order')

@section('css')
<style>
    a.x-sjr.ui-btn-right.ui-link.ui-btn.ui-corner-all {color: #000;}
</style>
@stop

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>我的{{ $title }}</h1>
       <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:history.back(-1); @endif"  data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <a href="{{ u('UserCenter/addressdetail',['cartIds'=>Input::get('cartIds'), 'plateId'=>Input::get('plateId'), 'postId'=>Input::get('postId')]) }}" class="x-sjr ui-btn-right addr_save" data-shadow="false">新增</a>
    </div>
@stop

@section('content')
<div>
    @if(!empty($list))
    <ul class="y-xzshdz y-dzgltb" id="address">
        @include('wap.community.usercenter.address_item')
    </ul>
    @else
        <div class="x-serno c-green">
            <img src="{{  asset('wap/community/client/images/ico/cry.png') }}"  />
            <span>很抱歉！你还没有添加地址！</span>
        </div>
    @endif
</div>
@include('wap.community._layouts.swiper')
@stop
@section('js')
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
    <script type="text/javascript">
        // 列表
        $(function() {
            $.SwiperInit('#address','.y-address',"{{ u('UserCenter/address',$args) }}");
            var isChange = "{{ (int)Input::get('change') }}";
            var cartIds = "{{ Input::get('cartIds') }}";
            var plateId = "{{ Input::get('plateId') }}";
            var postId = "{{ Input::get('postId') }}";
            $(".y-address").touchend(function(){
                var id = $(this).data('id');
                if(cartIds != ''){
                    var url = "{!! u('Order/order',['cartIds'=>$args['cartIds'],'proId'=>$args['proId'],'addressId' => ADDID]) !!}".replace("ADDID", id);
                    window.location.href = url;
                }else if(isChange == "1"){
                    $.setDefaultAdd(id);
                    window.location.href = "{!! u('Index/index') !!}";
                } else if (plateId > 0) {
                    var url = "{!! u('Forum/addbbs',['plateId'=>$args['plateId'], 'postId'=>$args['postId'],'addressId' => ADDID]) !!}".replace("ADDID", id);
                    window.location.href = url;
                }
            })


            $(".urlte").touchend(function(){
                if(cartIds == '' && plateId == 0) {
                    var url = "{{ u('UserCenter/addressdetail' ,array('id' => 'ids') )}}".replace("ids", $(this).parents(".y-address").data('id'));
                    window.location.href = url;
                }
            });
            // 删除地址
            $(".y-address .y-del").touchend(function(){
                var id = $(this).parents(".y-address").data('id');
                $.showOperation("是否确认删除","javascript:$.deladds(" + id + ");");
                
            });

            $.deladds = function(id){
                //关闭弹框                
                $(".operation").addClass("none");

                var obj = $(".y-address"+id);
                var cartIds = "{{ Input::get('cartIds') }}";
                if(cartIds == '' && plateId == 0) {
                    $.post("{{ u('UserCenter/deladdress') }}", {id: id}, function (res) {
                        if (res.code == 0) {
                            obj.slideUp('fast', function () {
                                obj.remove();
                                var rems = 0;
                                $(".y-address").each(function(i,v){
                                    i ++;
                                    rems += i;
                                });
                               if(rems == 0){
                                $("#address").html('<div class="x-serno c-green"><img src="{{  asset("wap/community/client/images/ico/cry.png") }}"  /><span>很抱歉！你还没有添加地址！</span></div>');                                     
                               }
                            });
                        }
                    }, "json");
                }
            };
            // 设置默认地址
            $(".y-address .x-setDuf").touchend(function(){
                var obj = $(this).parents(".y-address");
                var athis = $(this);
                var id = obj.data('id');
                if(cartIds == '' && plateId == 0){
                    $.setDefaultAdd(id);
                }

            });

            $.setDefaultAdd = function(id){
                var obj = $(".y-address"+id);
                var change = "{{ (int)Input::get('change') }}";
                $.post("{{ u('UserCenter/setdefault') }}",{id:id,change:change},function(res){
                    if(res.code == 0){
                        $(".y-address").removeClass("on");
                        obj.addClass("on");
                        $(".y-address").find("a").removeClass("x-okaddress").addClass("x-okaddress1");
                        obj.find(".x-setDuf").removeClass("x-okaddress1").addClass("x-okaddress");
                        obj.addClass("on").siblings().removeClass("on");
                        obj.find("span.y-mraddr").text("默认");
                        obj.siblings().find("span.y-mraddr").text("设为默认");
                    }
                },"json");
            }

            $("#set-here").touchend(function(){
                $.post("{{ u('Index/here') }}",function(){
                    window.location.href = "{{ u('Index/index') }}";
                })
            })
        });
    </script>
@stop