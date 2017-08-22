@foreach($list as $vo)
    @if($args['type'] == 2)
        <li @if($vo['isDelivery'] == 0)style="background:#f3f3f3;" data-isurl="0" @else data-isurl="1" @endif data-id="{{$vo['id']}}">
            <a href="#" class="item-link item-content">
                <div class="item-media todetail" data-id="{{$vo['id']}}">
                    <img src="{{ formatImage($vo['logo'],73,73) }}" onerror='this.src="{{ asset("wap/community/newclient/images/no.jpg") }}"' width="73">
                </div>
                <div class="item-inner">
                    <div class="item-title-row todetail" data-id="{{$vo['id']}}">
                        <div class="item-title f14">{{$vo['name']}}</div>
                        <div class="item-after"><i class="icon iconfont c-gray2 f20 y-wdscr" data-id="{{$vo['id']}}" data-type="{{$args['type']}}">&#xe630;</i></div>
                    </div>
                    <div class="item-title-row f12 c-gray mt5 mb5">
                        <div class="item-title">
                            <div class="y-starcont">
                                <div class="c-gray4 y-star">
                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                </div>
                                <div class="c-red f12 y-startwo" style="width:{{$vo['score'] * 20}}%;">
                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                </div>
                            </div>
                            @if($vo['orderCount'] > 0)
                                <span class="c-gray f12">已售{{$vo['orderCount']}}</span>
                            @else
                                <span class="c-gray f12"></span>
                            @endif
                        </div>
                        <div class="item-after">
                            <i class="icon iconfont c-gray2 f18">&#xe60d;</i>
                            <span class="compute-distance" data-map-point-x="{{ $vo['mapPoint']['x'] }}" data-map-point-y="{{ $vo['mapPoint']['y'] }}"></span>
                        </div>
                    </div>
                    <div class="item-subtitle c-gray">
                        <span class="mr10">{!! $vo['freight'] !!}</span>
                    </div>
                </div>
            </a>
        </li>
    @else
        <li data-id="{{$vo['id']}}">
            <a href="#" class="item-link item-content">
                <div class="item-media todetail" data-id="{{$vo['id']}}">
                    <img src="{{$vo['logo']}}" onerror='this.src="{{ asset("wap/community/newclient/images/no.jpg") }}"' width="73">
                </div>
                <div class="item-inner">
                    <div class="item-title-row mt10 todetail" data-id="{{$vo['id']}}">
                        <div class="item-title f14">{{$vo['name']}}</div>
                        <div class="item-after"><i class="icon iconfont c-gray2 f20 y-wdscr" data-id="{{$vo['id']}}" data-type="{{$args['type']}}">&#xe630;</i></div>
                    </div>
                    <div class="item-subtitle mb10 mt10 y-f14">
                        <span class="c-red">￥{{$vo['price']}}</span>
                    </div>
                </div>
            </a>
        </li>
    @endif
@endforeach

