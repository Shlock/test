<style type="text/css">  
    .datetime{text-align: center;height: 35px;}
    .datetime input,select{border: 1px solid skyblue;text-align: center;height: 25px;width: 50px;}  
    .yuetime ul {text-align: center;margin-top:5px;}
    .yuetime li{list-style-type:none;-moz-border-radius: 5px;-webkit-border-radius: 5px;border-radius:5px; border:  1px solid #b6b4b4}
    .yuetime ul li{margin: 2px; float: left;width:80px; height: 60px;line-height: 30px;}   
    .showtime {width:520px; padding:20px; border:1px solid #ccc; background-color:#fff;}
    .showtime input{-moz-border-radius: 5px;-webkit-border-radius: 5px;width: 165px  !important;margin-top: -10px;border: 1px solid #ccc;text-align: center;}
    .clear{clear: both;} 
    .msg{color:skyblue;}
    #datetimepicker4{width: 130px}
    .yuetime li{cursor:pointer}
    /*| crosshair | default | hand | move | help | wait | text | w-resize |s-resize | n-resize |e-resize | ne-resize |sw-resize | se-resize | nw-resize |pointer | */
</style> 
<div class="showtime"> 
    <input type="text" name="appointTimeymd" class="appointdate u-ipttext" onchange="onblurs(this)" value="点击编辑或查看预约时间" id="datetimepicker4"/>
    <span class="msg"></span>
    <div class="input clear">        
        @foreach ($data['appointTime'] as $appointTime)
            <input style='width: 50px;margin-left:4px;' readonly name='appointTime[]' date-xuanz="" type='text' value="{{ $appointTime }}">
        @endforeach
    </div>
    <div class="yuetime">
        <ul class="uli"></ul>
    </div> 
    <div class="clear"></div>
</div>
<script type="text/javascript">
    var sellerId = "{{$data['id']}}";
     $('.appointdate').datepicker({
        minDate:"{{yzday(UTC_TIME)}}"
    }); 
    var appointdate =""; 
    function onblurs() { 
        $(".uli").html("");
        $(".input").html(""); 
        $(".msg").text("");
        //时间 / html
        var timeh = -1;  
        var html = "";  
        var sta = "" ;  
        var appointdate = $(".appointdate").val().replace("-", "").replace("-", "");
        $.post("{{ u('Seller/schedule')}}",{sellerId:sellerId,date:appointdate},function(result){ 
            res = eval(result.data);
            $.each(res.hours,function(key,value){  
                if(value.status == "-1"){      
                    html +="<li data-time="+key+"><span>"+value.hour+"</span><div>不可预约</div></li> ";               
                }else{
                    html +="<li data-time="+key+"><span>"+value.hour+"</span><div>可预约</div></li> ";                    
                }
            }); 
            $(".uli").append(html);
            $(".yuetime  li").each(function(){
                sta = $(this).children("div").text();
                if( sta == "不可预约"){               
                    $(this).css("color","#fff");
                    $(this).css("background","#b50000");
                }else{
                    $(this).css("color","#000"); 
                    $(this).css("background","#fff");
                }
            });
            $(".yuetime li").on("click",function(){  
                var query = {}; 
                var appoint = $(".appointdate").val().split('-');
                    query.sellerId = sellerId;
                var input = $(this).attr("data-time"); 
                $(this).each(function(){

                    var shijian = $(this).children('span').text();             
                    var h = shijian.split(':'); 

                    if(h[0]<10){hrs = "0"+h[0]}else{hrs = h[0];} 

                    sta = $(this).children("div").text();   
                    query.hours  = appoint[0]+""+appoint[1]+""+appoint[2]+""+hrs; 

                    if(sta == "可预约"){           
                        query.status  = "-1";  
                        getUrl(query,1);                            
                        $(this).css("color","#fff");
                        $(this).css("background","#b50000");
                        $(this).children("div").text("不可预约"); 
                    }else{  
                        query.status  = 0;  
                        getUrl(query,2);            
                        $(this).css("color","#000"); 
                        $(this).css("background","#fff"); 
                        $(this).children("div").text("可预约");
                    }

                }); 
            }); 
        },'json');  
        function getUrl(query,type){
            $.post("{{ u('Seller/updatatime')}}",query,function(result){  
                if(result.status == true){ 
                    if(type == 2){
                        $(".msg").text("取消拒绝预约时间成功");
                    }else{
                        $(".msg").text("设置拒绝预约时间成功");
                    }
                }else{
                    $(".msg").text("设置失败");
                }
            },'json');
        }
    }; 
</script>