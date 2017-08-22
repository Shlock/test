<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@section('title')首页@show{{$site_config['site_title']}}</title>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="stylesheet" href="{{ asset('seller/js/suimobile/sm-extend.min.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('seller/js/suimobile/sm.min.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('seller/css/public.css') }}?{{TPL_VERSION }}">
    <script src="{{ asset('seller/js/suimobile/zepto.min.js') }}" charset='utf-8'></script>
	<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry"></script>
    @yield('css')
    @yield('top_js')
    <script>
        var item_pages = 2;
        var idshow = 100 * 2;
        // 加载flag
        var loading = false;

    </script>
</head>
<body>
<div class="page page-current" id="{{$id_action.$ajaxurl_page}}" data-ajaxurl="{!! u(CONTROLLER_NAME.'/'.ACTION_NAME,$args) !!}">
    @section('show_top')
        <header class="bar bar-nav">
            <a class="button button-link button-nav pull-left back" data-transition='slide-out'>
                <span class="icon iconfont">&#xe64c;</span>
            </a>
            <h1 class="title">{{$title}}</h1>
        </header>
    @show

    @section('show_nav')
        <nav class="bar bar-tab">
            <a class="tab-item @if($active == 'index') active @endif" href="#" onclick="JumpURL('{{u('Index/index')}}','#index_index_view',2)">
                <span class="icon iconfont i-neworder"></span>
                <span class="tab-label">新订单</span>
            </a>
            <a class="tab-item @if($active == 'order') active @endif" href="#" onclick="JumpURL('{{u('Order/index')}}','#order_index_view_2',2)">
                <span class="icon iconfont i-ordermanage"></span>
                <span class="tab-label">订单管理</span>
            </a>
            @if(in_array($role,['1','3','5','7']))
                <a class="tab-item @if($active == 'seller') active @endif" href="#" onclick="JumpURL('{{u('Seller/index')}}','#seller_index_view',2)">
                    <span class="icon iconfont i-shop"></span>
                    <span class="tab-label">店铺</span>
                </a>
            @endif
            <a class="tab-item @if($active == 'mine') active @endif" href="#" onclick="JumpURL('{{u('Mine/index')}}','#mine_index_view',2)">
                <span class="icon iconfont i-mine"></span>
                <span class="tab-label">我的</span>
            </a>
        </nav>
    @show
    @yield('preview')
    <div class="content @yield('contentcss')" @yield('distance')>
        @yield('content')
        @if($show_preloader)
            <!-- 加载提示符 -->
            <div class="infinite-scroll-preloader">
                <div class="preloader"></div>
            </div>
        @endif
        <div class="pa w100 tc allEnd hide">
            <p class="f12 c-gray mt015 mb5">暂无更多数据</p>
        </div>
        @yield('page_js')
    </div>
</div>
<iframe id="changeStaffFrame" src="" width="0" height="0" style="position: absolute;top:0px;left:0px;z-index:9999;" frameborder="0"></iframe>
<script type="text/tpl" id="show_norms">
 <div id="del{idshow}" >
    <div class="delete-but"   onclick="$.deletebut({idshow})">
        <i class="icon iconfont right-ico">&#xe619;</i>
    </div>
    <ul class="goods-editer-b s-goods-editer-b" id="del{idshow}">
        <input type="hidden" placeholder="" name="norms[{idshow}][id]">
        <li>
            <div class="item-content">
                <div class="item-inner">
                    <div class="item-title label">型&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号:</div>
                    <div class="item-input">
                        <input type="text" placeholder="尺寸，颜色，大小等" name="norms[{idshow}][name]" id="norms">
                    </div>
                </div>
            </div>
        </li>
        <li>
            <div class="item-content">
                <div class="item-inner">
                    <div class="item-title label">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;价:</div>
                    <div class="item-input">
                        <input type="text" placeholder="请输入金额（元）"  name="norms[{idshow}][price]" id="price">
                    </div>
                    <span class="unit">元</span>
                </div>
            </div>
        </li>
        <li>
            <div class="item-content">
                <div class="item-inner">
                    <div class="item-title label">库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存:</div>
                    <div class="item-input">
                        <input type="text" name="norms[{idshow}][stock]" placeholder="必须是数字"  id="stock" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" >
                    </div>
                </div>
            </div>
        </li>
    </ul>
</div>
</script>

