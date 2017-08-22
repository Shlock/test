@if(!empty($list))
    @foreach($list as $vo)
        <li class="clearfix">
            <a href="" class="m-tximg"><img src="{{$vo['avatar'] != null ? formatImage($vo['avatar'],40,40) : asset('wap/community/client/images/wdtx-wzc.png') }}" alt=""></a>
            <div class="m-pjshow">
                <p class="f-pjtt clearfix">
                    <span class="fl">{{$vo['userName']}}</span>
                    <span class="fr">{{$vo['createTime']}}</span>
                </p>
                <p class="clearfix">
                                        <span class="star-rank">
                                            <!-- 五颗星总长70px，此时星级的长度用百分比控制 -->
                                            <span class="star-score" style="max-width:70px;width:{{$vo['star'] * 20}}%;"></span>
                                        </span>
                </p>
                <p class="f14">{{$vo['content']}}</p>
                @if(!empty($vo['images']))
                <div class="x-plimglst">
                    <ul class="clearfix">
                        @foreach($vo['images'] as $img)
                        <li>
                            <a href="{{$img}}"><img src="{{formatImage($img,65,65)}}" alt=""></a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(!empty($vo['reply']))
                <p class="x-sjhf">商家回复：{{ $vo['reply'] }}</p>
                @endif
            </div>
        </li>

    @endforeach
@else
    <div class="u-sbtip" style="padding-bottom:20px;">
        <img src="{{ asset('wap/images/error.png') }}" alt="">
        <p class="tc f14">暂时还没有评论</p>
    </div>
@endif