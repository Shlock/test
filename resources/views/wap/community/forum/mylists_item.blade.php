@if($list)
@foreach($list as $item)
    <li class="x-postlist">
        <div class="y-tzli">
            <a @if($item['isCheck'] == 1) href="{{ u('Forum/detail',['id'=>$item['id']]) }}" @endif><p class="f16 mb10">{{$item['title']}} @if($item['isCheck'] == 0) （待审核） @elseif($item['isCheck'] == -1) （审核未通过）  @endif</p></a>
            <p class="f12 c-green">
                <a href="{{ u('Forum/index',['plateId'=>$item['plate']['id']]) }}" class="lf1">{{$item['plate']['name']}}</a>
                <a href="javascript:;" class="lf2 ml20">{{$item['rateNum']}}</a>
                <span class="lf3">{{yztime($item['createTime'])}}</span>
            </p>
        </div>
        @if($args['type'] == 0)
        <div class="behind">
            <a href="javascript:;" class="ui-btn delete-btn btn-del" data-id="{{$item['id']}}"><span><img src="{{  asset('wap/community/client/images/ico/tzimg5.png') }}" width="20"></span></a>
            <a href="{{ u('Forum/addbbs',['plateId'=>$item['plate']['id'],'postId'=>$item['id']]) }}" class="ui-btn delete-btn btn-mod"><span><img src="{{  asset('wap/community/client/images/ico/tzimg6.png') }}" width="20"></span></a>
        </div>
        @endif
    </li>
@endforeach
@endif