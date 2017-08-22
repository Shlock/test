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
    <a href="javascript:history.back(-1);" class="btn mb10"><i class="fa fa-reply mr10"></i>返回</a>
    @if($data['isCanCancel'])
		<a href="javascript:;" class="fr btn mb10 hsbtn-78  ml20" onclick="$.orderVerify(0)">取消订单</a>
	@endif
	@if($data['isCanAccept'])
		<a href="javascript:;" class="fr btn mb10 hsbtn-78 ml20" onclick="$.orderVerify(1)">接单</a>
	@endif
    @if($data['isCanStartService'])
        <a href="javascript:;" class="fr btn mb10 hsbtn-78 ml20" onclick="$.orderStart(1)">开始服务</a>
    @endif
    @if($data['isCanFinish'])
        <a href="javascript:;" class="fr btn mb10 hsbtn-78 ml20" onclick="$.orderFinish(1)">完成订单</a>
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
                                <p class=" f14">服务时间：{{ Time::toDate($data['appTime']) }}</p>
                                <p class=" f14">支付方式：{{$data['payType']}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:10px">
                                <p class=" f14">订单备注：{{ $data['buyRemark']}}</p>
                                @if($data['invoiceRemark'])
                                    <p class=" f14">发票抬头：{{ $data['invoiceRemark'] }}</p>
                                @endif
                                @if($data['giftRemark'])
                                    <p class=" f14">贺卡内容：{{ $data['giftRemark'] }}</p>
                                @endif
                                @if($data['cancelRemark'])
                                    <p class=" f14">取消原因：{{ $data['cancelRemark'] }}</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:10px">
                                <p class=" f14">服务人员：{{$data['staff']['name']}}    {{$data['staff']['mobile']}} {{--<a href="{{ u('Order/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">点击重新指派</a>--}}</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="u-tt clearfix">
                <span class="fl f14">商家信息</span>
            </div>
            <div class="clearfix">
                <div class="fl m-taborder" style="width:100%;background: #FFF;">
                    <table>
                        <tr>
                            <td style="padding-left:10px">
                                <p class=" f14">店铺名称：{{$data['seller']['name']}}</p>
                            </td>
                            <td style="padding-left:10px">
                                <p class=" f14">法人/店主：{{$data['seller']['contacts']}}   {{$data['seller']['mobile']}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:10px">
                                <p class=" f14">服务电话：{{$data['seller']['serviceTel']}}</p>
                            </td>
                            <td style="padding-left:10px">
                                <p class=" f14">商家地址：{{$data['seller']['address']}}</p>
                            </td>
                        </tr>

                    </table>
                </div>
            </div>
            @include('admin.common.serviceorder.snack')
        </div>
    </div>
    <!-- @else -->
    <div class="ts">未查询到相关订单</div>
    <!-- @endif -->
@stop
@section('js')
    <script type="text/tpl" id="serviceContent">
	<div style="width:500px;text-align:center;padding:10px;">
		<textarea name='disposeRemark' id='content' placeholder='请务必填写取消订单的理由备注。' style="width:480px;height:100px;border:1px solid #EEE"></textarea>
	</div>
</script>

    <script type="text/javascript">
        var id = "{{$data['id']}}";

        $(function(){

            //取消订单
            $.orderCancel = function() {
                var msg = "确认取消该订单吗？";
                $.ShowConfirm(msg, $.orderCancelOk);
            }
            $.orderCancelOk = function() {
                // alert('取消订单')
                $.refundRemark({{ORDER_STATUS_USER_CANCEL}});

            }
            //接单 拒绝接单
            $.orderVerify = function(status) {
                var msg = "拒绝接单吗？";
                var okfun = $.orderVerifyFalse;
                if(status==1){
                    msg = "确认接单？";
                    okfun = $.orderVerifyTrue;
                }
                $.ShowConfirm(msg, okfun);
            }
            $.orderVerifyTrue = function (){
                // alert('确认接单');
                $.refundRemark({{ORDER_STATUS_AFFIRM_SELLER}},1);
            }
            $.orderVerifyFalse = function() {
                // alert('拒绝接单');
                $.refundRemark({{ORDER_STATUS_CANCEL_ADMIN}},0);
            } 

            //开始订单
            $.orderStart = function() {
                var msg = "确认开始服务吗？";
                $.ShowConfirm(msg, $.orderStartOk);
            } 

            $.orderStartOk = function() { 
                $.refundRemark({{ORDER_STATUS_START_SERVICE}}, 1); 
            }

            //完成订单
            $.orderFinish = function() {
                var msg = "确认完成此订单吗？";
                $.ShowConfirm(msg, $.orderFinishOk);
            } 

            $.orderFinishOk = function() { 
                $.refundRemark({{ORDER_STATUS_FINISH_STAFF}}, 1); 
            }

            $.refundRemark = function(status,type){
                if(type != 1){
                    var dialog = $.zydialogs.open($("#serviceContent").html(), {
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
                            var  refuseContent = $("#content").val();
                            if(refuseContent != ""){
                                if(!status) {
                                    $.ShowAlert("参数错误");
                                }else{
                                    dialog.setLoading();
                                    $.post("{{ u('ServiceOrder/refundRemark') }}",{'id':id,'status':status,'refuseContent':refuseContent},function(res){
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
                        $.post("{{ u('ServiceOrder/refundRemark') }}",{'id':id,'status':status},function(res){
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