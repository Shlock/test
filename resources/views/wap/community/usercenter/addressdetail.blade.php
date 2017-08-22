@extends('wap.community._layouts.base_order')
@section('js')
    <script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry"></script>
@stop
@section('content')
    <!-- /header -->
    @section('show_top')
        <div data-role="header" data-position="fixed" class="x-header">
            <h1>我的{{ $title }}</h1>
            <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
            <a href="javascript:;" class="x-sjr ui-btn-right addr_save" data-addr="true"><i class="x-okico"></i></a>
        </div>
    @stop
    <!-- /content -->
    <div role="main" class="ui-content">
        <div class="x-lh45br x-editadd">
            <div>
                <span class="fl">收货人：</span>
                <input type="text" name="name"  id="name" placeholder="请输入收货人姓名" value="{{ $data['name'] }}" />
            </div>
            <div>
                <span class="fl"><span class="x-w3">电</span>话：</span>
                <input type="text" name="mobile"  id="mobile" placeholder="请输入收货人电话" value="{{ $data['mobile'] }}" maxlength="11"/>
            </div>
            <div>
                <span class="fl"><span class="x-w3">地</span>址：</span>
                <input type="hidden" name="detailAddress" id="address" value="{{ $data['detailAddress'] }}"/>
                <div class="x-inputbg mapurl">
                    <i class="x-addico"></i>@if($data['detailAddress']){{ $data['detailAddress'] }} @else 点击选择地址 @endif
                    <i class="x-rightico fr mr10"></i>
                </div>
            </div>
            <div class="last">
                <input type="text" name="doorplate" id="doorplate" placeholder="输入楼号门牌号等详细信息"  value="{{ $data['doorplate'] }}"/>
            </div>
            @if($data['id'] > 0)
                <!-- 编辑 -->
                <input type="hidden" id="map_point" value="{{$data['mapPointStr']}}"/>
            @else
                <!-- 新增 -->
                <input type="hidden" id="map_point" value="{{$data['mapPoint']}}"/>
            @endif
            <input type="hidden" id="id" value="{{ $data['id'] }}" />
        </div>
    </div>
<script type="text/javascript">
    jQuery(function($){        
        var cartIds = "{{ Input::get('cartIds') }}";
        var mapurl = "{{ u('UserCenter/addressmap') }}";
        var id = "{{ Input::get('id') }}";
        var plateId = "{{ Input::get('plateId') }}";
        var postId = "{{ Input::get('postId') }}";

        function getData(){
            var obj = new Object();
            obj.id = $.trim($("#id").val());
            obj.name = $.trim($("#name").val())
            obj.mobile = $.trim($("#mobile").val());
            obj.detailAddress = $.trim($("#address").val());
            obj.mapPoint = $.trim($("#map_point").val());
            obj.doorplate = $.trim($("#doorplate").val());
            return obj;
        }

        //添加地址
        $(".addr_save").touchend(function(){
            var data = getData();
            $.post("{{ u('UserCenter/saveaddress') }}",data,function(res){
                if(res.code == 0){
                    if(cartIds != '') {
                        var return_url = "{!! u('Order/order',['cartIds'=>Input::get('cartIds'),'addressId' => ADDID]) !!}".replace("ADDID", res.data.id);
                    } else if (plateId > 0) {
                        var return_url = "{!! u('Forum/addbbs',['plateId'=>Input::get('plateId'),'postId'=>Input::get('postId'),'addressId' => ADDID]) !!}".replace("ADDID", res.data.id);
                    }else {
                        var return_url = "{{ u('UserCenter/address') }}";
                    } 
                    $.showSuccess(res.msg,return_url);
                }else{
                    $.showError(res.msg);
                }
            },"json");
        })


        $(".mapurl").touchend(function() {
            if (cartIds != '') {
                mapurl += "?cartIds=" + cartIds;
            }
            if (plateId > 0) {
                mapurl += "?plateId=" + plateId + "&postId=" + postId;
            }
            if(id > 0) {
                mapurl += "?id=" + id;
            }
            var data = getData();
            $.post("{{ u('UserCenter/saveAddrData') }}",data,function(res){
                window.location.href = mapurl;
            },"json");
            
        })
    })
</script>
@stop