@if(!empty($list))

    @foreach($list as $k=>$v)
        <li data-id="{{$v['id']}}">
            <div class="item-content pt5 pb5">
                <div class="item-media pr">
                    @if($v['type'] == 1)
                        <img src="{{ asset('wap/community/client/images/ltxximg1.png') }}">
                    @elseif($v['type'] == 2)
                        <img src="{{$v['avatar']}}">
                    @endif
                    @if($v['readTime'] == 0)
                        <span class="dot c-bg pa"></span>
                    @endif
                </div>
                <div class="item-inner">
                    <div class="item-title-row f12">
                        <div class="item-title c-gray">
                            @if($v['type'] == 1)
                                系统消息
                            @elseif($v['type'] == 2)
                               {{$v['username']}}
                            @endif
                        </div>
                        <div class="item-after c-gray">{{$v['sendTime']}}
                        </div>
                    </div>
                    <div class="item-subtitle f14 mt5 c-black">{{$v['content']}}</div>
                    <div class="item-text ha mt5 c-gray f12">
                        <a href="#" class="f12 link">{{$v['forumTitle']}}</a>
                    </div>
                </div>
            </div>
        </li>
    @endforeach

@endif