@extends('xwap.community._layouts.base')

@section('show_top')
 <header class="bar bar-nav">
    <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:$.back(); @endif" data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>
    </a>
    <a href="javascript:addr_save()" class="button button-link button-nav pull-right open-popup" data-popup=".popup-about">
        <span class="icon iconfont c-gray f24">&#xe610;</span>
    </a>
    <h1 class="title f16">我的{{ $title }}</h1>
</header>
@stop


@section('content')
    <div class="content" id=''>
            <div class="list-block mt10 f14 y-bjshaddr">
                <ul>
                    <li class="item-content">
                        <div class="item-media">
                            <span>定位城市：</span>
                        </div>
                        <div class="item-inner">
                            <div class="item-title @if(empty($data['id'])) cityurl @endif">
                                <input type="text" name="city"  id="city" placeholder="@if($data['city']['name']){{ $data['city']['name'] }} @else 点击选择城市 @endif" readonly="" />
                            </div>
                        </div>
                    </li>
                    <li class="item-content">
                        <div class="item-media">
                            <span>地&nbsp;&nbsp;&nbsp;&nbsp;址：</span>
                        </div>
                        <input type="hidden" name="detailAddress" id="address" value="{{ $data['detailAddress'] }}"/>
                        <div class="item-inner">
                            <div class="item-title mapurl">
                                <input type="text" placeholder="@if($data['detailAddress']){{ $data['detailAddress'] }} @else 点击选择地址 @endif" readonly="">
                            </div>
                        </div>
                    </li>
                    <li class="item-content">
                        <div class="item-media">
                            <span></span>
                        </div>
                        <div class="item-inner">
                            <div class="item-title">
                                <input type="text" name="doorplate" id="doorplate" placeholder="输入楼号门牌号等详细信息"  value="{{ $data['doorplate'] }}"/>
                            </div>
                        </div>
                    </li>
                    <li class="item-content">
                        <div class="item-media">
                            <span>收货人：</span>
                        </div>
                        <div class="item-inner">
                            <div class="item-title">
                                 <input type="text" name="name"  id="name" placeholder="请输入收货人姓名" value="{{ $data['name'] }}" />
                            </div>
                        </div>
                    </li>
                    <li class="item-content">
                        <div class="item-media">
                            <span>电&nbsp;&nbsp;&nbsp;&nbsp;话：</span>
                        </div>
                        <div class="item-inner">
                            <div class="item-title">
                                <input type="text" name="mobile"  id="mobile" placeholder="请输入收货人电话" value="{{ $data['mobile'] }}" maxlength="11"/>
                            </div>
                        </div>
                    </li>
                    @if($data['id'] > 0)
                        <!-- 编辑 -->
                        @if(!empty($data['mapPoint']) && !is_array($data['mapPoint']))
                            <input type="hidden" id="map_point" value="{{$data['mapPoint']}}"/>
                        @else
                            <input type="hidden" id="map_point" value="{{$data['mapPointStr']}}"/>
                        @endif
                    @else
                        <!-- 新增 -->
                        <input type="hidden" id="map_point" value="{{$data['mapPoint']}}"/>
                    @endif
                    <input type="hidden" id="id" value="{{ $data['id'] }}" />
                    <input type="hidden" id="city_id" value="{{ $data['cityId'] }}" />
                </ul>
            </div>
        </div>
@stop

@section($js)
    <script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry"></script>
    <script type="text/javascript">
    var cartIds = "{{ Input::get('cartIds') }}";
    var mapurl = "{{ u('UserCenter/addressmap') }}";
    var id = "{{ Input::get('id') }}";
    var plateId = "{{ Input::get('plateId') }}";
    var postId = "{{ Input::get('postId') }}";
    var cityurl  = "{{ u('Index/cityservice',['type'=>1]) }}";
    var change = "{{ Input::get('change') }}";

    $(function($){
        $(document).on("touchend",".cityurl",function(){
            $.router.load(cityurl, true);
        })

        $(".mapurl").unbind("touchend");
        $(document).on("touchend",".mapurl",function(){
            if (cartIds != '') {
                mapurl = "{{ u('UserCenter/addressmap') }}?cartIds=" + cartIds;
            }
            if (plateId > 0) {
                mapurl = "{{ u('UserCenter/addressmap') }}?plateId=" + plateId + "&postId=" + postId;
            }
            if(id > 0) {
                mapurl = "{{ u('UserCenter/addressmap') }}?id=" + id;
            }
            if(change > 0) {
                mapurl = "{{ u('UserCenter/addressmap') }}?change=" + change;
            }
//            var city = $("#city").val();
//            if(city == ""){
//                $.toast("抱歉，请先选择城市");
//                return false;
//            }
            var data = getData();
            $.post("{{ u('UserCenter/saveAddrData') }}",data,function(res){
                $.router.load(mapurl, true);
            },"json");
            
        })

        $(document).on("touchend", ".y-mraddrmain", function(){
            $(this).find(".y-fxk .iconfont").toggle();
            if($(this).hasClass("on")){
                $(this).removeClass("on");
            }else{
                $(this).addClass("on");
            }
        });

    })

    function getData(){
        var obj = new Object();
        obj.id = $.trim($("#id").val());
        obj.name = $.trim($("#name").val())
        obj.mobile = $.trim($("#mobile").val());
        obj.detailAddress = $.trim($("#address").val());
        obj.mapPoint = $.trim($("#map_point").val());
        obj.doorplate = $.trim($("#doorplate").val());
        obj.cityId = $.trim($("#city_id").val());
        return obj;
    }

    function addr_save() {
		$.showPreloader('请稍候...');
        var data = getData();
        $.post("{{ u('UserCenter/saveaddress') }}",data,function(res){
            if(res.code == 0){
                if(cartIds != '') {
                    var return_url = "{!! u('GoodsCart/index',['cartIds'=>Input::get('cartIds'),'addressId' => ADDID]) !!}".replace("ADDID", res.data.id);
                } else if (plateId > 0) {
                    var return_url = "{!! u('Forum/addbbs',['plateId'=>Input::get('plateId'),'postId'=>Input::get('postId'),'addressId' => ADDID]) !!}".replace("ADDID", res.data.id);
                }else if(change > 0) {
                    var return_url = "{{ u('UserCenter/address') }}?change=" + change;
                }else{
                    var return_url = "{{ u('UserCenter/address') }}";
                } 
                $.alert(res.msg,function(){
                    $.router.load(return_url, true);
                });
            }else{
                $.alert(res.msg);
            }
			$.hidePreloader(); 
        },"json");
    }
</script>
@stop