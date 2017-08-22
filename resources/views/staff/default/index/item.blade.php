@foreach($list['orders'] as $vs)
    <div class="list-block orderlist" onclick="JumpURL('{{u('Index/detail',['id'=>$vs['id']])}}','#index_detail_view',2)">
        <div class="l-ordertitle">
            <div class="f_l">下单时间：{{$vs['createTime']}}</div>
            <div class="f_r focus-color-f">{{$vs['orderStatusStr']}}</div>
        </div>
        <a href="#">
            <div class="l-ordercon">
                <div class="f_l orderconimg">
                    @foreach($vs['images'] as $goods)
                        <img src="{{$goods}}" class="logo_shop_img" />
                    @endforeach
                </div>
                <div class="f_r tot_r">共<em class="focus-color-f">{{$vs['num']}}</em>件<i class="iconfont">&#xe64b;</i></div>
            </div>
        </a>
        <div class="l-orderfoot">
            <span class="f_l">￥<em class="focus-color-f">{{$vs['payFee']}}</em>元</span>
            <span class="f_r">订单号：{{$vs['sn']}}</span>
        </div>
    </div>
@endforeach