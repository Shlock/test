
<div data-role="footer" data-position="fixed" data-tap-toggle="false" class="x-footer">
    <div class="x-tocart">
        <span class="dot c-bg"><label class="total_amount">{{ $cart['totalAmount'] }}</label></span>
    </div>
    <div class="fl x-total">
        <span class="f14 fl c-green">总价:￥</span>
        <span class="x-tprice c-red total_price">{{ $cart['totalPrice'] }}</span>
    </div>
    @if(($seller['serviceFee'] - (int)$cart['totalPrice']) >  0)
    <div class="x-menuok c-bg choose_complet no-click" style="background-color:#ccc"> 还差{{$seller['serviceFee'] - (int)$cart['totalPrice']}}元起送 </div>
    @else 
    <div class="x-menuok c-bg choose_complet"> 选好了 </div>
    @endif
</div>
<script type="text/javascript">
    $(".choose_complet").click(function(){
        if(!$(this).hasClass("no-click")){
            window.location.href="{{u('GoodsCart/index')}}";
        }else{
            return false;
        }

    });
</script>