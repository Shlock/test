<script type="text/javascript">
     $(function (){
        $.delOrders = function(oid) {
           $.showPreloader('正在删除，请稍等...');  //显示加载器
            if(oid > 0){
                $.post(delOrder_url,{id:oid},function(res){
                    $.hidePreloader();  //隐藏加载器
                    var obj = $("#list_item"+oid);                    
                    if(res.code == 0){
                        $.alert(res.msg, function(){
                            $.router.load("{{ u('Order/index') }}", true);
                        });
                    }else{
                        $.alert(res.msg);
                    }
                },'json');
            }else{
                $.hidePreloader();  //隐藏加载器
                $.alert("提交参数错误");
            }
        }
        $.cancelOrder = function(orderId,content) {
            $.showPreloader('正在取消订单...');  //显示加载器
            if(content != ""){
                 $.post("{{u('Order/cancelorder')}}", {'id':orderId,'cancelRemark':content}, function(res){
                    $.hidePreloader();  //隐藏加载器
                    if(res.code == 0) {
                        $.alert(res.msg, function(){
                            $.router.load("{{ u('Order/index') }}", true);
                        });
                    }else{
                        $.alert(res.msg);
                    }
                },'json')
            }else{
                $.hidePreloader();  //隐藏加载器
                $.alert(res.msg);
            }          
        }
        /*$.delOrders = function(orderId) {
            $.post("{{u('Order/delorder')}}", {'id':orderId}, function(res){
                $(".operation").addClass("none");
                $.alert(res.msg);
                if(res.code == 0) {
                    window.location.reload();
                }
            },'json')
        }*/

        $.confirmOrder = function(orderId) {
            $.showPreloader();  //显示加载器
            $.post("{{u('Order/confirmorder')}}", {'id':orderId}, function(res){
                $.hidePreloader();  //隐藏加载器           
                if(res.code == 0) {
                    //{{ u('Order/index',array('oid'=>oids)) }}").replace("oids",orderId)
                    $.alert(res.msg, function(){
                        $.router.load("{{ u('Order/index') }}", true);
                    });
                }else{
                    $.alert(res.msg);
                }
            },'json')
        }
        $(document).on("touchend",".y-tkyy span", function(){
            $(".y-tkyy span").removeClass("on");
            $(this).addClass("on"); 
            $("#cancelorder").val("").val($(this).text());
        });
    })
</script>