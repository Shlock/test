<ul class="x-sjtab x-menut1">
    <li @if(CONTROLLER_NAME == 'Goods' && ACTION_NAME == 'index') class="on" @endif><a href="{{u('Goods/index',['id'=>Input::get('id'), 'type'=>1])}}">商品</a></li>
    <li @if(CONTROLLER_NAME == 'Goods' && ACTION_NAME == 'comment') class="on" @endif><a href="{{u('Goods/comment',['id'=>Input::get('id')])}}">评价</a></li>
    <li @if(CONTROLLER_NAME == 'Seller' && ACTION_NAME == 'detail') class="on" @endif><a href="{{u('Seller/detail',['id'=>Input::get('id')])}}">商家</a></li>
</ul> 
<div class="x-lh45 c-green x-notice x-menut2">
    <i class="x-tsico"></i>
    <div id="notice">
        @if($articles) 
        <span class="article_detail" data-content="{!!$value['content']!!}"> </span> 
        @else 
        <span>无最新公告信息</span>
        @endif
    </div>
</div>
<script>
@if(count($articles) > 1)
    // var content = "{{$value['content']}}";
    // var Mar = document.getElementById("notice");
    // var child_div=Mar.getElementsByTagName("span")
    // var picH = 45;//移动高度
    // var scrollstep=3;//移动步幅,越大越快
    // var scrolltime=20;//移动频度(毫秒)越大越慢
    // var stoptime=3000;//间断时间(毫秒)
    // var tmpH = 0;
    // Mar.innerHTML += Mar.innerHTML;
    // function start(){
        // if(tmpH < picH){
            // tmpH += scrollstep;
            // if(tmpH > picH )tmpH = picH ;
            // Mar.scrollTop = tmpH;
            // setTimeout(start,scrolltime);
        // }else{
            // tmpH = 0;
            // Mar.appendChild(child_div[0]);
            // Mar.scrollTop = 0;
            // setTimeout(start,stoptime);
        // }
    // }
    // onload=function(){setTimeout(start,stoptime)};
@endif
    var content = "{{$articles[0]['content']}}";
    if(content.length <= 28){
        $(".article_detail").html(content);
    } else {
        $(".article_detail").html("<marquee>"+content+"</marquee>");
    }
</script> 