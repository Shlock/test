<div data-role="footer" data-tap-toggle="false" data-position="fixed" class="x-footer">
    <div data-role="navbar" class="x-footnav">
        <ul class="x-bar">
            <li @if ($nav == 'index') class="on" @endif><a href="{{ u('Index/index') }}" class="x-home"><i class="fr1"></i><span>首页</span></a></li>
            <li @if ($nav == 'goodscart') class="on" @endif><a href="{{ u('GoodsCart/index') }}" class="x-cart"><i class="fr2">@if((int)$counts['cartGoodsCount'] > 0)<span class="x-dot c-bg" id="cart_amount">{{(int)$counts['cartGoodsCount']}}</span>@endif</i><span>购物车</span></a></li>
            <li @if ($nav == 'forum') class="on" @endif><a href="{{ u('Forum/index') }}" class="x-order"><i class="fr3"></i><span>生活圈</span></a></li>
            <li @if ($nav == 'mine') class="on" @endif><a href="{{ u('User/index') }}" class="x-my"><i class="fr4">@if((int)$counts['newMsgCount'] > 0)<span class="x-dot c-bg">{{(int)$counts['newMsgCount']}}</span>@endif</i><span>我的</span></a></li>
        </ul>
    </div>
</div>