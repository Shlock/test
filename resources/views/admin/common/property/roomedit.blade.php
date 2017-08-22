@extends('admin._layouts.base')
@section('right_content')
    @yizan_begin
        <yz:form id="yz_form" action="roomsave">
            <input type="hidden" name="sellerId" value="{{$sellerId}}">
            <yz:fitem label="物业公司">
                {{$seller['name']}}
            </yz:fitem>
            <yz:fitem label="小区名称">
                {{$seller['district']['name']}}
            </yz:fitem>
            @if($build)
            <yz:fitem name="buildId" label="楼栋号">
                <input type="hidden" name="buildId" value="{{$build['id']}}">
            	{{$build['name']}}
            </yz:fitem>
            @else 
            <yz:fitem name="buildId" label="楼栋号">
                <yz:select name="buildId" options="$buildIds" valuefield="id" textfield="name" selected="$data['build']['id']"></yz:select>
            </yz:fitem>
            @endif
            <yz:fitem name="roomNum" label="房间号"></yz:fitem>
            <yz:fitem name="owner" label="业主"></yz:fitem>
            <yz:fitem name="mobile" label="联系手机"></yz:fitem>
            <yz:fitem name="propertyFee" label="物业费" tip="元/月"></yz:fitem>
            <yz:fitem name="structureArea" label="建筑面积" tip="平方米"></yz:fitem>
            <yz:fitem name="roomArea" label="套内面积" tip="平方米"></yz:fitem>
            <yz:fitem name="intakeTime" label="入住时间" type="date"></yz:fitem>
            <yz:fitem label="备注" name="remark"></yz:fitem>
        </yz:form>
    @yizan_end
<script type="text/javascript">
    $(function(){ 
        $(".date").datepicker({
            changeYear:true,
            changeMonth:true,
            defaultDate:"-25y"
        });
    })
</script>
@stop
