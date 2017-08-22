@foreach($data['paylogs'] as $v)
<li>
    <p class="f14">{{ Lang::get('wap.pay_type.'.$v['payType']) }}<span class="fr f12 c-green">{{$v['createTime']}}</span></p>
    <p class="f12 c-green mt5">交易号：{{$v['sn']}}</p>
    <p class="f12 c-green mt5">余额：{{$v['balance']}}@if($v['payType'] == 1)<span class="c-red fr">-{{$v['money']}} @else<span class="y-green1 fr">+{{$v['money']}}</span>@endif</p>
</li>
@endforeach