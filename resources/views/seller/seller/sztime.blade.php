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
        <input type="hidden" name="id" value="{{ $data['id'] }}">
        <div class="u-antt tc" style=" background: #f9f9f9;">
            <a href="javascript:;" class="mt15 ml15 m-quxiaobtn">取消</a>
            <a href="javascript:;" data-sid="" class="mt15 ml15 on m-sjglbcbtn">保存</a>
        </div>
    </div>
</div>
<script>
    var szurl = "{{ u('Seller/updatetime') }}";   
    var adddatatime = "{{ u('Seller/addtime') }}"; 
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
    // 添加时间
    $(".m-sjglbcbtn").click(function(){
        $('.msg').text("");
        var week = new Array();
        var hour = new Array();
        var hr = new Array();
        $(".m-zhouct label input:checked").each(function(){
            week.push($(this).val());   
            $('.msg').append( $(this).parents("label").text() );
        });
        $(".m-sjdct ul li").each(function(){
            if($(this).hasClass("on")){
                hour.push($(this).find('span').data("hours"));
                hr.push($(this).text());    
            }
        });
        if(week==''){
            $.ShowAlert('你还没有选择星期几');
            return false;
        }
        if(hour==''){
            $.ShowAlert('你还没有选择预约时间');
            return false;
        }
        var staffId = $("input[name=id]").val();
        obj = new Object();
        obj.weeks = week;
        obj.hours = hour;
        obj.staffId = staffId;
        var msg = $('.msg').text();
        if($(this).text() == "更新"){
                obj.id = $(".data-id").text();
                $.post(szurl,obj,function(result){  
                    if(result.status == true){
                        $(".u-timshow .u-czct span").each(function(){
                            if($(this).data('mid') == obj.id){
                                htmls = '<div class="u-timshow por"><div class="updatetime"><p>'+hr+'</p><p class="gray">'+msg+'</p><p class="grays" style="display:none;">'+week+', </p></div><div class="u-czct"><span data-id="'+obj.id+'" class="mr15 f-edit f-edit'+obj.id+'"><a href="javascript:;" class="fa fa-edit f14"></a>编辑</span><span data-id="'+obj.id+'" data-mid="'+obj.id+'" data-css="m-timlst'+obj.id+'" class="f-delet"><a href="javascript:;" class="fa fa-trash f14 dels" ></a>删除</span></div></div>';
                                $(this).parents(".m-timlst").html(htmls);
                            }
                            $('.m-bjtimbox').slideUp();
                        });
                    $(".m-timebtn").addClass("none");
                    alod ();
                    }else{
                        $.ShowAlert(result.msg);
                    }
                },'json'); 
            }else{
                $.post(adddatatime,obj,function(result){  
                    if(result.status == true){
                        $(".m-sjdct ul li").each(function(){
                            $(this).removeClass("on");
                        });
                        $(".m-zhouct label input:checked").each(function(){
                            $(this).removeAttr("checked");
                            $(this).attr("disabled","true");
                        });
                        $.ShowAlert(result.msg);
                        $.post(gettimes,{id:obj.staffId},function(res){
                            var htmls = "";
                            if(res.code == 0){                  
                                $.each(res.data,function(i,v){
                                    htmls = '<div class="m-timlst m-timlst'+v.id+'"><div class="u-timshow por"><div class="updatetime"><p>'+v.times+'</p><p class="gray">'+v.weeks+'</p><p class="grays" style="display:none;">'+week+', </p></div><div class="u-czct"><span data-id="'+v.id+'" class="mr15 f-edit f-edit'+v.id+'"><a href="javascript:;" class="fa fa-edit f14"></a>编辑</span><span data-id="'+v.id+'" data-mid="'+v.id+'" data-css="m-timlst'+v.id+'" class="f-delet"><a href="javascript:;" class="fa fa-trash f14 dels" ></a>删除</span></div></div></div>';
                                });
                            }
                            $('.g-tmlstzct').append(htmls);
                            alod ();
                        });
                        $('.m-bjtimbox').slideUp();
                        $(".m-timebtn").addClass("none");
                    }else{
                        $.ShowAlert(result.msg);
                    }
                },'json'); 
            }
    });
    
</script>