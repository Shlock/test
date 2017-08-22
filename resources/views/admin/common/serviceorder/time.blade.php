<style type="text/css">  
    .datetime{text-align: center;height: 35px;}
    .datetime input,select{border: 1px solid skyblue;text-align: center;height: 25px;width: 50px;}  
    .yuetime ul {text-align: center;margin-top:5px;}
    .yuetime li{list-style-type:none;-moz-border-radius: 5px;-webkit-border-radius: 5px;border-radius:5px; border:  1px solid #b6b4b4}
    .yuetime ul li{margin: 2px; float: left;width:80px; height: 60px;line-height: 30px;}   
    .showtime {width:520px; padding:20px; border:1px solid #ccc; background-color:#fff;}
    .showtime input{-moz-border-radius: 5px;-webkit-border-radius: 5px;width: 65px;margin-top: -10px;border: 1px solid #ccc;text-align: center;}
    .clear{clear: both;} 
    .msg{color:skyblue;}
    #datetimepicker4{width: 130px}
</style> 
<div class="showtime"> 
    <input type="text" name="appointTime" class="appointdate" onchange="onblurs(this)" value="点击选择时间" id="datetimepicker4"/>
    <span class="msg"></span> 
    <div class="yuetime">
        <ul class="uli"></ul>
    </div> 
    <div class="clear"></div>
</div>
<script type="text/javascript">
    $('.appointdate').datepicker({
        minDate:"{{yzday(UTC_TIME)}}"
    }); 

    var appointdate =""; 
    function onblurs() { 
        appointdate = $(".appointdate").val();
        $(".uli").html("");
        $(".input").html("");
        
        var html = "";
        var style = "";
        var timeh = 9; 
        
        if(sellerId < 1) {
            $.ShowAlert("请选择服务");
            return false;
        }
        var checktime = $("#datetimepicker4").val().replace("-", "").replace("-", "");

        //获取可选择时间
        $.post("{{ u('Order/schedule') }}",{'sellerId':sellerId,'date':checktime},function(res){
            res = eval(res);
            $.each(res,function(key,value){
                if(value.status != 0){
                    html ="<li data-time="+value.hour+" data-status="+value.status+" style='color:#999;background:#EEE'><span>"+value.hour+"</span><div>不可选</div></li>";
                }else{
                    html ="<li id='time-"+key+"' data-time="+value.hour+" data-status="+value.status+" style='color:#000;background:#fff;cursor:pointer' onclick=yuyuetime("+key+")><span>"+value.hour+"</span><div>可选择</div></li>";
                }
                 $(".uli").append(html)
            });

        });

    }; 
</script>