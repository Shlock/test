<script type="text/javascript">
     $(function (){
        $.delOrders = function(oid) {
            $(".operation").addClass("none");
            if(oid > 0){
                $.post(delOrder_url,{id:oid},function(res){
                    var obj = $("#list_item"+oid);                    
                    if(res.code == 0){
                        $.showSuccess(res.msg,"{{ u('Order/index') }}");
                        // window.location.href = "";
                        /*obj.slideUp('fast', function () {
                            obj.remove(); 
                        });*/
                    }else{
                        $.showError(res.msg);
                    }
                },'json');
            }else{
                $.showError("提交参数错误");
            }
        }
        $.cancelOrder = function(orderId,content) {
            if(content != ""){
                 $.post("{{u('Order/cancelorder')}}", {'id':orderId,'cancelRemark':content}, function(res){
                    $(".operation").addClass("none");
                    if(res.code == 0) {
                        $.showSuccess(res.msg,"{{ u('Order/index') }}");
                    }else{
                        $.showError(res.msg);
                    }
                },'json')
            }else{
                $.showError(res.msg);
            }          
        }
        /*$.delOrders = function(orderId) {
            $.post("{{u('Order/delorder')}}", {'id':orderId}, function(res){
                $(".operation").addClass("none");
                $.showSuccess(res.msg);
                if(res.code == 0) {
                    window.location.reload();
                }
            },'json')
        }*/

        $.confirmOrder = function(orderId) {
            $.post("{{u('Order/confirmorder')}}", {'id':orderId}, function(res){
                $(".operation").addClass("none");                
                if(res.code == 0) {
                    //{{ u('Order/index',array('oid'=>oids)) }}").replace("oids",orderId)
                    $.showSuccess(res.msg," ");
                }else{
                    $.showError(res.msg);
                }
            },'json')
        }
        $(".y-tkyy span").touchend(function(){
            $(".y-tkyy span").removeClass("on");
            $(this).addClass("on"); 
            $("#cancelorder").val("").val($(this).text());
        });
    })
</script>