@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.m-tab table tbody td{padding: 5px 0px;}
</style>
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 人员管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">小区管理</span>
					</p>
				</div>
				<!-- 人员表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
							<btns>
								<linkbtn label="添加小区" url="{{ u('District/create') }}" css="btn-green"></linkbtn>
							</btns>
	                        <table css="goodstable">
                                <columns>
                                    <column code="name" label="小区名称" width="60" iscut="1"></column>
                                    <actions width="90">
                                        <action type="edit" css="blu"></action>
                                        <action type="destroy" css="red"></action>
                                    </actions>
                                </columns>
	                        </table>
	                    </yz:list>
	                @yizan_end
				</div>
			</div>
		</div>
	</div>
@stop

@section('js')
@stop
