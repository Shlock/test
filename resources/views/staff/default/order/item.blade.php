@foreach($list['orders'] as $v)
    <div class=" card @if($v['isFinished']) z-red @else z-gray @endif"  onclick="JumpURL('{{u('Order/detail',['id'=>$v['id']])}}','#order_detail_view',2)">
        <div class="card-header"><div>
                <em>#{{$v['id']}}</em><span>下单时间：{{$v['createTime']}}</span>
            </div>
        </div>
        <div class="card-content">
            <div class="card-content-inner">
                <div class="f_l f8a8a8a">{{$v['shopName']}}</div>
                <div class="f_r f4eafe6">{{$v['orderStatusStr']}}</div>
            </div>
        </div>
        <div class="card-footer">
            <span>实收：￥<em class="focus-color-f">{{$v['totalFee']}}</em>元</span>
            <span>{{$v['payStatusStr']}}</span>
        </div>
    </div>
@endforeach