<nav class="bar bar-tab">
    <a class="tab-item @if($nav == 'index') active @endif pageloading" href="{{ u('Index/index') }}" data-no-cache="true">
        <span class="icon icon-home iconfont">@if($nav == 'index') &#xe61c; @else &#xe61b; @endif</span><!-- &#xe61c; -->
        <span class="tab-label">首页</span>
    </a>
    <a class="tab-item @if($nav == 'goodscart') active @endif pageloading" href="{{ u('GoodsCart/index') }}" data-no-cache="true">
        <span class="icon icon-cart iconfont">@if($nav == 'goodscart') &#xe619; @else &#xe618; @endif<!-- &#xe619; -->
        	@if((int)$counts['cartGoodsCount'] > 0)
        		<span class="x-dot f12" id="tpGoodsCart">{{(int)$counts['cartGoodsCount'] > 99 ? '99+' : (int)$counts['cartGoodsCount']}}</span>
        	@endif
        </span>
        <span class="tab-label">购物车</span>
    </a>
    <a class="tab-item @if($nav == 'forum') active @endif pageloading" href="{{ u('Forum/index') }}" data-no-cache="true">
        <span class="icon icon-life iconfont">@if($nav == 'forum') &#xe636; @else &#xe635; @endif</span><!-- &#xe636; -->
        <span class="tab-label">生活圈</span>
    </a>
    <a class="tab-item @if($nav == 'mine') active @endif pageloading" href="{{ u('User/index') }}" data-no-cache="true">
        <span class="icon icon-me iconfont">@if($nav == 'mine') &#xe649; @else &#xe63a; @endif<!-- &#xe63a; -->
        	@if((int)$counts['newMsgCount'] > 0)
        		<span class="x-dot f12">{{(int)$counts['newMsgCount'] > 99? '99+' : (int)$counts['newMsgCount']}}</span>
        	@endif
        </span>
        <span class="tab-label">我的</span>
    </a>
</nav>