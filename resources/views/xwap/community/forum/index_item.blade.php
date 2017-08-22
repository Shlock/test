@if($lists)
    @foreach($lists as $item)
        <li>
            <div class="item-content">
                <div class="item-inner">
                    <div class="item-title-row">
                        <div class="item-title f15 c-black" onclick="$.href('{{ u('Forum/detail',['id'=>$item['id']])}}')">
                            {{$item['title']}}
                        </div>
                    </div>
                    <div class="item-title-row f12 mt5">
                        <div class="item-title c-gray" onclick="$.href('{{u('Forum/lists',['plateId'=>$item['plate']['id']])}}')">
                            {{$item['plate']['name']}}
                        </div>
                        <div class="item-after c-gray">{{$item['rateNum']}}<i class="icon iconfont ml10 mr2 f15">&#xe64f;</i>{{yzday($item['createTime'])}}</div>
                    </div>
                </div>
            </div>
        </li>
    @endforeach
@endif