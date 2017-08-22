<div class="pl20 pr20">                 
    <!-- 添加时间框 -->
    <div class="m-bjtimbox" style="display:none;" id="addbox">
        <p class="f-tt" style="width:99.5%;text-align: left; padding: 0 0 0 5px; ">设置时间</p>
        <div class="m-zhouct"></div>
        <div class="m-sjdct">
            <ul class="clearfix">
                <!-- DEFAULT_BEGIN_ORDER_DATE  启始值 -->
                <!-- DEFAULT_END_ORDER_DATE    结束值 -->
                <!-- SERVICE_TIME_SPAN       时间间隔 -->
                <?php 
                    //开始时间
                    $begin_time = UTC_DAY + DEFAULT_BEGIN_ORDER_DATE;
                    //结束时间
                    $end_time    = UTC_DAY + DEFAULT_END_ORDER_DATE;
                ?>
                @for($begin_time; $begin_time < $end_time; $begin_time += SERVICE_TIME_SPAN)                    
                <li class="hours{{ yztime($begin_time, 'H_i') }}">
                    <span data-hours="{{ yztime($begin_time, 'H:i') }}">{{ yztime($begin_time, 'H:i') }} ~ {{ yztime($begin_time + SERVICE_TIME_SPAN, 'H:i') }}</span><i></i>
                </li>
                @endfor
            </ul>
        </div>                              
        <div class="u-antt tc" style=" background: #f9f9f9;">
            <a href="javascript:;" class="mt15 ml15 m-quxiaobtn">取消</a>
            <a href="javascript:;" data-sid="" class="mt15 ml15 on m-sjglbcbtn">保存</a>
        </div>
    </div>
</div>
<script>
    var szurl = "{{ u('Staff/updatatime') }}";   
    var adddatatime = "{{ u('Staff/adddatatime') }}"; 
    to_html ();
    //times ();
    alod ();
    function times () {
        var htmls  = "";
        for (var i = 0; i < 24; i++) {
            if(i <= 9){
                htmls = '<li class="hours0'+i+'"><span data-hours="0'+i+':00">0'+i+':00 ~ 0'+i+':30</span><i></i></li><li class="hours0'+i+'"><span data-hours="0'+i+':30">0'+i+':00 ~ 0'+i+':00</span><i></i></li>';        
            }else{
                htmls = '<li class="hours'+i+'"><span data-hours="'+i+':00">'+i+':00 ~ '+i+':30</span><i></i></li><li class="hours'+i+'"><span data-hours="'+i+':30">'+i+':00 ~ '+i+':00</span><i></i></li>'; 
            }
            $(".m-sjdct ul").append(htmls);
        };
    }
    function to_html () {
        var to_htmls  = "";
        var ChnNum  　=["日","一","二","三","四","五","六"]; 
        for (var i = 0; i < 7; i++) {       
            to_htmls =  '<label data-to="'+i+'" for="to_'+i+'"><input type="checkbox" id="to_'+i+'" value="'+i+'">星期'+ChnNum[i]+'</label>';  
            $(".m-bjtimbox .m-zhouct").append(to_htmls);
        };
    }
    function alod () {
        var show = $(".grays").text().split(",");
        // console.log(show.length);
        if( show.length == 8){
            $(".m-timebtn").hide();
        }else{
            $(".m-timebtn").show();
        }
    }
    
</script>