@foreach($list['orderList'] as $v)
    <div class="card y-ddcard" id="list_item{{ $v['id'] }}">
        <div class="card-header" onclick="$.href('{{ u('Goods/index',['type'=>$v['orderType'],'id'=>$v['sellerId']]) }}')">
            <span class="y-ddmaxwidth">{{ $v['shopName'] }}</span>
            <span class="fr c-red">{{ $v['orderStatusStr']}}</span>
            <!-- <span class="fr c-gray">{{ $v['orderStatusStr']}}</span> 交易完成 -->
        </div>
        <div class="card-content">
            <div class="list-block media-list y-ddlistblock">
                <ul>
                    <li>
                        <a href="{{ u('Order/detail',['id'=>$v['id']]) }}" class="item-link item-content pageloading" external>
                            <div class="item-media">
                                @foreach($v['goodsImages'] as $g)
                                    <img src="{{formatImage($g,200,200)}}" width="45.5">
                                @endforeach
                            </div>
                            <div class="item-inner">
                                <div class="item-subtitle f12 mt5">共<span class="c-red">{{$v['count']}}</span>件</div>
                                <div class="item-subtitle f12 c-gray">
                                    <span>下单时间：{{$v['createTime']}}</span>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-footer">
            <span>订单总金额：￥<span class="c-red">{{$v['totalFee']}}</span>元</span>
            <span class="y-ddlistbtn fr">
                <!-- <a href="">XXX</a> --><!-- 红色按钮 -->
                <!-- <a href="" class="y-ddlistbtnblue">XXX</a> --><!-- 蓝色按钮 -->
                 @if($v['isCanDelete'])
                    <a href="#" class="okorder" data-id="{{$v['id']}}" >删除</a><!-- 红色按钮 -->
                @endif
                @if($v['isCanRate'])
                        <a href="{{ u('Order/comment',['orderId' => $v['id']]) }}" class="pageloading y-ddlistbtnblue y-blue" >去评价</a><!-- 蓝色按钮 -->
                @endif
                @if($v['isCanCancel'])
                        <a href="#" class="cancelorder" data-id="{{$v['id']}}" data-status="{{ $v['status'] }}" >取消</a><!-- 红色按钮 -->
                @endif
                @if($v['isCanPay'])
                    <a href="{{ u('Order/cashierdesk',['orderId'=>$v['id']]) }}" class="pageloading y-ddlistbtnblue y-blue" >去支付</a><!-- 蓝色按钮 -->
                @endif
                @if($v['isCanConfirm'])
                    <a href="#" class="y-ddlistbtnblue confirmorder" data-id="{{$v['id']}}" >确认完成</a><!-- 蓝色按钮 -->
                @endif
            </span>
        </div>
    </div>
@endforeach