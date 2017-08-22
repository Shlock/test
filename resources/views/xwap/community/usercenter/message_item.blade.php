@if(!empty($list))
	@foreach($list as $k=>$v)
	    <li>
            <a href="{{ u('UserCenter/msgshow',['sellerId' => $v['seller_id']]) }}" class="item-link item-content">
                <div class="item-media y-xtxxicon">
                	<img src="@if($v['logo'] == '') {{ asset('wap/community/client/images/ico/sz5.png') }} @else {{formatImage($v['logo'],64,64)}} @endif" width="60">
                	@if($v['sum'] > 0)<span class="y-xxcont">{{$v['sum']}}</span>@endif
                </div>
                <div class="item-inner">
                    <div class="item-title-row">
                        <div class="item-title f14 bold">{{$v['name']}}</div>
                        <div class="item-after c-gray f12">{{Time::toDate($v['send_time'],'M-d')}}</div>
                    </div>
                    <div class="item-text f14 c-black mt5">{{$v['title']}}</div>
                </div>
            </a>
        </li>
	@endforeach
@endif