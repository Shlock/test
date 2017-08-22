@foreach($data as $k=>$v)
    @if($v['send_type'] != 3)
        <li>
            <a href="#" class="item-link item-content">
                <div class="item-media y-xtxxicon"><img src="@if($v['logo'] == '') {{ asset('wap/community/client/images/ico/sz5.png') }} @else {{ formatImage($v['logo'],64,64) }} @endif"></div>
                <div class="item-inner">
                    <div class="card-header f14">{{$v['title']}}</div>
                    <div class="card-content">
                        <div class="card-content-inner">
                            {{$v['content']}}
                            @if($v['send_type'] == 2)
                                <p><a href="{{$v['args']}}" style="color:blue;">{{$v['args']}}</a></p>
                            @endif
                        </div>
                    </div>
                    <div class="y-wdxxlistjt"><img src="{{ asset('wap/community/client/images/ico/jt-left.png') }}"></div>
                </div>
            </a>
        </li>
        <div class="y-dptitle f12"><span>{{Time::toDate($v['send_time'],'Y-m-d H:i:s')}}</span></div>
    @else
        <li>
            <a href="#" class="item-link item-content">
                <div class="item-media y-xtxxicon"><img src="@if($v['logo'] == '') {{ asset('wap/community/client/images/ico/sz5.png') }} @else {{ formatImage($v['logo'],64,64) }} @endif"></div>
                <div class="item-inner">
                    <div class="card-header f14">{{$v['title']}}</div>
                    <div class="card-content">
                        <div class="card-content-inner">
                            <p class="c-gray2 f12 mb10">{{$v['content']}}</p>
                            <p><span>商品数量：<span class="c-red">{{$v['count']}}份</span></span></p>
                            <p><span>订单总金额：<span class="c-red">￥{{$v['total_fee']}}</span></span></p>
                            <p><span>送达时间：<span class="c-gray">{{Time::toDate($v['app_time'],'Y-m-d H:i:s')}}</span></span></p>
                        </div>
                    </div>
                    <div class="card-footer f14 c-black" onclick="$.href('{{ u('Order/detail',['id' => $v['args'],'tid' => Input::get('sellerId')]) }}')">
                        点击查看详情<i class="icon iconfont c-gray2">&#xe602;</i>
                    </div>
                    <div class="y-wdxxlistjt"><img src="{{ asset('wap/community/client/images/ico/jt-left.png') }}"></div>
                </div>
            </a>
        </li>
        <div class="y-dptitle f12"><span>{{Time::toDate($v['send_time'],'Y-m-d H:i:s')}}</span></div>
    @endif
@endforeach


    
        

        


