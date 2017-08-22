@foreach($list as $v)
<li onclick="$.href('{{u('Repair/detail', ['id'=>$v['id'], 'districtId'=> $v['districtId']])}}')">
    <label class="label-checkbox item-content">
        <div class="item-media"></div>
        <div class="item-inner">
            <div class="item-subtitle mb5">
                <span class="f18">{{$v['repairType']}}</span>
                <span class="f12 c-gray fr">{{ $v['createTime'] }}</span>
            </div>
            <div class="item-text ha f14">{{mb_substr($v['content'], 0, 20) . '......'}}</div>
            @if($v['images'][0])
                <ul class="x-postpic clearfix">
                    @foreach($v['images'] as $val)
                        <li>
                            <img src="{{ $val }}">
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </label>
</li>
@endforeach