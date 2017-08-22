<!-- 已转H5 -->
@foreach($list['list'] as $val)
	<div class="tab active @if($val['type'] == 'offset') y-coupmaindyq @else  @endif ">
	    <div class="card y-coupmain">
	        <div class="card-content">
	            <div class="y-coupleft f12">
	                <div class="y-couplmain">
	                    <p class="mb5">
	                    	@if($val['type'] == 'money')
	                    		<i class="f18">￥</i><span>{{$val['money']}}</span>
	                    	@else
				                <span>抵</span>
				            @endif
	                    </p>
	                    <p>到期时间</p>
	                    <p>{{$val['expireTimeStr']}}</p>
	                </div>
	            </div>
	            <div class="y-coupright">
	                <div class="y-couprmain">
	                    <p>{{ $val['name'] }}</p>
	                    <p>{{ $val['brief'] }}</p>
	                </div>
	            </div>
	            @if($val['status'])<div class="y-failure"></div>@endif
	        </div>
	    </div>
	</div>
@endforeach