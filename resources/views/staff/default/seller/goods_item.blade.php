@foreach($goods as $v)
    <li class="del_show{{$v['id']}}">
        <div class="fine-bor plr085 w_b top-con">
            <span class="icon iconfont reduce-icon pr-085 bottom-dels"  id="del_seller" data-true="@if($v['goodsNum'])false @else true @endif" data-id="{{$v['id']}}" data-type="{{$v['type']}}">&#xe619;</span>
            <a href="#" onclick="JumpURL('{{u('Seller/goods',['id'=>$v['id'],'type'=>$v['type']])}}','#seller_goods_view_1',2)" class="w_b_f_1">
                <span>{{$v['name']}}</span>
                <p>{{$v['goodsNum']}}个商品</p>
            </a>
            <a href="#" onclick="JumpURL('{{u('Seller/goodsedit',['id'=>$v['id'],'type'=>$v['type'],'tradeId'=>$v['tradeId']])}}','#seller_goodsedit_view',2)" class="icon iconfont pr-085 big">&#xe61f;</a>
            <a href="#" onclick="JumpURL('{{u('Seller/goods',['id'=>$v['id'],'type'=>$v['type']])}}','#seller_goods_view_1',2)" class="url icon iconfont big">&#xe655;</a>
            <i class="icon iconfont right-ico">&#xe64b;</i>
        </div>
        <div class="bottom-del"  id="del_seller" data-id="{{$v['id']}}" data-true="@if($v['goodsNum'])false @else true @endif" data-type="{{$v['type']}}">删除</div>
    </li>
@endforeach