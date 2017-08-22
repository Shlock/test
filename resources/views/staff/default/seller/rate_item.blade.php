@foreach($evaluation['eva'] as $v)
    <li class="evaluation{{$v['id']}}_show">
        <div class="clearfix user-info">
            <span class="f_l name">{{$v['userName']}}</span>
            <span class="f_r time">{{$v['createTime']}}</span>
        </div>
        <div class="small">
            <div class="start-b down">
                <i class="icon iconfont">&#xe645;</i>
                <i class="icon iconfont">&#xe645;</i>
                <i class="icon iconfont">&#xe645;</i>
                <i class="icon iconfont">&#xe645;</i>
                <i class="icon iconfont">&#xe645;</i>
                <div class="start-b top" style=" width:{{ $v['star'] * 20 }}%;">
                    <i class="icon iconfont">&#xe645;</i>
                    <i class="icon iconfont">&#xe645;</i>
                    <i class="icon iconfont">&#xe645;</i>
                    <i class="icon iconfont">&#xe645;</i>
                    <i class="icon iconfont">&#xe645;</i>
                </div>
            </div>
        </div>
        <p class="reply-text">{{$v['content']}}</p>
        <div class="show-img-list">
            @foreach($v['images'] as $i)
                <img src="{{$i}}"/>
            @endforeach
        </div>
        @if($v['reply'])
            <div class="evaluation-b">回复：{{$v['reply']}}</div>
        @endif
        <div class="clearfix">
            <a  onclick="JumpURL('{{u('Order/detail',['id'=>$v['orderId'],"url_css"=>$id_action.$ajaxurl_page])}}','#order_detail_view',2)" href="#" class="f_l x-toorder external pageloding">订单详情<i class="icon iconfont">&#xe609;</i></a>
            @if($args['type'] == '1')
                <div class="call-black prompt-title-ok" data-id="{{$v['id']}}">回复</div>
            @endif
        </div>
    </li>
@endforeach