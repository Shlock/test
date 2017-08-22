@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 人员管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">菜单分类管理</span>
					</p>
				</div>
				<!-- 人员表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
							<btns>
								<linkbtn label="添加分类" url="{{ u('GoodsType/add') }}" css="btn-green"></linkbtn>
							</btns>
	                        <table css="goodstable">
                                <columns>
                                	<column code="name" label="分类" width="60"></column>
                                    <column code="ico" label="分类图标" type="image" width="50"></column>
                                    <column code="sort" label="排序" width="60"></column>
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
