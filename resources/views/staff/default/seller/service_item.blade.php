
@if($args['type'] == 2)
    @foreach($goods as $v)
        @if($v['type'] == 2)
            <li class="fine-bor w_b del_data{{ $v['id'] }}" data-id="{{ $v['id'] }}">
                <input name="goodsId" value="{{ $v['id'] }}" type="checkbox" class="mt"/>
                <div class="img_box"><img src="{{ formatImage($v['image'],70,50) }}"/></div>
                <div class="text" style="width:62.6%;">
                    <p style="word-break:keep-all; white-space:nowrap; overflow: hidden; text-overflow:ellipsis; width: 100%;">{{$v['name']}}</p>
                    <span>￥{{$v['price']}}</span>
                </div>
                <div class="w_b_f_1"></div>
                <div class="sales">销量：{{$v['saleCount']}}</div>
                <a href="#" onclick="JumpURL('{{u('Seller/editnew',['type'=>2,'tradeId'=>$id,'id'=>$v['id']])}}','#seller_editnew_view',2)" class="icon iconfont  big">&#xe61f;</a>
                {{--<a href="#" class="icon iconfont big">&#xe655;</a>--}}
            </li>
        @endif
    @endforeach
@else
    @foreach($goods as $v)
        @if($v['type'] == 1)
            <li class="fine-bor w_b del_data{{ $v['id'] }}" data-id="{{ $v['id'] }}">
                <input name="goodsId" value="{{ $v['id'] }}" type="checkbox" class="mt"/>
                <div class="img_box"><img src="{{ formatImage($v['image'],70,50) }}"/></div>
                <div class="text" style="width:62.6%;">
                    <p style="word-break:keep-all; white-space:nowrap; overflow: hidden; text-overflow:ellipsis; width: 100%;">{{$v['name']}}</p>
                    <span>￥{{$v['price']}}</span>
                </div>
                <div class="w_b_f_1"></div>
                <div class="sales">销量：{{$v['saleCount']}}</div>
                <a href="#" onclick="JumpURL('{{u('Seller/editnew',['type'=>1,'tradeId'=>$id,'id'=>$v['id']])}}','#seller_editnew_view',2)" class="icon iconfont  big">&#xe61f;</a>
                {{--<a href="#" class="icon iconfont big">&#xe655;</a>--}}
            </li>
        @endif
    @endforeach
@endif