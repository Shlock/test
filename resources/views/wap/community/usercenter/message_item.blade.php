
@if(!empty($list))
@foreach($list as $k=>$v)
    <li onclick="window.location.href = '{{ u("UserCenter/msgshow",["sellerId" => $v["seller_id"]]) }}'">
        <div class="y-ltxximg"><img src="@if($v['logo'] == '') {{ asset('wap/community/client/images/ico/sz5.png') }} @else {{$v['logo']}} @endif">@if($v['sum'] > 0)<span class="y-xhd">{{$v['sum']}}</span>@endif</div>
        <div class="y-ctsist y-ltxxmain">
            <div class="f12 y-fhtx"><strong class="y-wdxstitle">{{$v['name']}}</strong><span class="y-time c-green">{{Time::toDate($v['send_time'],'M-d')}}</span></div>
            <p class="f14">{{$v['title']}}</p>
        </div>
    </li>
@endforeach
    @endif