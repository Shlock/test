@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.m-tab table tbody td{padding: 5px 0px;}
</style>
@stop
@section('content')
<?php 
    $plan = [
        ['id'=>-2,'name'=>'全部'],
        ['id'=>2,'name'=>'等待审核'],
        ['id'=>1,'name'=>'审核失败'],
    ];
 ?>
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<div class="g-fwgl mb10">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">服务审核</span>
					</p>
				</div>
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
	                    	<search>
		                        <row>
		                            <yz:select name="status" options="$plan" textfield="name" valuefield="id" selected="$search_args['status']"></yz:select>
		                            <item name="name" label="服务名称"></item>
		                            <btn type="search" css="btn-gray"></btn>
		                        </row>
		                    </search>
	                        <table css="goodstable">
	                            <columns>
	                                <column label="服务名称" align="left">
	                                	<a href="{{ $list_item['image'] }}" target="_blank" class="goodstable_img fl">
	                                		<img src="{{$list_item['image']}}" alt="" width="70px">
	                                	</a>
	                                	<div class="goods_name">{{ $list_item['name'] }}</div>
	                                </column>
	                                <column label="服务分类">
	                                	{{ $list_item['cate']['id'] ? $cate[$list_item['cate']['id']]['levelrel'] : '--分类不存在--' }}
	                                </column>
	                                <column code="disposeResult" label="审核结果"></column>
	                                <column label="审核进度">
	                                	@if($list_item['status']==-1)
											<span style="color:red">未通过</span>
	                                	@elseif($list_item['status']==0)
											<span style="color:#999">未处理</span>
	                                	@endif
	                                </column>
	                                 <actions>
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
