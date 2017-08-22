@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
<?php 
$disposeStatus = [
	['id'=>0,'name'=>'全部状态'],
	['id'=>2,'name'=>'待审核'],
	['id'=>3,'name'=>'审核通过'],
	['id'=>1,'name'=>'审核失败'],
];
 ?>
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 人员管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">菜单审核</span>
					</p>
				</div>
				<!-- 人员表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
	                    	<search> 
								<row>
									<item name="name" label="美食名称"></item>
                                    <item label="审核状态">
										<yz:select name="disposeStatus" options="$disposeStatus" textfield="name" valuefield="id" selected="$search_args['disposeStatus']">
										</yz:select>
									</item> 
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>
	                        <table css="goodstable">
                                <columns>
                                    <column code="restaurant.name" label="餐厅" width="60"></column>
                                    <column code="name" label="美食名称" width="60"></column>
                                    <column code="image" label="美食图片" type="image" width="60" iscut="0"></column>
                                    <column code="price" label="售价" width="60"></column>
                                    <column code="type.name" label="分类" width="60"></column>
                                    <column code="joinService" label="参与服务" width="60">
										<!-- @if($list_item['joinService'] == 1) -->
											<yz:checkbox name="joinService" options="1,2" texts="即时送餐,预约午餐" checked="1"></yz:checkbox>
										<!-- @elseif($list_item['joinService'] == 2) -->
											<yz:checkbox name="joinService" options="1,2" texts="即时送餐,预约午餐" checked="2"></yz:checkbox>
										<!-- @elseif($list_item['joinService'] == 3) -->
											<yz:checkbox name="joinService" options="1,2" texts="即时送餐,预约午餐" checked="1,2"></yz:checkbox>
										<!-- @else -->
											<yz:checkbox name="joinService" options="1,2" texts="即时送餐,预约午餐" checked=""></yz:checkbox>
										<!-- @endif -->
									</column>
                                    <column label="状态" width="40">
                                    	<!-- @if($list_item['disposeStatus'] == 0) -->
											待审核
                                    	<!-- @elseif($list_item['disposeStatus'] == 1) -->
											审核通过
                                    	<!-- @elseif($list_item['disposeStatus'] == -1) -->
											审核失败
                                    	<!-- @endif -->
                                    </column>
                                    <actions width="20">
                                        <!-- @if($list_item['disposeStatus'] == -1) -->
											<action type="edit" css="blu"></action>
                                    	<!-- @endif -->
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
<script type="text/javascript">
	$(function(){
		$("input[name='joinService']").click(function(){
			var joinService = 0;
			var id = $(this).parent().parent().parent().parent().parent().attr('key');
			$(".tr-"+id+" input[name='joinService']:checked").each(function(){
				joinService += this.value*1;
			});
			if(joinService < 1 || joinService > 3){
				joinService = 0;
			}
			$.post("{{u('Restaurant/joinService')}}",{'joinService':joinService,'id':id},function(res){
				$.ShowAlert(res.msg);
			});
		});
	})
</script>
@stop