<!-- 已转H5 -->
<div class="bar bar-footer">
    <span class="x-cart pr mr10">
        <i class="icon iconfont c-gray">&#xe618;</i>
        <span class="badge pa c-bg c-white total_amount" id="cartTotalAmount">{{ $cart['totalAmount'] }}</span>
    </span>
    <span class="f14 c-gray ml20">总价:￥</span>
    <span class="c-red f18" id="cartTotalPrice">{{number_format($cart['totalPrice'], 2) }}</span>
    <a class="x-menuok c-bg c-white f16 fr choose_complet" href="{{u('GoodsCart/index',['id'=>Input::get('id'),'type'=>Input::get('type')])}}">选好了</a>
</div>