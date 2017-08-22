<div class="pl20 pr20">
    <div class="u-antt" style=" background: #fff;">
        <a href="javascript:;" class="mt15 on m-timebtn">添加</a>
    </div>
    <div class="g-tmlstzct">
        @foreach($stime as  $v)
        <div class="m-timlst m-timlst{{$v['id']}}">
            <div class="u-timshow por">
                <div class="updatetime">
                    <p>{{$v['times']}}</p>
                    <p class="gray">{{$v['weeks']}}</p>
                    <p class="grays" style="display:none;">@foreach($v['week'] as  $wk) {{$wk}}, @endforeach</p>
                </div>
                <div class="u-czct">
                    <span data-id="{{$v['id']}}" class="mr15 f-edit f-edit{{$v['id']}}"><a href="javascript:;" class="fa fa-edit f14"></a>编辑</span>
                    <span data-id="{{$v['id']}}" data-mid="{{$v['id']}}"  data-css="m-timlst{{$v['id']}}" class="f-delet"><a href="javascript:;" class="fa fa-trash f14 dels" ></a>删除</span>  
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <span class="msg" style="display:none;"></span>
    <span class="data-id" data-css style="display:none;"></span>
</div>
<script>
    var delurl = "{{ u('Staff/deldatatime') }}";
    var gettimes = "{{ u('Staff/gettimes') }}";    
    var showtime = "{{ u('Staff/showtime') }}";    
    var staffId = " {{$data['id']}}";
     $(document).on('click','.f-delet',function() {
        $('.m-bjtimbox').slideUp();
        var id = $(this).data('id');
        var css = $(this).data('css');
        if(id == ''){
            alert('为获取到时间编号');
            return false;
        }
        $.post(delurl,{id:id,staffId:staffId},function(result){
            if(result.code == 0){ 
               $.ShowAlert(result.msg);
               $("."+css).slideUp('fast'); 
               $("."+css).remove();
              $(".m-timebtn").removeClass("none");   
              alod ();            
            }else{
               $.ShowAlert(result.msg);
            }
        },'json'); 
    });

    $(document).on('click','.f-edit',function() { 
        
        $(".m-sjdct ul li").each(function(){
            if($(this).hasClass("on")){
                $(this).removeClass("on");
            }
        });
        $(".m-bjtimbox .m-zhouct").html("");
        to_html ();
        $(".data-id").text("");
        $(".m-sjglbcbtn").text("更新");
        var s = $(".grays").text().split(",");
        for (var i=0; i < s.length; i++) {
            var label = $(".m-bjtimbox .m-zhouct label[for='to_"+ $(".grays").text().split(",")[i].replace( /^\s*/, '') +"']");
            label.find('input').checked = false;
            label.find('input').css('color','red');
            label.find('input').attr("disabled","true");
            // console.log(label);     
        }   
        $(".m-bjtimbox").animate({overflow:'toggle'});
        var id = $(this).data('id');
        $(".data-id").text(id);
        $.post(showtime,{id:id,staffId:staffId},function(result){
            if(result.code == 0){
                result = result.data;
                for (var i=0; i < result.week.length; i++) {
                    var label = $(".m-bjtimbox .m-zhouct label[for='to_"+ result.week[i] +"']");
                    label.find('span').addClass('checked');
                    label.find('input').get(0).checked = true;
                    label.find('input').get(0).disabled = false;
                }
                for (var i=0; i < result.hours.length; i++) {
                    $(".m-sjdct ul .hours"+ result.hours[i].replace(':','_')).addClass('on');
                }
            }else{
               $.ShowAlert(result.msg);
            }
        },'json'); 
    });
</script>