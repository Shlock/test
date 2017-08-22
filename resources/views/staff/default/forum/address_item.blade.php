
@foreach($list as $v)
    <li class="x-addr x-addr{{ $v['id'] }} @if($v['isDefault']) on @endif" data-id="{{ $v['id'] }}">
        <div>
            <span>{{ $v['name'] }}</span><span class="tel phone">{{ $v['mobile'] }}</span>
            <p class="address">{{ $v['address'] }}</p>
        </div>
        <div class="y-tubiao">
            <p class="x-delico"><img src="{{ asset('wap/community/client/images/ico/del.png') }}"></p>
            <p class="urlte"><img src="{{ asset('wap/community/client/images/ico/edit.png') }}"></p>
        </div>
    </li>
@endforeach
