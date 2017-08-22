<php> $n = 0;
    //$css = "f_r mr_percentage_5 order-to-mark bdr3";mr_percentage_5
    $css = "bdr3 ui-button_login_register";
</php>
<div class="pd050">
    @if($data['isCanAccept'] && in_array($role,['1','3','5','7']) )
        <php> $n = $n +1;</php>
        <a href="#" id="isCanAccept" onclick="$.isCanAccept({{$data['id']}},2 )" class="{{$css}}">接单</a>
    @endif

    @if($data['orderType'] == 1)

        @if($data['isCanStartService'] && in_array($role,['2','3','7']) )
            <php> $n = $n +1; </php>
            <a href="#" id="isCanAccept" onclick="$.isCanStartService({{$data['id']}},4 )" class="{{$css}}">开始配送</a>
        @endif

        @if($data['isCanFinishService'] && in_array($role,['2','3','7']) )
            <php> $n = $n +1; </php>
            <a href="#" id="isCanAccept" onclick="$.isCanFinishService({{$data['id']}},3 )" class="{{$css}}">配送完成</a>
        @endif
    @else
        @if($data['isCanStartService'] && in_array($role,['4','5','6','7']) )
            <php> $n = $n +1; </php>
            <a href="#" id="isCanAccept" onclick="$.isCanStartService({{$data['id']}},4 )" class="{{$css}}">开始服务</a>
        @endif

        @if($data['isCanFinishService'] && in_array($role,['4','5','6','7']) )
            <php> $n = $n +1; </php>
            <a href="#" id="isCanAccept" onclick="$.isCanFinishService({{$data['id']}},3 )" class="{{$css}}">服务完成</a>
        @endif

    @endif
</div>
@if($n == 0)
    {{--<div class="pd050">--}}
    {{--<a class="ui-button_login_register" href="#" onclick="$.daohang()"  style="background: #55adec;">导航</a>--}}
    {{--</div>--}}
@else
    {{--<div class="daohang">--}}
    {{--<a href="#" onclick="$.daohang()" class="f_l ml_percentage_5 order-to-nav bdr3">导航</a>--}}
    {{--</div>--}}
@endif
@section($js)
    <script type="text/javascript">
        $(function() {
            $.status = function (id, status) {
                $.showIndicator();
                $.post("{{ u('Order/orderReceiving') }}", {'id': id, 'status': status}, function (res) {
                    if (res.code == 0) {
                        if (status == 2) {
							$('#{{$id_action.$ajaxurl_page}} #isCanAccept').attr("onclick", "$.isCanStartService(" + res.data.id + ",4)");
							if (res.data.orderType == 1) {									
								@if(in_array($role,['2','3','7']) )
									$("#{{$id_action.$ajaxurl_page}} #isCanAccept").html("开始配送").css("color", "#fff");
								@else
									$('#{{$id_action.$ajaxurl_page}} #isCanAccept').attr("onclick", "return;");
									$("#{{$id_action.$ajaxurl_page}} #isCanAccept").html("已接单").css("color", "#fff");										
								@endif
							} else {
								@if(in_array($role,['4','5','6','7']))									
									$("#{{$id_action.$ajaxurl_page}} #isCanAccept").html("开始服务").css("color", "#fff");
								@else
									$('#{{$id_action.$ajaxurl_page}} #isCanAccept').attr("onclick", "return;");
									$("#{{$id_action.$ajaxurl_page}} #isCanAccept").html("已接单").css("color", "#fff");										
								@endif
							}
                        } else if (status == 4) {
                            $('#{{$id_action.$ajaxurl_page}} #isCanAccept').attr("onclick", "$.isCanFinishService(" + res.data.id + ",3)");
                            if (res.data.orderType == 1) {
                                $("#{{$id_action.$ajaxurl_page}} #isCanAccept").html("配送完成").css("color", "#fff");
                            } else {
                                $("#{{$id_action.$ajaxurl_page}} #isCanAccept").html("服务完成").css("color", "#fff");
                            }
                        } else if (status == 3) {
                            $("#{{$id_action.$ajaxurl_page}} #isCanAccept").remove();
                            $("#{{$id_action.$ajaxurl_page}} .daohang").html('<a class="ui-button_login_register" style="background: #55adec;">导航</a>').addClass("pd050");
                        }
                        $("#{{$id_action.$ajaxurl_page}} .order-state").html(res.data.orderStatusStr);

                    }
                    $.toast(res.msg);
                    $.hideIndicator();
                }, "json");
            }
            //接单
            $.isCanAccept = function (id, status) {
                $.status(id, status);
            }
            //开始
            $.isCanStartService = function (id, status) {
                $.status(id, status);
            }
            //完成
            $.isCanFinishService = function (id, status) {
                $.status(id, status);
            }
        });
    </script>
@stop