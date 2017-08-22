@extends('admin._layouts.base_nomenu')
@section('css')
    <style type="text/css">
        #refund_reason{color: #DA0809}
        .m-porbar .m-barlst li.on .f-lsbar{top: 9px;}
        .ts{text-align: center;color: #999}
        ._gray{color: #ccc;}
        .tds tr{background-color: #fff;}
    </style>
@stop

@section('return_link')
    <a href="{{ url('RefundOrder/index') }}" class="btn mb10 lsbtn-120"><i class="fa fa-reply mr10"></i>返回订单列表</a>
    @if($data['status'] == ORDER_STATUS_REFUND_AUDITING || $data['status'] == ORDER_REFUND_SELLER_AGREE)
        <a href="javascript:;" class="fr btn mb10 hsbtn-78  ml20" onclick="$.orderVerify(2)">取消退款</a>
        <a href="javascript:;" class="fr btn mb10 hsbtn-78  ml20" onclick="$.orderVerify(3)">确认退款</a>
    @endif
@stop

<?php
//dd($data);
?>
@section('right_content')
    <!-- @if($data) -->
    <div class="m-ddbgct">
        <!-- 进度条 -->
        <div class="m-ddh">
            <p class="f-tt">
                订单号：{{$data['sn']}}
                <span class="ml20">
					下单时间：{{ Time::toDate($data['createTime']) }}
				</span>
				<span class="ml20" >支付状态：
                    @if($data['payStatus']==1)
                        已支付
                    @else
                        <span class='_gray'>等待支付</span>
                    @endif
				</span>
                <span class="ml20" >订单状态：{{$data['orderStatusStr']}}</span>
            </p>
            <?php $width=(100/count($data['statusNameDate'])).'%'; $_width = ((100/count($data['statusNameDate']))-1).'%';?>
            @if($data['statusFlowImage'])
                <div class="m-porbar clearfix">
                    <img src="{{ asset('images/'.$data['statusFlowImage'].'.png') }}" alt="" class="mt20 pt10 clearfix">
                    <ul class="m-barlst clearfix tc mt20 pt10" style="width:900px;">
                        @foreach($data['statusNameDate'] as $key => $value)
                            @if($data['statusFlowImage'] == 'statusflow_2' && $key == 2)
                                <?php $color = '#efbe3b'; ?>
                            @elseif($data['statusFlowImage'] == 'statusflow_7' && $key == 3)
                                <?php $color = '#eb6868'; ?>
                            @else
                                @if($value['date']==0)
                                    <?php $color = '#ccc'; ?>
                                @else
                                    <?php $color = '#7abd54'; ?>
                                @endif
                            @endif
                            <li style="width:{{$width}};*width:{{$_width}};color:{{$color}}">
                                <p class="tc">{{$value['name']}}</p>
                                <p class="tc">{{ $value['date'] > 0 ? yztime($value['date']) : '' }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <!-- 不可操作的订单信息 -->
        <div class="m-ordergk">
            <div class="u-tt clearfix">
                <span class="fl f14">订单概况</span>
            </div>
            <div class="clearfix">
                <div class="fl m-taborder" style="width:100%;background: #FFF;">
                    <table>
                        <tr>
                            <td style="padding-left:10px">
                                <p class=" f14">会员名：{{$data['user']['name']}}</p>
                                <p class=" f14">服务地址：{{$data['name']}}，{{$data['mobile']}}，{{$data['province']}}{{$data['city']}}{{$data['area']}}{{$data['address']}}</p>
                                <p class=" f14">配送时间：{{ Time::toDate($data['appTime']) }}</p>
                                <p class=" f14">订单备注：{{ $data['buyRemark'] }}</p>
                                <p class=" f14">支付方式：{{$data['payment']['name']}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:10px"><p class=" f14">发票抬头：{{$data['invoiceRemark']}}</p></td>
                        </tr>
                        <tr>
                            <td style="padding-left:10px">
                                <p class=" f14">服务人员：{{$data['staff']['name']}}    {{$data['staff']['mobile']}} {{--<a href="{{ u('Order/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">点击重新指派</a>--}}</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @include('admin.common.refundorder.snack')
        </div>
    </div>
    <!-- @else -->
    <div class="ts">未查询到相关订单</div>
    <!-- @endif -->
@stop
@section('js')
<script type="text/tpl" id="WithdrawMoney">
<div style="width:500px;text-align:center;padding:10px;">
    <textarea name='disposeRemark' id='disposeRemark' placeholder='请务必填写退款备注。' style="width:480px;height:100px;border:1px solid #EEE"></textarea>
</div>
</script>

    <script type="text/javascript">
        var id = "{{$data['id']}}";

        $(function(){
            //接单 拒绝接单
            $.orderVerify = function(status) {
                var msg = "拒绝接单吗？";
                var okfun = $.orderVerifyFalse;
                if(status==1){
                    msg = "确认接单？";
                    okfun = $.orderVerifyTrue;
                }else if(status==2){
                    msg = "确认取消退款？";
                    okfun = $.orderVerify2;
                }else if(status==3){
                    msg = "确认退款？";
                    okfun = $.orderVerify3;
                }
                $.ShowConfirm(msg, okfun);
            }
            $.orderVerifyTrue = function (){
                // alert('确认接单');
                $.refundRemark("{{ORDER_STATUS_AFFIRM_SELLER}}",1);
            }
            $.orderVerifyFalse = function() {
                // alert('拒绝接单');
                $.refundRemark("{{ORDER_STATUS_CANCEL_SELLER}}",0);
            }
            $.orderVerify2 = function (){
                // alert('取消退款');
                $.refundRemark("{{ORDER_REFUND_ADMIN_REFUSE}}",2);
            }
            $.orderVerify3 = function() {
                // alert('确认退款');
                $.refundRemark("{{ORDER_REFUND_ADMIN_AGREE}}",3);
            }

            $.refundRemark = function(status,type){
                if(type != 1 && type != 3){
                    if(type == 2){
                        var open =  "#WithdrawMoney";
                    }else{
                        var open = "#serviceContent";
                    }
                    var dialog = $.zydialogs.open($(open).html(), {
                        boxid:'SET_GROUP_WEEBOX',
                        width:300,
                        title:'拒绝理由',
                        showClose:true,
                        showButton:true,
                        showOk:true,
                        showCancel:true,
                        okBtnName: '确认理由',
                        cancelBtnName: '取消',
                        contentType:'content',
                        onOk: function(){
                            var  refuseContent = $("#disposeRemark").val();
                            if(refuseContent != ""){
                                if(!status) {
                                    $.ShowAlert("参数错误");
                                }else{
                                    dialog.setLoading();
                                    $.post("{{ u('RefundOrder/refund') }}",{'id':id,'status':status,'refuseContent':refuseContent},function(res){
                                        $.ShowAlert(res.msg);
                                        dialog.setLoading(false);
                                        if(res.status==true) {
                                            window.location.reload();
                                        }
                                    },'json');
                                }
                            }else{
                                $.ShowAlert("请输入理由");
                            }
                        },
                        onCancel:function(){
                            $.zydialogs.close("SET_GROUP_WEEBOX");
                        }
                    });
                }else{
                    if(!status) {
                        $.ShowAlert("参数错误");
                    }else{
                        $.post("{{ u('RefundOrder/refund') }}",{'id':id,'status':status},function(res){
                            $.ShowAlert(res.msg);
                            if(res.status==true) {
                                window.location.reload();
                            }
                        },'json');
                    }
                }
            }

        })


    </script>
@stop