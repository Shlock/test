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

    .my{
        width: 120px;
        border: 1px solid #ccc;
        height: 20px;
        padding: 5px;
    }
    .my2{
        width: 120px;
        border: 1px solid #ccc;
        height: 20px;
        padding: 5px;
    }
</style>
@stop

@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save_register_activity">

            <div id="name-form-item" class="u-fitem clearfix ">
                <span class="f-tt">
                     活动名称:
                </span>
                <div class="f-boxr">
                    <input type="text" maxlength="20" style="width:224px;" value="{{ $data['name'] }}" name="name" class="my2 f-ipt fl" placeholder="3-20个字符">
                </div>
            </div>

            <yz:fitem name="startTime" label="开始时间" type="date"></yz:fitem>
            <yz:fitem name="endTime" label="结束时间" type="date"></yz:fitem>

            @if(!empty($data))
                @foreach($data['promotion'] as $k2=>$v2)
                    <div class="u-fitem clearfix myclass count">
		            <span class="f-tt">
                        @if($k2 == 0)
                            选择优惠券:
                        @else
                            &nbsp;
                        @endif
		            </span>
                        <div class="f-boxr">
                            <select name="promotion[0][id]" class="sle  ">
                                @foreach($promotionList as $k=>$v)
                                    <option @if($v2['promotionId']==$v['id']) selected="" @endif value="{{$v['id']}}">{{$v['name']}}</option>
                                @endforeach
                            </select>
                            <div class="f-boxr">
                                数量:
                                <input type="text" maxlength="8" style="width:80px;" class="my" value="{{ $v2['num'] }}" name="promotion[0][num]" placeholder="-1表示无限制" > 张

                                @if($k2 == 0)
                                    <span style="color: #0000C2;cursor: pointer" class="add_content">添加</span>
                                @else
                                    <span style="color: #ff0000;cursor: pointer" class="del_content">删除</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="u-fitem clearfix myclass count">
		            <span class="f-tt">
		                 选择优惠券:
		            </span>
                    <div class="f-boxr">
                        <select name="promotion[0][id]" class="sle promotion-sle">
                            <option selected="" value="0">选择优惠券</option>
                        </select>
                        <div class="f-boxr">
                            数量:
                            <input type="text" maxlength="8" style="width:80px;" class="my" value="{{ $data['num'] }}" name="promotion[0][num]" placeholder="-1表示无限制"> 张
                            <span style="color: #0000C2;cursor: pointer;width: 30px;" class="add_content">添加</span>
                            <span style="font-weight: bold">(请先选择开始时间和结束时间)</span>
                        </div>
                    </div>
                </div>
            @endif

            <yz:fitem label="状态">
                <php> $data['status'] = isset($data['status']) ? $data['status'] : 1; </php>
                <yz:radio name="status" options="1,0" texts="启用,禁用" checked="$data['status']"></yz:radio>
            </yz:fitem>
        </yz:form>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('keyup afterpaste', '.my', function(event) {
            var value = parseInt(this.value);
            if (isNaN(value)) {
                if (this.value != "-") {
                    $(this).val('');
                    return;
                }else{
                    $(this).val('-');
                    return;
                }
            } else {
                if (value < -1) {
                    $(this).val('-1');
                    return;
                }
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

        //添加内容
        $('.add_content').click(function(){
            var i = 0;
            $('.count').each(function(){
                i++;
            })

            var startTime = $("#startTime").val();
            var endTime = $("#endTime").val();
            if(startTime == "" || endTime == "" ){
                $.ShowAlert("请先选择开始时间和结束时间！");
                return false;
            }

            $.post("{{ u('Activity/getpromotion') }}",{startTime:startTime,endTime2:endTime},function(result){
                if(result.code == 0){
                    var count = result.data.totalCount;
                    if(i >= count){
                        $.ShowAlert("亲，添加得已经够多了！");
                        return false;
                    }

                    var html = '<div class="u-fitem clearfix count">';
                    html += '<span class="f-tt">';
                    html += '&nbsp;';
                    html += '</span>';
                    html += '<div class="f-boxr">';
                    html += '<select name="promotion['+i+'][id]" class="sle">';
                    html += '<option selected="" value="">选择优惠券</option>';

                    $(result.data.list).each(function(o){
                        html += '<option value="'+this.id+'">'+this.name+'</option>';
                    });

                    html += '</select>';
                    html += '<div class="f-boxr">';
                    html += '数量:';
                    html += '&nbsp;<input type="text" maxlength="8" style="width:80px;" class="my" value="" name="promotion['+i+'][num]" placeholder="-1表示无限制"> 张';
                    html += '&nbsp;<span style="cursor: pointer; color:#ff0000" class="del_content">删除</span>';
                    html += '</div>';
                    html += '</div>';

                    $('.myclass').after(html);
                }else{
                    $.ShowAlert("数据有错误！");
                    return false;
                }
            },'json');

        })
        //删除内容
        $(document).on('click','.del_content',function(){
            $(this).parents(".u-fitem").remove()
        })
    })
</script>
@stop