<script type="text/tpl" id="hide_norms">
<ul class="goods-editer-b h-goods-editer-b">
    <li>
        <div class="item-content">
            <div class="item-inner">
                <div class="item-title label">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;价:</div>
                <div class="item-input">
                    <input type="text" placeholder="请输入金额（元）"  name="price" id="price">
                </div>
                <span class="unit">元</span>
            </div>
        </div>
    </li>
    <li>
        <div class="item-content">
            <div class="item-inner">
                <div class="item-title label">库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存:</div>
                <div class="item-input">
                    <input type="text" placeholder="必须是数字"  name="stock" id="stock" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" >
                </div>
            </div>
        </div>
    </li>
</ul>
</script>
@section('remove_js')
    <script src="{{ asset('seller/js/suimobile/sm.min.js') }}" charset='utf-8'></script>
@show
<script src="{{ asset('seller/js/suimobile/sm-extend.min.js') }}" charset='utf-8'></script>
<script src="{{ asset('seller/js/public.js') }}" charset='utf-8'></script>
<script src="{{ asset('wap/community/newbase.js') }}" charset='utf-8'></script>
<script src="{{ asset('seller/js/Chart.js') }}?{{TPL_VERSION }}" charset='utf-8'></script>
<script type='text/javascript' src='{{ asset('seller/js/suimobile/custom_sui_datetime_picker.js') }}' charset='utf-8'></script>
@yield('js')
<script type="text/javascript">
    function doChangeStaff(staffId,staffName) {
        $(".page-current input[name=staffId]").val(staffId);
        $(".page-current input[name=staffName]").val(staffName);
        $("#changeStaffFrame").attr("src","").attr("width","0").attr("height","0");
    }

    function showStaffIframe() {
        var staffId = $(".page-current input[name=staffId]").val();
        var url = "{!! u('Seller/staff',['type'=>2,'staffId'=>STID]) !!}".replace("STID", staffId);
        $("#changeStaffFrame").attr("width","100%").attr("height","100%").attr("src", url);

    }
    var data = {};
    data.page       = '';
    data.tpl        = 'item';
    data.date       = '';
    data.keywords   = '';
    $(function(){
        //订单
        $.base = function (page,keyword,time){

            if(page == 1){
                $('.page-current .lists_item_ajax').html("");
            }
            data.page = page;
            data.keywords   = keyword;
            data.date = time;

            var psot_url =  $(".page-current").data("ajaxurl");
            $.post(psot_url,data,function(res){
                $(" .page-current .infinite-scroll-preloader").addClass("hide").hide();
                $(" .page-current .x-null").addClass("hide").hide();

                $('.page-current .lists_item_ajax').append(res);
                if(!empty(res)){
                    item_pages ++;
                    $(" .page-current .allEnd").addClass("hide").hide();
                }else{
                    loading = true;
                    // 加载完毕，则注销无限加载事件，以防不必要的加载
                    //$.detachInfiniteScroll($('.page-current .infinite-scroll'));
                    $(" .page-current .allEnd").addClass("block").show();
                    return;
                }
                data.keywords = "";
            });
        }

        // 上拉刷新 ----------------------dsy---------------------------------------------------------------------------
        $(document).on('refresh', '.page-current .pull-to-refresh-content',function(e) {
            $(" .page-current .allEnd").addClass("hide").hide();
            // 模拟2s的加载过程
            setTimeout(function() {
                $('.page-current  .lists_item_ajax').html("");
                loading = false;
                item_pages = 1;
                $.base(1,"");
                // 加载完毕需要重置
                $(" .page-current .infinite-scroll-preloader").removeClass("hide_show").show();
                $.pullToRefreshDone('.page-current .pull-to-refresh-content');
            }, 1000);
        });
//        // 下拉分页 ----------------------dsy---------------------------------------------------------------------------
        $(document).on('infinite', '.page-current .infinite-scroll-bottom',function() {
            $(" .page-current .allEnd").addClass("hide").hide();
            // 如果正在加载，则退出
            if (loading) return;
            // 设置flag
            loading = true;
            // 模拟1s的加载过程
            setTimeout(function() {
                $.base(item_pages,"");
                // 重置加载flag
                loading = false;
                // 添加新条目
                $(" .page-current .infinite-scroll-preloader").removeClass("hide_show").show();
                //$.refreshScroller();
            }, 1000);
        });  //订单管理

        $.changeStaff = function() {
            var staffId = $(".page-current input[name=staffId]").val();
            var staffIds = "";
            var staffName = "";
            $(".page-current input[name=staff]:checked").each(function(){
                if(staffIds == ""){
                    staffIds  = $(this).val();
                }else{
                    staffIds += ','+$(this).val();
                }
                staffName += $(this).siblings(".name").html()+" ";
            });
            window.parent.doChangeStaff(staffIds,staffName);
        }

        //时间选择处理事件 ----------------------dsy--------------------------------------------------------------------
        $.onChange = function(obj){
            $(".page-current .list-container").html("");
            var date  = obj.value.replace(/-/,"").replace(/-/,"");
            $(".page-current .picker-calendar").remove();
            $.base(1,'',date);
        }
        //关键字选择处理事件 ----------------------dsy------------------------------------------------------------------
        $.keywords = function ()
        {
            if (event.keyCode == 13)
            {
                event.returnValue=false;
                event.cancel = true;
                var keywords = $(".page-current  #keywords").val();
                $.base(1,keywords);
            }
        }
        //关键字选择处理事件 ----------------------dsy------------------------------------------------------------------
        $(document).on("click",".page-current #keywords_ck",function(){
            var keywords = $(".page-current #keywords").val();
            $.base(1,keywords);
        });

        //评价回复处理事件 ------------------------dsy------------------------------------------------------------------
        $(document).on('click', '.page-current .prompt-title-ok',function () {
            var ids = $(this).data('id');
            $.modal({
                title:  '回复',
                text: '<div class="list-block" style="margin: 0 0;">\
                            <ul>\
                            <li class="align-top">\
                                <div class="item-content">\
                                    <div class="item-inner">\
                                        <div class="item-input">\
                                            <textarea placeholder="请认真回复，250字之内" name="content" id="content"></textarea>\
                                        </div>\
                                    </div>\
                                </div>\
                            </li>\
                        </ul>\
                        </div>',
                buttons: [
                    {
                        text: '取消'
                    },
                    {
                        text: '确定',
                        onClick: function() {
                            var value = $("#content").val();
                            if(value ==""){
                                $.toast("请输入评价回复内容");
                                return false;
                            }
                            $.showIndicator();
                            $.post('{{u('Seller/saveevaluation')}}',{"content":value ,'id': ids},function(res){
                                if(res.code == 0){
                                    $(".evaluation"+ids+"_show").remove();
                                    var unReply = parseInt($(".unReply").text());
                                    if( unReply > 0){
                                        unReply --;
                                    }
                                    var reply = parseInt($(".reply").text());
                                    if( reply >= 0){
                                        reply ++;
                                    }
                                    $('.evaluation'+ ids+'_show').append('<div class="evaluation-b">回复：'+value+'</div>');
                                    $(".unReply").html(unReply);
                                    $(".reply").html(reply);
                                }else{
                                    $.toast(res.msg);
                                }
                                $.hideIndicator();
                            },'json');
                        }
                    }
                ]
            });

        });
        //编辑切换事件 ----------------------------dsy------------------------------------------------------------------
        $(document).on('click','.page-current #seller_editor-but',function () {

            if($(this).hasClass("ongoing"))
            {
                $(this).html("编辑").css("color","#ff2c4c").removeClass("ongoing");
                $(".page-current .management-editor .name").show().siblings(".check_all").hide();
                $(".page-current .service-list li input,.service-list li .big").hide().siblings(".img_box").show();
                $(".page-current .service-deal").toggleClass("hide");
                $(".page-current nav.bar-tab").toggleClass("hide");
            }
            else
            {
                $(this).html("完成").css("color","#a9a9a9").addClass("ongoing");
                $(".page-current .management-editor .name").hide().siblings(".check_all").show();
                $(".page-current .service-list li input,.service-list li .big").show().siblings(".img_box").hide();
                $(".page-current .service-deal").toggleClass("hide");
                $(".page-current nav.bar-tab").toggleClass("hide");
            }
        });

        //退出登录处理事件 ----------------------------dsy--------------------------------------------------------------
        $(document).on("click",".page-current .ui-button_login_out-logout",function(){
            $.post("{{ u('Staff/logout') }}",{},function(res){
                JumpURL(res,'#staff_login_view',2);
            },"json");
        });

        //编辑切换事件 ----------------------------dsy------------------------------------------------------------------
        $(document).on('click','.page-current #editor-but',function () {
            if($(this).hasClass("ongoing"))
            {
                $(this).html("编辑").css("color","#ff2c4c").removeClass("ongoing");
                $(".page-current .management-ul li .big ").hide().siblings(".reduce-icon").hide().siblings(".right-ico").show();
            }
            else
            {
                $(this).html("完成").css("color","#a9a9a9").addClass("ongoing");
                $(".management-ul li .big").show().siblings(".reduce-icon").show().siblings(".right-ico").hide();
            }
            // $.alert('Here goes alert text');
        });
        //保存数据处理事件 ----------------------------dsy--------------------------------------------------------------
        $(document).on('click','.page-current #J_end', function () {
            var type = $(".page-current .val_selected").data('type');
            var id = $(".page-current .val_selected").data('id');
            var tradeId = $(".page-current .val_selected").data('tradeid');
            var name = $(".page-current input[name='cate_name']").val();
            var data = {
                'type' : type,
                'id' : id,
                'tradeId' : tradeId,
                'name' : name
            }
            if(tradeId == ""){
                $.toast("请选择分类");
                return false;
            }
            $.post("{{ u('Seller/saveedit') }}",data,function(res){
                if(res.code == 0){
                    if(type == 1){
                        var url = "{!! u('Seller/goodslists') !!}";
                        var css = '#seller_goodslists_view';
                        if(id) {
                            $.toast("编辑商品成功");
                        }else{
                            $.toast("添加商品成功");
                        }
                    }else{
                        var url = "{!! u('Seller/seller') !!}";
                        var css = '#seller_seller_view';
                        if(id) {
                            $.toast("编辑服务成功");
                        }else{
                            $.toast("添加服务成功");
                        }
                    }
                    JumpURL(url,css,2)
                }else{
                    if(res.msg != ''){
                        $.toast(res.msg);
                    } else {
                        $.toast("操作失败");
                    }
                }
            },"json");
        });

        //删除数据处理事件 ----------------------------dsy--------------------------------------------------------------
        $(document).on('click','.page-current #del_seller',function () {
            var is_ok = $(this).data('true').replace(/(^\s*)|(\s*$)/g, "");
            var show;
            if($(this).data('type') == 2){
                show = "服务"
            }else{
                show = "商品"
            }

            var msg = "确定删除该分类？";
            if(is_ok == "false"|| is_ok == false){
                msg = "此分类下已有"+show+"，不允许删除，如需删除分类，请先删除分类下的"+show+"。";
            }
            var id = $(this).data('id');

            if(is_ok == "true" || is_ok == true){
                $.modal({
                    title:  '删除'+show,
                    text: msg,
                    buttons: [
                        {
                            text: '取消',
                            bold: true
                        },{
                            text: '确定',
                            onClick: function() {
                                $.post("{{ u('Seller/isDel') }}",{'id':id},function(res){
                                    if(res.code == 0){
                                        $.toast("删除成功");
                                        $(".del_show"+id).remove();
                                    }
                                },"json");
                            }
                        },
                    ]
                })
            }else{
                $.modal({
                    title:  '删除'+show,
                    text: msg,
                    buttons: [
                        {
                            text: '确定',
                            bold: true
                        }
                    ]
                })
            }
        });


        //分类数据处理事件 ----------------------------dsy------------------------------------------------------------------
        $.keywords_seller = function (type,status,id){
            if (event.keyCode == 13)
            {
                event.returnValue=false;
                event.cancel = true;
                var key = $(".page-current #keywords_word").val();
                var url = "{!! u('Seller/service') !!}?keywords="+key+"&status="+status+"&id="+id+"&type="+type;
                if(status != ""){
                    if(status == 1){
                        JumpURL(url,'#seller_service_view_2',2)
                    }else{
                        JumpURL(url,'#seller_service_view_1',2)
                    }
                }
            }
        }
        $.keywords_goods = function (status,id){
            if (event.keyCode == 13)
            {
                event.returnValue=false;
                event.cancel = true;
                var key = $(".page-current #keywords_word").val();
                var url = "{!! u('Seller/goods') !!}?keywords="+key+"&status="+status+"&id="+id;
                if(status != ""){
                    if(status == 1){
                        JumpURL(url,'#seller_goods_view_2',2)
                    }else{
                        JumpURL(url,'#seller_goods_view_1',2)
                    }
                }
            }
        }

        //编辑选择处理事件 -----------------------------dsy------------------------------------------------------------------
        $(document).on('click','.page-current  #goods_editor-but',function () {

            if($(this).hasClass("ongoing"))
            {
                $(this).html("编辑").css("color","#ff2c4c").removeClass("ongoing");
                $(".management-ul li .big ").hide().siblings(".reduce-icon").hide().siblings(".right-ico").show();
            }
            else
            {
                $(this).html("完成").css("color","#a9a9a9").addClass("ongoing");
                $(".management-ul li .big").show().siblings(".reduce-icon").show().siblings(".right-ico").hide();
            }
        });

        //删除选择处理事件 ----------------------------dsy------------------------------------------------------------------
        $.opgoods_all = function (type,id,status,showtype){
            var goodsId = "";
            $(".page-current input[name=goodsId]:checked").each(function(){
                if(goodsId == ""){
                    goodsId = $(this).val();
                }else{
                    goodsId += "," + $(this).val();
                }
            })
            if(goodsId == ""){
                $.toast("请选择至少一个");
                return false;
            }
            $.post("{{ u('Seller/opgoods') }}",{goodsId:goodsId,type:type},function(res){
                if(res.code == 0){
                    $.toast("操作成功")
                    if(showtype == 2){
                        // var url = "{!! u('Seller/service') !!}?status="+status+"&id="+id+"&type="+showtype ;

                        // if(status != ""){
                        //     if(status == 1){
                        //         JumpURL(url,'#seller_service_view_1',2)
                        //     }else{
                        //         JumpURL(url,'#seller_service_view_2',2)
                        //     }
                        // }
                        $.each(goodsId.split(","),function(i,v){
                            $(".del_data"+v).remove();
                        });
                    }else{
                        // var url = "{!! u('Seller/goods') !!}?status="+status+"&id="+id+"&type="+showtype ;
                        // if(status != ""){
                        //     if(status == 1){
                        //         JumpURL(url,'#seller_goods_view_1',2)
                        //     }else{
                        //         JumpURL(url,'#seller_goods_view_2',2)
                        //     }
                        // }                        
                          $.each(goodsId.split(","),function(i,v){
                            $(".del_data"+v).remove();
                        });
                    }

                }else{
                    $.toast(res.msg);
                }
            },"json");
        }

        $(document).on("click",".page-current .J_carry_all",function(){
            var is_carry_money = $(".page-current #is_carry_money").attr("data");
            $(".page-current .carry_moneys").html(is_carry_money);
            $(".page-current input[name='carry_money']").val(is_carry_money);
        });

        //提现选择处理事件 ----------------------------dsy------------------------------------------------------------------
        $(document).on("click",".page-current .ajax-success-bnt",function(){
            var money = $(".page-current input[name='carry_money']").val();
            if(!money){
                $.toast("提现金额不能为空");
                return false;
            }
			
            if(money <= 0){
                $.toast("提现金额不能小于0");
                return false;
            }
            $.post("{{ u('Seller/withdraw') }}",{'amount': money},function(res){
                $.toast(res.msg);
                if(res.code == 0){
                    var url = "{{u('Seller/account')}}?ajax=account-show&account="+res.data.money;
                    JumpURL(url,'#seller_account_view',2);
                }
            },"json");
        });


        //店铺详情-------- ----------------------------dsy------------------------------------------------------------------
        $.saveinfo = function(saveurl){
            var name = $(".page-current #save_info_name").val();
            if(empty(name)){
                $.toast("提现金额不能为空");
                return false;
            }
            var type = $(".page-current #save_info_name").data('type');
            var data = {};
            data[type] = name;
            $.post(saveurl,data,function(){
                JumpURL('{{ u('Seller/info') }}','#seller_info_view',2)
            },"json");
        } 
        $(document).on('click','.page-current .status_checkbox div',function () {
            var bln = $(".page-current input[name='status_checkbox']").is(':checked');
            if(bln){
                bln = 0;
            }else{
                bln = 1;
            }
            $.post('{{u("Seller/isStatus")}}',{"status":bln},function(){
                if(bln == false){
                    $(".page-current .status_msg").html("营业状态:暂停营业");
                }else{
                    $(".page-current .status_msg").html("营业状态:正在营业");
                }
            })
        });

        $(document).on('click','.page-current .h_checkbox div',function () {
            var bln = $(".page-current input[name='h_checkbox']").is(':checked');
            if(bln){
                bln = 0;
            }else{
                bln = 1;
            }
            $.post('{{u("Seller/isDelivery")}}',{"delivery":bln},function(){
                if(bln == false){
                    $(".page-current .h_checkbox_msg").html("货到付款:已关闭");
                }else{
                    $(".page-current .h_checkbox_msg").html("货到付款:已开启");
                }
            })
        });
        $.deletebut = function(delid){
            $(".page-current #del"+delid).remove();
            if($(".page-current .s-goods-editer-b").length == 0){
                $(".page-current .show_norms").removeClass("add-block").append($("#hide_norms").html());
            }
        }

        $.opgoods = function(type,goodsId,tradeId){

            if(goodsId == ""){
                $.toast("服务不能为空");
                return false;
            }
            if(type == 3){
                $.modal({
                    title:  '删除商品',
                    text: "确定要删除该商品",
                    buttons: [
                        {
                            text: '取消',
                            bold: true
                        },{
                            text: '确定',
                            onClick: function() {
                                $.dsy_del(goodsId,type,tradeId);
                            }
                        },
                    ]
                })
            }else{
                $.dsy_del(goodsId,type,tradeId);
            }
        }
        $.goodssave = function(tradeId,type){
            var data = $(".page-current #goods-form").serialize();
            $.showIndicator();
            $.post("{{ u('Seller/goodsSave') }}",data,function(res){
                $.hideIndicator();
                if(res.code == 0){
                    $.toast("操作成功");
                    var url,css;
                    if(type == 2){
                        url = "{{ u('Seller/service') }}?id="+tradeId+"&type=2";
                        css = "#seller_service_view_1";
                    }else{
                        url = "{{ u('Seller/goods') }}?id="+tradeId+"&type=1";
                        css = "#seller_goods_view_1";
                    }
                    JumpURL(url,css,2)
                }else{
                    $.toast(res.msg);
                }
            },"json");
        }

        $.dsy_del = function(goodsId,type,tradeId){
            $.post("{{ u('Seller/opgoods') }}",{'goodsId':goodsId,'type':type},function(res){
                if(res.code == 0){
                    $.toast("操作成功");
                    if(type == 2){
                        $('.page-current #opgoods').attr("onclick","$.opgoods(1,"+goodsId+")");
                        $('.page-current #opgoods').html("上架");
                        $(".page-current #opgoods_iconfont").html("&#xe67f;");
                    }else if(type == 1){
                        $('.page-current #opgoods').attr("onclick","$.opgoods(2,"+goodsId+")");
                        $('.page-current #opgoods').html("下架");
                        $(".page-current #opgoods_iconfont").html("&#xe67e;");
                    }else{
                        var url = "{{ u('Seller/service') }}?id="+tradeId;
                        JumpURL(url,'#seller_service_view_1',2)
                    }
                }else{
                    $.toast(res.msg);
                }
            },"json");
        }

        $.message = function(url,css,k){
            $( '.page-current  .message-k'+k).removeClass("new-message-title");
            JumpURL(url,css,2);
        }
        function prevent_default(e) {
            e.preventDefault();
        }

        function disable_scroll() {
            $(document).on('touchmove', prevent_default);
        }

        function enable_scroll() {
            $(document).unbind('touchmove', prevent_default);
        }

        var x;

        $(document).on('touchstart', '.page-current .management-ul li .top-con', function (e) {
//        $('.page-current .management-ul li .top-con').on('touchstart', function(e) {
            $('.management-ul li .top-con').css('left', '0');
            $(e.currentTarget).addClass('open');
            x = e.targetTouches[0].pageX;

        })

        $(document).on('touchmove', '.page-current .management-ul li .top-con', function (e) {
            var change = e.targetTouches[0].pageX - x;
            change = Math.min(Math.max(-3.8, change), 0);
            e.currentTarget.style.left = change + 'rem';
            if (change < -1) disable_scroll();
        })
        $(document).on('touchend', '.page-current .management-ul li .top-con', function (e) {
            var left = parseInt(e.currentTarget.style.left)
            var new_left;
            if (left < -1.9) {
                new_left = '-1.9rem';
            } else if (left > 1.9) {
                new_left = '1.9rem';
            } else {
                new_left = '0rem';
            }
            $(e.currentTarget).on('animate', function () {
                left: new_left
            }, 200);
            enable_scroll();
        });


        $(document).on('click','.page-current .add_goods_specifications',function () {
            $(".page-current .show_norms").append($(" #show_norms").html().replace(/\{idshow\}/g,idshow));
            $(".page-current .add-b").addClass("add-block");
            $(".page-current .add_goods_specifications").css({"margin-top":"0.75rem"});
            if($(".page-current .s-goods-editer-b").length != 0){
                if($(".page-current .h-goods-editer-b").length != 0){
                    $(".page-current .h-goods-editer-b").remove();
                }
            }
            idshow ++;
        });
       
        $.init();
    });

</script>
@yield('bnt_js')
</body>
</html>