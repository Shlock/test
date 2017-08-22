@foreach($account as $v)
    <li class="fine-bor flex">
        <div class="w_percentage_40 ">{{$v['createTime']}}</div>
        <div class="w_percentage_30 ">￥{{$v['money']}}</div>
        <div class="flex-1 {{$v['statusColor']}} tr w_percentage_30">{{$v['statusStr']}}</div><!--c_24cd68已拒接的颜色-->
    </li>
@endforeach