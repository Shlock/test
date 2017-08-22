@if($list)
    @foreach($list as $item)
            <li id="li_{{$item['id']}}" @if($args['type'] == 0) class="li-left" @endif>
                <div class="top-con" @if($item['isCheck'] == 1) onclick="$.href('{{ u('Forum/detail',['id'=>$item['id']]) }}')" @endif>
                    <p class="f16 posttit"> {{$item['title']}} @if($item['isCheck'] == 0) （待审核） @elseif($item['isCheck'] == -1) （审核未通过）  @endif</p>
                    <p class="c-gray f12 postb">
                        <span>{{$item['plate']['name']}}</span>
                        <span class="fr">{{yztime($item['createTime'])}}<i class="icon iconfont ml10 mr2 f15 vat">&#xe64f;</i>{{$item['rateNum']}}</span>
                    </p>
                </div>
                
            @if($args['type'] == 0)
                <!-- <div class="behind">
                    <a href="javascript:;" class="ui-btn delete-btn btn-del" data-id="{{$item['id']}}">
                        <span>
                            <img src="{{  asset('wap/community/client/images/ico/tzimg5.png') }}" width="20">
                        </span>
                    </a>
                    <a href="{{ u('Forum/addbbs',['plateId'=>$item['plate']['id'],'postId'=>$item['id']]) }}" class="ui-btn delete-btn btn-mod">
                        <span>
                            <img src="{{  asset('wap/community/client/images/ico/tzimg6.png') }}" width="20">
                        </span>
                    </a>
                </div> -->
                <div class="edit" onclick="$.router.load('{{ u('Forum/addbbs',['plateId'=>$item['plate']['id'],'postId'=>$item['id']]) }}', true);"><i class="icon iconfont">&#xe63c;</i></div>
                <div class="delete" onclick="listdelete({{$item['id']}})"><i class="icon iconfont">&#xe630;</i></div>
            @endif
        </li>
    @endforeach
@endif