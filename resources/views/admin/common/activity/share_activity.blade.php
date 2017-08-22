@extends('admin._layouts.base')
@section('css')
<style type="text/css">
    .add_content{ width: 80px; height: 30px; cursor: pointer}
    .y-fenlei tr td{padding: 5px;}
    .y-fenlei ,.y-fenlei tr th,.y-fenlei tr td{border: 1px #ccc solid;text-align: center;}
    .y-fenlei{clear: both;width: 450px;margin-left:105px;}

    .form-tip{background-color: #F9F9F9;padding: 10px 0px;margin-bottom: 10px;}

    .sle{float: left;margin-right: 10px;}
    .y-yhqsl{width:70px;line-height: 30px;border: 1px solid #ddd;margin-right: 10px;text-align: center;}

    .ioio{border: 1px solid #000;padding-left: 5px; max-width: 650px; margin-top: 2px;}
    .cur{cursor: pointer;margin-left: 15px;}

</style>
@stop
@section('right_content')
	@yizan_begin
        <script src="{{ asset('js/layer/layer.js') }}"></script>

        <yz:form id="yz_form" action="save_share_activity">

            <yz:fitem name="name" label="活动名称"></yz:fitem>

            <div id="bgimage-form-item" class="u-fitem clearfix ">
		            <span class="f-tt">
		                 活动背景:
		            </span>
                <div class="f-boxr">
                    <ul class="m-tpyllst clearfix">
                        <li id="bgimage-box" class="">
                            <a id="img-preview-bgimage" class="m-tpyllst-img" href="javascript:;" target="_self">
                                @if(!empty($data['bgimage']))
                                    <img src="{{$data['bgimage']}}" alt="">
                                @else
                                    <img src="" alt="" style="display:none;">
                                @endif
                            </a>
                            <a class="m-tpyllst-btn img-update-btn" href="javascript:;" data-rel="bgimage">
                                <i class="fa fa-plus"></i> 上传图片
                            </a>
                            @if(!empty($data['bgimage']))
                                <input type="hidden" data-tip-rel="#bgimage-box" name="bgimage" id="bgimage" value="{{$data['bgimage']}}">
                            @else
                                <input type="hidden" data-tip-rel="#bgimage-box" name="bgimage" id="bgimage" value="">
                            @endif
                        </li>
                        &nbsp;<span>建议尺寸：1080px*360px,支持JPG\PNG格式，例:<a style="color: #0000C2;cursor: pointer" class="show">点击查看</a></span>
                    </ul>
                </div>
            </div>

            <yz:fitem name="startTime" label="活动开始时间" type="date"></yz:fitem>
            <yz:fitem name="endTime" label="活动结束时间" type="date"></yz:fitem>

            @if(empty($data))
                <div id="promotion-form-item" class="u-fitem clearfix ">
                    <span class="f-tt">
                         选择优惠券:
                    </span>
                    <div class="f-boxr" style="margin-bottom: 1em;">
                        <select name="promotionId" class="sle promotion-sle">
                            <option selected="" value="0">选择优惠券</option>
                        </select>
                    </div>
                </div>
            @else
                <div id="promotion-form-item" class="u-fitem clearfix ">
                    <span class="f-tt">
                         选择优惠券:
                    </span>
                    <div class="f-boxr" style="margin-bottom: 1em;">
                        <select name="promotionId" class="sle promotion-sle">
                            <option value="0">选择优惠券</option>
                            <option selected="" value="{{$data['promotion'][0]['promotionId']}}">{{$data['promotion'][0]['promotion']['name']}}</option>
                        </select>
                    </div>
                </div>
            @endif

            <div id="num-form-item" class="u-fitem clearfix ">
		            <span class="f-tt">
		                 发放数量:
		            </span>
                <div class="f-boxr">
                    <input type="text" name="num" id="num" class="u-ipttext my" style="width:80px;" value="{{$data['promotion'][0]['num']}}" maxlength="8">&nbsp;<span>张</span>
                </div>
            </div>

            <div id="money-form-item" class="u-fitem clearfix ">
                <span class="f-tt">
                     订单限制金额:
                </span>
                <div class="f-boxr">
                    <input type="text" name="money" id="money" class="u-ipttext my" style="width:80px;" value="{{$data['money']}}" maxlength="8">&nbsp;<span>元，>=限制金额可获得分享优惠券机会</span>
                </div>
            </div>

            <div id="sharePromotionNum-form-item" class="u-fitem clearfix ">
                <span class="f-tt">
                     单次获得个数:
                </span>
                <div class="f-boxr">
                    <input type="text" name="sharePromotionNum" id="sharePromotionNum" class="u-ipttext my" style="width:80px;" value="{{$data['sharePromotionNum']}}" maxlength="8">&nbsp;<span>每个订单获得优惠券的个数</span>
                </div>
            </div>

            <div id="title-form-item" class="u-fitem clearfix ">
		            <span class="f-tt">
		                 分享链接标题:
		            </span>
                <div class="f-boxr">
                    <input type="text" name="title" id="title" class="u-ipttext" value="{{$data['title']}}" maxlength="20">&nbsp;<span>限20个字符内,例:<a style="color: #0000C2;cursor: pointer" class="show2">点击查看</a></span>
                </div>
            </div>

            <div id="detail-form-item" class="u-fitem clearfix ">
		            <span class="f-tt">
		                 分享链接内容:
		            </span>
                <div class="f-boxr">
                    <input type="text" name="detail" id="detail" class="u-ipttext" value="{{$data['detail']}}">&nbsp;<span>限30个字符内,例:<a style="color: #0000C2;cursor: pointer" class="show2">点击查看</a></span>
                </div>
            </div>

            <div id="image-form-item" class="u-fitem clearfix ">
		            <span class="f-tt">
		                 分享链接图片:
		            </span>
                <div class="f-boxr">
                    <ul class="m-tpyllst clearfix">
                        <li id="image-box" class="">
                            <a id="img-preview-image" class="m-tpyllst-img" href="javascript:;" target="_self">
                                @if(!empty($data['image']))
                                    <img src="{{$data['image']}}" alt="">
                                @else
                                    <img src="" alt="" style="display:none;">
                                @endif
                            </a>
                            <a class="m-tpyllst-btn img-update-btn" href="javascript:;" data-rel="image">
                                <i class="fa fa-plus"></i> 上传图片
                            </a>
                            @if(!empty($data['image']))
                                <input type="hidden" data-tip-rel="#image-box" name="image" id="image" value="{{$data['image']}}">
                            @else
                                <input type="hidden" data-tip-rel="#image-box" name="image" id="image" value="">
                            @endif
                        </li>
                        例:<a style="color: #0000C2;cursor: pointer" class="show2">点击查看</a>
                    </ul>
                </div>
            </div>

            <yz:fitem name="buttonName" label="按钮名称"></yz:fitem>
            <yz:fitem name="buttonUrl" label="按钮连接"></yz:fitem>
            <yz:fitem name="brief" label="活动细则">
                <yz:Editor name="brief" value="{{ $data['brief'] }}"></yz:Editor>
            </yz:fitem>

            <yz:fitem label="状态">
                <php> $data['status'] = isset($data['status']) ? $data['status'] : 1; </php>
                <yz:radio name="status" options="1,0" texts="启用,禁用" checked="$data['status']"></yz:radio>
            </yz:fitem>
        </yz:form>

        <div id="share3" style="display: none"><img src="{{ asset('images/share3.png') }}"></div>
        <div id="share2" style="display: none"><img src="{{ asset('images/share2.png') }}"></div>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $('.show').click(function(){
            layer.open({
                type: 1,
                title: false,
                closeBtn: 0,
                area: '690px',
                skin: 'layui-layer-nobg',
                shadeClose: true,
                shade:0.8,
                content: $('#share3')
            });
        })

        $('.show2').click(function(){
            layer.open({
                type: 1,
                title: false,
                closeBtn: 0,
                area: '600px',
                skin: 'layui-layer-nobg',
                shadeClose: true,
                shade:0.8,
                content: $('#share2')
            });
        })
        
        $(document).on('keyup afterpaste', '.my', function(event) {
            var value = parseInt(this.value);
            if (isNaN(value)) {
                $(this).val('');
                return;
            }
            $(this).val(value);
        });

        $.getPromotion = function() {
            var startTime = $("#startTime").val();
            var endTime = $("#endTime").val();
            if(startTime == "" || endTime == "" ){
                return false;
            }
            var i = 0;
            $('.count').each(function(){
                i++;
            })

            $(".promotion-sle").html("<option value=''>正在加载中...</option>");
            $.post("{{ u('Activity/getpromotion') }}",{startTime:startTime,endTime2:endTime},function(result){
                if(result.code == 0){
                    var html = '<option selected="" value="">选择优惠券</option>';
                    $(result.data.list).each(function(o){
                        html += '<option value="'+this.id+'">'+this.name+'</option>';
                    });
                    $('.promotion-sle').html(html);
                }else{
                    $.ShowAlert("数据有错误！");
                    return false;
                }
            },'json');
        }

        $("#startTime").change(function(){
            $.getPromotion();
        })

        $("#endTime").change(function(){
            $.getPromotion();
        })
    })
</script>
@stop