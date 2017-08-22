@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/info') }}','#seller_info_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a href="#" class="button button-link pull-right" id="J_save_times">保存</a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop

@section('content')
    <div class="admin-shop-setting-delivery-time">
        <div class="list-block">
            <form action="" id="ajax_form">
                <ul id="stimes">
                    <li class="item-content">
                        <div class="item-inner flex-start">
                            <div class="item-title">配送时间：</div>
                            <div class="w_b">
                                <div class="item-input time-show">
                                    <input type="text" placeholder="" name="stimes[]" readonly="" class="delivery_input" />
                                </div>
                                <div style="line-height:3;">-</div>
                                <div class="item-input">
                                    <input type="text" placeholder="" name="etimes[]" readonly="" class="delivery_input" />
                                </div>
                                <i class="icon iconfont f_red add_delivery_time J_add_delivery_time">&#xe618;</i>
                            </div>
                        </div>
                    </li>
                </ul>
            </form>
        </div>
        <div class="demo_add_delivery_time">
            <li class="item-content">
                <div class="item-inner flex-start">
                    <div class="item-title"></div>
                    <div class="w_b">
                        <div class="item-input">
                            <input type="text" placeholder="" name="stimes[]" readonly="" class="delivery_input" />
                        </div>
                        <div style="line-height:3;">-</div>
                        <div class="item-input">
                            <input type="text" placeholder="" name="etimes[]" readonly="" class="delivery_input" />
                        </div>
                        <i class="icon iconfont f_red add_delivery_time J_minus_delivery_time">&#xe619;</i>
                    </div>
                </div>
            </li>
        </div>
    </div>
@stop
@section($js)
    <script type="text/javascript">
        $("#{{$id_action}}{{$ajaxurl_page}} .delivery_input").datetimePicker();
        $("#{{$id_action}}{{$ajaxurl_page}} .J_add_delivery_time").on('click',function(){
            var bin = true;
            $("#{{$id_action}}{{$ajaxurl_page}} #stimes input").each(function(){
                if($(this).val() == ""){
                    $.alert("请填写完整的配送时间");
                    bin = false;
                    return false;
                }
            });
            if(bin){

                var html_add_delivery_time = $(".demo_add_delivery_time").html();
                $("#{{$id_action}}{{$ajaxurl_page}} .list-block ul").append(html_add_delivery_time);
                $("#{{$id_action}}{{$ajaxurl_page}} .delivery_input").datetimePicker();
            }

        });
        $("#{{$id_action}}{{$ajaxurl_page}} .J_minus_delivery_time").live('click',function(){
            $(this).parent().parent().parent().remove();
        });

        $("#{{$id_action}}{{$ajaxurl_page}} #J_save_times").on('click',function(){
            $.showIndicator();
            var data  = $("#{{$id_action}}{{$ajaxurl_page}} #ajax_form").serialize();
            if(!data){
                $.toast("未选择时间");
                return false;
            }
            $.post('{{u('Seller/savedelivery')}}',data,function(){
                JumpURL('{{ u('Seller/info') }}','#seller_info_view',2)
            });
        });
    </script>
@stop