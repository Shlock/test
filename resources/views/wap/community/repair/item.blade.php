@foreach($list as $v)
<li class="x-bxdetail1" onclick="window.location.href='{{u('Repair/detail', ['id'=>$v['id'], 'districtId'=> $v['districtId']])}}'">
    <span class="x-bx1">{{$v['repairType']}}</span>
    <span class="c-green fr f12 mt5">{{ $v['createTime'] }}</span>
    <p class="x-bxdel1 mt5 f14">{{mb_substr($v['content'], 0, 20) . '......'}}</p>
    @if($v['images'][0])
    <div class="x-bxpic mt15 clearfix">
        <ul>
            @foreach($v['images'] as $val)
            <li><img src="{{ $val}} "></li>
            @endforeach
        </ul>
    </div>
    @endif
</li>
@endforeach