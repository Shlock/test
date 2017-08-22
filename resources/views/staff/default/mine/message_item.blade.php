@if(!empty($list))
    @foreach($list as $k=>$v)
        <li onclick="$.message('{{ u("Mine/msgshow",['id' =>  $v['id'] ]) }}','#mine_msgshow_view','{{$v['id']}}')" >
            <div>
                <span class="message-k{{$v['id']}} message-title @if(!$v['isRead']) new-message-title @endif f_l">{{$v['title']}}</span><i></i>
                <span class="message-time f_r">{{Time::toDate($v['sendTime'],'y-m-d')}}</span>
            </div>
            <div class="blank0"> </div>
            <div class="message-content text-overflow">{{$v['content']}}</div>
        </li>
    @endforeach
@endif