@extends('seller._layouts.base')
@section('content')
<div>
    <div class="m-zjgltbg">                 
        <div class="p10">
            <div class="g-fwgl">
                <p class="f-bhtt f14 clearfix">
                    <span class="ml15 fl">门禁管理</span>
                </p>
            </div>
            <div class="m-tab m-smfw-ser pt20">
				@yizan_begin
					<yz:form id="yz_form" action="save"> 
						<input type="hidden" name="puserId" value="{{$args['puserId']}}">
						<yz:fitem name="doorId" label="可用门禁">
				        	<yz:checkBox name="doorId[]" options="$doorIds" valuefield="id" textfield="name" selected="$data['doorId']"></yz:checkBox>
				        </yz:fitem>
						<yz:fitem name="endTime" label="截止时间" type="date"></yz:fitem>
					</yz:form>
				@yizan_end
            </div>
        </div>
    </div>
</div>
@stop