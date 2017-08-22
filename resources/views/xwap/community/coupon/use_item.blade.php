<!-- 已转H5 -->
@foreach($list as $val)
<div class="card y-coupmain @if($val['type'] == 'offset') y-coupmaindyq @endif" onclick="$.href('{{ u('Order/order',['proId'=>$val['id'],'cartIds'=>$args['cartIds'],'addressId'=>$args['addressId']]) }}')">
    <div class="card-content">
        <div class="y-coupleft f12">
            <div class="y-couplmain">
            	@if($val['type'] == 'money')
                	<p class="mb5"><i class="f18">￥</i><span>{{$val['money']}}</span></p>
                @else
					<p class="mb5"><span>抵</span></p>
                @endif
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
    </div>
</div>
@endforeach
