@extends('admin._layouts.base')
@section('css')

@stop
@section('right_content')
    @yizan_begin
    <yz:list>
        <div class="u-ssct clearfix" style="position:relative;">

            <div class="search-row clearfix">
                <div id="beginTime-form-item" class="u-fitem clearfix ">
                    <span class="f-tt">服务人员： </span>
                    <div class="f-boxr">
                        {{$order['staff']['name']}}
                        <a class="btn mr5" style="position:absolute;right:0px;top:5px;" href="{{ u('Staffleave/detail',['id'=>Input::get('id')]) }}">返回</a>
                    </div>
                </div>
            </div>

            <div class="search-row clearfix">
                <div id="beginTime-form-item" class="u-fitem clearfix ">
                    <span class="f-tt">服务事项： </span>
                    <div class="f-boxr">
                        {{$order['goodsName']}}
                    </div>
                </div>
            </div>
            <div class="search-row clearfix">
                <div id="beginTime-form-item" class="u-fitem clearfix ">
                    <span class="f-tt">服务时长： </span>
                    <div class="f-boxr">
                        {{$order['Duration']}}
                    </div>
                </div>
            </div>

        </div>
    </yz:list>
    <yz:form id="yz_form" action="updatedate">
        <dl class="m-ddl">
            <dt>更改日期</dt>
            <dd class="clearfix">
                <yz:fitem name="begin_time" label="开始时间" type="datetime"></yz:fitem>
                <yz:fitem name="end_time" label="结束时间" type="datetime"></yz:fitem>
                <yz:fitem name="oid" type="hidden"></yz:fitem>
            </dd>
        </dl>

    </yz:form>


    @yizan_end
@stop
@section('js')
    <script type="text/javascript">

    </script>
@stop
