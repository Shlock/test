@extends('wap.community._layouts.base')

@section('css')
<style>
    a.x-sjr.ui-btn-right.ui-link.ui-btn.ui-corner-all {color: #000;}
</style>
@stop

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>我的{{ $title }}</h1>
       <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:$.back(); @endif"  data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <a href="{{ u('Forum/addressdetail',['plateId'=>Input::get('plateId'), 'postId'=>Input::get('postId')]) }}" class="x-sjr ui-btn-right addr_save" data-shadow="false">新增</a>
    </div>
@stop

@section('content')
<div role="main" class="ui-content" id="address">  
    @if(!empty($list))
        <ul class="y-xzshdz y-dzgltb">
            @include('wap.community.forum.address_item')
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

            $.SwiperInit('#address','.yz-address',"{!! u('Forum/address',$args) !!}");

            var plateId = "{{ Input::get('plateId') }}";
            if(plateId != ''){
                $(document).on("touchend",".x-addr",function(){
                    var id = $(this).data('id');
                    var url = "{!! u('Forum/addbbs',['plateId'=>Input::get('plateId'), 'postId'=>Input::get('postId'),'addressId' => ADDID]) !!}".replace("ADDID", id);
                    $.router.load(url, true);
                })
             }

            $(document).on("touchend",".urlte",function(){
                $(".x-addr").unbind();
                var url = "{{ u('Forum/addressdetail' ,array('id' => 'ids') )}}".replace("ids", $(this).parents(".x-addr").data('id'));
                $.router.load(url, true);
            });
            // 删除地址
            $(".x-addr .x-delico").touchend(function(){
                var id = $(this).parents(".x-addr").data('id');
                $.showOperation("是否确认删除","javascript:$.deladds(" + id + ");");
                
            });

            $.deladds = function(id){
                //关闭弹框                
                $(".operation").addClass("none");

                var obj = $(".x-addr"+id);
                var plateId = "{{ Input::get('plateId') }}";
                if(plateId == '') {
                    $.post("{{ u('Forum/deladdress') }}", {id: id}, function (res) {
                        if (res.code == 0) {
                            obj.slideUp('fast', function () {
                                obj.remove();
                                var rems = 0;
                                $(".x-addr").each(function(i,v){
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
            $(".x-addr .x-setDuf").touchend(function(){
                var obj = $(this).parents(".x-addr");
                var athis = $(this);
                var id = obj.data('id');
                var plateId = "{{ Input::get('plateId') }}";
                if(plateId == ''){
                    $.post("{{ u('Forum/setdefault') }}",{id:id},function(res){
                        if(res.code == 0){
                            $(".x-addr").removeClass("on");
                            obj.addClass("on");
                            $(".x-addr").find("a").removeClass("x-okaddress").addClass("x-okaddress1");
                            athis.removeClass("x-okaddress1").addClass("x-okaddress");
                        }
                    },"json");
                }

            });


        });
    </script>
@stop