<style type="text/css">
.swiper-loading{width:100%; clear:both; padding-top:10px; text-align: center;}
.swiper-loading img{width:32px;}
</style>
<script>
$.SwiperInit = function(box, item, url) {
    $(box).infinitescroll({
        itemSelector    : item,
        debug           : false,
        dataType        : 'html', 
        nextUrl         : url,
        loading			: {msg:$('<div class="swiper-loading"><img src="{{ asset("wap/community/client/css/images/ajax-loader.gif") }}" /></div>')}
    },
    function()
    {
        $.mobile.pageContainer.trigger("create");
    }
    );
    return false;
}
</script>