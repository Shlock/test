@foreach($list['orderList'] as $v)

    <li id="list_item{{ $v['id'] }}">
        <p class="f12">
            <span><a href="{{ u('Goods/index',['type'=>$v['orderType'],'id'=>$v['sellerId']]) }}">{{ $v['shopName'] }}</a></span>
            <span class="y-zhuangtai c-red">{{ $v['orderStatusStr']}}</span>
        </p>
        <div class="y-ddcontent" onclick="window.location.href='{{ u("Order/detail",["id"=>$v["id"]]) }}'">
            <div class="y-imgcont">
                @foreach($v['goodsImages'] as $g)
                    <div><img src="{{formatImage($g,200,200)}}"></div>
                @endforeach
            </div>
            <i class="c-green">共<span class="c-red">{{$v['count']}}</span>件<b class="x-rightico"></b></i>
        </div>
        <p class="f12">
            <span class="c-green">订单总额：￥<span class="c-red">{{$v['totalFee']}}</span>元</span><span class="y-allddbtn fr mr10">
                <!--@if($v['isCanDelete'])
                    <a href="javascript:;" class="okorder" data-id="{{$v['id']}}">删除</a>
                @endif-->
                @if($v['isCanRate'])
                        <a href="{{ u('Order/comment',['orderId' => $v['id']]) }}" class="y-blue">去评价</a>
                @endif
                @if($v['isCanCancel'])
                        <a href="javascript:;" class="cancelorder" data-id="{{$v['id']}}" data-status="{{ (int)$v['isContactCancel'] }}" data-seller="{{$v['shopName']}}" data-tel="{{$v['sellerTel']}}">取消</a>
                @endif
                @if($v['isCanPay'])
                    <a href="{{ u('Order/cashierdesk',['orderId'=>$v['id']]) }}" class="y-blue">去支付</a>
                @endif
                @if($v['isCanConfirm'])
                    <a href="javascript:;" class="confirmorder" data-id="{{$v['id']}}">确认完成</a>
                @endif
            </span>
        </p>
    </li>
@endforeach