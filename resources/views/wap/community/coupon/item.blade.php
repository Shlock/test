@foreach($list['list'] as $val)
<li @if($val['type'] == 'offset') class="y-xcq2" @else class="y-xcq1" @endif>
	<div class="y-xcql">
		<p class="y-xcqmoney">
            @if($val['type'] == 'money')
                ￥<span style="font-size:1.5em;">{{$val['money']}}</span>
            @else
                <span>抵</span>
            @endif
        </p>
		<p class="y-xcqtime f12">到期时间</p>
		<p class="y-xcqtime f12">{{$val['expireTimeStr']}}</p>
	</div>
	<div class="y-xcqr">
		<div class="y-xcqr11">
			<div class="y-xcqr1">
				<p class="y-xcqname" style="font-size:1.5em;">{{ $val['name'] }}</p>
				<p>{{ $val['brief'] }}</p>
			</div>
		</div>
	</div>
    @if($val['status'])<div class="y-yhqysx"></div>@endif
</li>
@endforeach
