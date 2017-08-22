@extends('seller._layouts.base')
@section('css')

@stop
@section('content')
    @yizan_begin
        <yz:form id="yz_form" action="save" nobtn="1">
            <dl class="m-ddl">
                @yizan_yield('title')
                <dt>
                    员工信息
                    <a href="{{ u('Staff/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
                </dt>
                @yizan_stop
                <dd class="clearfix">
                    <yz:fitem name="mobile" label="手机号" attr="maxlength=11" type="text"></yz:fitem>
                    <yz:fitem name="name" label="姓名" attr="maxlength='4'" type="text"></yz:fitem>
                    <yz:fitem name="cardNumber" label="身份证" type="text"></yz:fitem>
                    <yz:fitem label="头像">
                        <a href="{{$data['avatar']}}" target="_blank">
                            <img src="{{$data['avatar']}}" alt="" width="80">
                        </a>
                    </yz:fitem>
                    <yz:fitem name="$data.seller.name" label="所在服务站" type="text"></yz:fitem>
                    <yz:fitem name="brief" label="人员简介" type="text"></yz:fitem>
                    <yz:fitem label="接单状态">
                        @if($data['orderStatus'] == 1)
                            正常接单
                        @else
                            暂停接单
                        @endif
                    </yz:fitem>
                    <yz:fitem label="接单时间">
                        {{$data['beginTime']}}
                        -
                        {{$data['endTime']}}
                    </yz:fitem>
                    <yz:fitem name="sort" label="排序" type="text"></yz:fitem>
                </dd>
            </dl>
        </yz:form>
    @yizan_end
@stop
@section('js')
@stop
