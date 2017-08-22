@extends('admin._layouts.base')
@section('css')

@stop
@section('right_content')
    @yizan_begin
    <yz:form id="yz_form" action="save">

        <yz:fitem name="begin_time" label="开始时间" type="datetime"></yz:fitem>
		<yz:fitem name="end_time" label="结束时间" type="datetime"></yz:fitem>
        <yz:fitem name="remark" label="请假理由" type="textarea"></yz:fitem>
        <yz:fitem name="staff_id" type="hidden" val="{{ Input::get('sid') }}"></yz:fitem>
    </yz:form>
    @yizan_end
@stop
@section('js')
    <script type="text/javascript">

    </script>
@stop
