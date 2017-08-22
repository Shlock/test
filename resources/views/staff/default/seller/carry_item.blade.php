<div class="list-block">
    <ul>
        <li class="item-content">
            <div class="item-inner flex-start">
                <div class="item-title">可提现金额：</div>
                <div class="item-after"><span class="f_red" id="is_carry_money" data="{{$money or 0}}">¥{{$money or 0}}</span></div>
            </div>
        </li>
        <li class="item-content">
            <div class="item-inner pr">
                <div class="item-title">提现金额：<span class="carry_moneys"></span></div>
                <div class="item-input">
                    <input type="hidden" name="carry_money" value="" />
                </div>
                <div id="" class="carry_all J_carry_all">全部转出</div>
            </div>
        </li>
    </ul>
</div>
<div class="account_hd_bottom content-padded">
    <p class="button button-fill button-success ajax-success-bnt">发起提现</p>
</div>
<div class="list-block cards-list">
    <ul>
        <li class="card">
            <div class="card-header">账户信息</div>
            <div class="card-content">
                <div class="card-content-inner">
                    <p>开户行：&nbsp;{{$bank['bankName']}}</p>
                    <p>开户名：&nbsp;{{$bank['name']}}</p>
                    <p>卡&nbsp;&nbsp;&nbsp;号：&nbsp;{{ $bank['bankNo'] ? substr_replace($bank['bankNo'],'**********',5,10) : null }}</p>
                </div>
            </div>
        </li>
        <li class="card">
            <div class="card-header">说明</div>
            <div class="card-content">
                <div class="card-content-inner">
                    {!! $bank['notice'] !!}
                </div>
            </div>
        </li>
    </ul>
</div>