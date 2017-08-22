@if(!empty($list))

    @foreach($list as $k=>$v)
        <li data-id="{{$v['id']}}">
            <div class="y-ltxximg">
                @if($v['type'] == 1)
                    <img src="{{ asset('wap/community/client/images/ltxximg1.png') }}">
                @elseif($v['type'] == 2)
                    <img src="{{$v['avatar']}}">
                @endif
            </div>
            <div class="y-ctsist y-ltxxmain">
                <div class="f12 c-green y-fhtx">
                    <span class="y-wdxstitle">
                       @if($v['type'] == 1)
                            系统消息
                        @elseif($v['type'] == 2)
                           {{$v['username']}}
                        @endif
                        @if($v['readTime'] == 0)
                        <span class="y-xhd"></span>
                        @endif
                    </span>
                    <span class="y-time">{{$v['sendTime']}}</span>
                </div>
                <p class="f14">{{$v['content']}}</p>
                <p class="f12 c-green"><a href="#">{{$v['forumTitle']}}</a></p>
            </div>
            <div class="behind">
                <a href="#" class="ui-btn delete-btn" data-id="{{$v['id']}}"><span>删除</span></a>
            </div>
        </li>
    @endforeach

@endif