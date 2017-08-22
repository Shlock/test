@foreach($list as $v)
    <div class="card y-card y-address y-address{{ $v['id'] }} yz-address @if($v['isDefault']) active @endif" data-id="{{ $v['id'] }}">
        <div class="card-content">
            <div class="card-content-inner" data-id="{{ $v['id'] }}">
                <p><span>{{ $v['name'] }}</span><span class="fr">{{ $v['mobile'] }}</span></p>
                <p class="mt5">{{ $v['address'] }}</p>
            </div>
        </div>
        <div class="card-footer c-gray2 f12">
            <div><i class="icon iconfont mr5 f20 vat c-red y-addron x-setDuf">&#xe612;</i>@if($v['isDefault']) 默认 @else 设为默认 @endif</div>
            <div>
                <span class="mr10 urlte pageloading"><i class="icon iconfont mr5 f18 vat">&#xe63c;</i>编辑</span>
                <span class="y-del"><i class="icon iconfont mr5 f18 vat">&#xe630;</i>删除</span>
            </div>
        </div>
    </div>
@endforeach
