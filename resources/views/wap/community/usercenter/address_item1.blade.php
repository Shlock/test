@foreach($list as $v)
    <div class="x-address x-address{{ $v['id'] }} yz-address @if($v['isDefault']) on @endif"  data-id="{{ $v['id'] }}" >
        <div class="x-addresson clearfix">
            <div class="x-addressl urlte">
                <p class="na1 clearfix">
                    <span>{{ $v['name'] }}</span>
                    <span class="phone">{{ $v['mobile'] }}</span>
                </p>
                <p>
                    <span class="address">{{ $v['address'] }}</span>
                </p>
            </div>
            <div class="x-addressr">
                <a href="javascript:;" class="@if(!$v['isDefault'])x-okaddress1 @else x-okaddress @endif x-setDuf fr"><i></i></a>
                <i class="x-delico fr" ></i>
            </div>
        </div>
    </div>
@endforeach