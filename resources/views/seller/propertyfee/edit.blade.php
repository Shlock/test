@extends('seller._layouts.base')
@section('content')
<div>
        <div class="m-zjgltbg">                 
            <div class="p10">
                <div class="g-fwgl">
                    <p class="f-bhtt f14 clearfix">
                        <span class="ml15 fl">物业费管理</span>
                    </p>
                </div>
                <div class="m-tab m-smfw-ser pt20">
                    @yizan_begin
                        <yz:form id="yz_form" action="save">
                            <yz:fitem name="buildId" label="楼栋号">
                                <select id="buildId" name="buildId" style="min-width:100px;width:auto" class="sle ">
                                    <option value=" " >请选择楼栋号</option>
                                    @foreach($buildIds as $val)
                                    <option value="{{$val['id']}}" @if($data['buildId'] == $val['id']) selected @endif>{{$val['name']}}</option>
                                    @endforeach
                                </select>
                            </yz:fitem>
                            <yz:fitem  name="roomId" label="房间号">
                                <select id="roomId" name="roomId" style="min-width:100px;width:auto" class="sle ">
                                    <option value=" " >请选择房间号</option>
                                    @foreach($roomIds as $val)
                                    <option value="{{$val['id']}}" @if($data['roomId'] == $val['id']) selected @endif>{{$val['roomNum']}}</option>
                                    @endforeach
                                </select>
                            </yz:fitem>
                            <yz:fitem name="name" label="业主"></yz:fitem>
                            <yz:fitem name="payableFee" label="应缴金额"></yz:fitem>
                            <yz:fitem name="payableTime" label="应缴日期" type="date"></yz:fitem>
                        </yz:form>
                    @yizan_end
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
<script type="text/javascript">
jQuery(function($){
    $("#buildId").change(function() {
        var buildId = $(this).val();
        var u_id = new Array(); 
        $.post("{{ u('PropertyFee/searchroom') }}",{"buildId":buildId},function(result){  
            var html = '';
            var data = result.data.list;
            $.each(data, function(index,e){
                //console.log(u_id.indexOf(result[index].id));
                if (u_id.indexOf(data[index].id) == -1){
                    html += " <option class='uid" + e.id + "' value=" + e.id + ">" + e.roomNum + "</option>";                     
                }
            });
            $('#roomId').html(html);
        },'json');
    });

});
</script>
@stop