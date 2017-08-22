@foreach($list as $k=>$v)
    @if($v['send_type'] != 3)
        <div class="msg-list">
            <div class="y-dpmc">
                <div class="y-dpmclogo"><img src="@if($v['logo'] == '') {{ asset('wap/community/client/images/ico/sz5.png') }} @else {{$v['logo']}} @endif"></div>
                <div class="y-dpmcmain">
                    <div class="y-bgjtimg"><img src="{{ asset('wap/community/client/images/ico/jt-left.png') }}"></div>
                    <h4 class="f14">{{$v['title']}}</h4>
                    <div class="y-dpmccont f14">
                        <p>{{$v['content']}}</p>
                        @if($v['send_type'] == 2)
                            <p><a href="{{$v['args']}}" style="color:blue;">{{$v['args']}}</a></p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="y-dptitle f12"><span>{{Time::toDate($v['send_time'],'Y-m-d H:i:s')}}</span></div>
        </div>
    @else
        <div class="msg-list">
            <div class="y-dpmc">
                <div class="y-dpmclogo"><img src="@if($v['logo'] == '') {{ asset('wap/community/client/images/ico/sz5.png') }} @else {{$v['logo']}} @endif"></div>
                <div class="y-dpmcmain">
                    <div class="y-bgjtimg"><img src="{{ asset('wap/community/client/images/ico/jt-left.png') }}"></div>
                    <h4 class="f14">{{$v['title']}}</h4>
                    <p class="c-green f12">{{$v['content']}}</p>
                    <div class="y-jdmain f14">
                        <p><span>商品数量：<span class="c-red">{{$v['count']}}份</span></span></p>
                        <p><span>订单总金额：<span class="c-red">￥{{$v['total_fee']}}</span></span></p>
                        <p><span>预约时间：<span class="c-green">{{Time::toDate($v['app_time'],'Y-m-d H:i:s')}}</span></span></p>
                    </div>
                    <p class="y-ckxq f14 msgnative" data-args="{{$v['args']}}"><a href="javascript:;">点击查看详情<i class="x-rightico"></i></a></p>
                </div>
            </div>
            <div class="y-dptitle f12"><span>{{Time::toDate($v['send_time'],'Y-m-d H:i:s')}}</span></div>
        </div>
    @endif
@endforeach
