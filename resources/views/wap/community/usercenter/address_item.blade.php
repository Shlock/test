@foreach($list as $v)
<li class="y-address y-address{{ $v['id'] }} yz-address @if($v['isDefault']) on @endif" data-id="{{ $v['id'] }}">
    <div>
        <span>{{ $v['name'] }}</span><span class="tel phone">{{ $v['mobile'] }}</span>
        <p class="address">{{ $v['address'] }}</p>
        <div class="y-tubiao y-addressr">
            <i class="@if($v['isDefault']) on @endif x-setDuf"></i>
            <span class="y-mraddr">@if($v['isDefault']) 默认 @else 设为默认 @endif</span>
            <span class="fr">
                <p class="f12 c-green urlte"><img src="{{  asset('wap/community/client/images/ico/bj.png') }}">编辑</p>
                <p class="f12 c-green y-del"><img src="{{  asset('wap/community/client/images/ico/del.png') }}">删除</p>
            </span>
        </div>
    </div>
</li>
@endforeach
