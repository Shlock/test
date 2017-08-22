@if(!empty($list))
    @foreach($list as $vo)
    @if($args['type'] == 2)
    <li @if($vo['isDelivery'] == 0)style="background:#f3f3f3;" data-isurl="0" @else data-isurl="1" @endif data-id="{{$vo['id']}}">
        <div class="y-wdscimg todetail"><img src="{{$vo['logo']}}"></div>
        <div class="y-ddxqtext">
            <p class="f16 todetail">{{$vo['name']}}<span class="y-wdscr" data-id="{{$vo['id']}}" data-type="{{$args['type']}}"><img src="{{ asset('wap/community/client/images/ico/delete.png')}}" width="20"></span></p>
            <p class="x-pjlstct  c-green f12 mt5" style="margin-left:0px;">
                        <span class="star-rank">
                            <!-- 五颗星总长70px，此时星级的长度用百分比控制 -->
                            <span class="star-score" style="width:{{$vo['score'] * 20}}%;"></span>
                        </span>
                @if($vo['orderCount'] > 0)
                <span style="line-height:30px;padding-left:5px;">已售 {{$vo['orderCount']}}</span>
                @else
                    <span style="line-height:30px;padding-left:5px;"></span>
                @endif
                        <span class="fr">
                            <i class="x-addico"></i>
                            <span class="compute-distance" data-map-point-x="{{ $vo['mapPoint']['x'] }}" data-map-point-y="{{ $vo['mapPoint']['y'] }}"></span>
                        </span>
            </p>
            <p class="f12 c-green mt10" style="text-align:left;">{!! $vo['freight'] !!}</p>
        </div>
    </li>
    @else
    <li data-id="{{$vo['id']}}">
        <div class="y-wdscimg todetail"><img src="{{$vo['logo']}}"></div>
        <div class="y-ddxqtext">
            <p class="f16 y-margintop todetail">{{$vo['name']}}<span class="y-wdscr" data-id="{{$vo['id']}}" data-type="{{$args['type']}}"><img src="{{ asset('wap/community/client/images/ico/delete.png')}}" width="20"></span></p>
            @if($vo['salesCount'] > 0)
            <p class="f12 y-wdscyysj" style="color:#979797">已售 {{$vo['salesCount']}}</p>
            @else
                <p class="f12 y-wdscyysj"></p>
            @endif
            <p class="f12 c-red y-wdscyysj">￥{{$vo['price']}}</p>
        </div>
    </li>
    @endif
    @endforeach
@else
    <div class="x-serno c-green">
        <img src="{{  asset('wap/community/client/images/ico/cry.png') }}"  />
        <span>很抱歉，你还没有收藏！</span>
    </div>
@endif
<script type="text/javascript">
    $(function() {
        $('.todetail').click(function() { 
            var id = typeof($(this).parent().data('id')) == 'undefined' ? $(this).parent().parent().data('id') : $(this).parent().data('id');
            var type = "{{$args['type']}}";
            var isurl = typeof($(this).parent().data('isurl')) == 'undefined' ? $(this).parent().parent().data('isurl') : $(this).parent().data('isurl');
            if (type == 2) {
                if (isurl > 0) {
                   window.location.href="{!! u('Seller/detail')!!}?id=" + id; 
                };
            } else {
                window.location.href="{!! u('Goods/detail')!!}?goodsId=" + id;
            }
        })

    })

</script>