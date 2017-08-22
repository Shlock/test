@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.m-taborder{width: 100%}
	.ts{text-align: center;color: #999}
</style>
@stop
@section('return_link')
	<a href="{{ u('SellerGoods/index') }}" class="btn mb10 fr"><i class="fa fa-reply mr10"></i>返回服务列表</a>
@stop
@section('right_content')
	@yizan_begin
		@if($data)
		<div class="m-ordergk">
			<div class="clearfix">
				<div class="fl m-taborder">
					<table>
						<tr>
							<td width="15%">
								<p class="tc f14">
									服务名称
								</p>
							</td>
							<td width="*">
								<p class="pl20">
									<b class="f16">{{$data['name']}}</b>
								</p>
							</td>
						</tr>
						<tr>
							<td width="15%">
								<p class="tc f14">
									服务人员
								</p>
							</td>
							<td width="*">
								<p class="pl20">
									{{$data['seller']['name']}} / {{$data['seller']['mobile']}}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									价格类型
								</p>
							</td>
							<td>
								<p class="pl20">
									{{ Lang::get('admin.PriceType.'.$data['priceType']) }}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									服务价格
								</p>
							</td>
							<td>
								<p class="pl20">
									￥{{ $data['price'] }}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									门店价格
								</p>
							</td>
							<td>
								<p class="pl20 clearfix">
									￥{{ $data['marketPrice'] }}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									服务分类
								</p>
							</td>
							<td>
								<p class="pl20 clearfix">
									{{ $data['cate'] }}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									服务简介
								</p>
							</td>
							<td>
								<p class="pl20 clearfix">
									{{ $data['brief'] }}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									服务详情
								</p>
							</td>
							<td>
								<p class="pl20 clearfix">
									{!! $data['detail'] !!}
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									服务图片
								</p>
							</td>
							<td>
								<p class="tc f14 pl20 pt20 fl">
									<!-- @foreach($data['images'] as $key => $value) -->
										<a href="{{$value}}" target="_new"><img src="{{$value}}" alt="" width="100px;"></a>
									<!-- @endforeach -->
								</p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="tc f14">
									排序
								</p>
							</td>
							<td>
								<p class="pl20 clearfix">
									{{ $data['sort'] or '100' }}
								</p>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		@else
		<div class="ts">未查询到相关服务</div>
		@endif
	@yizan_end
@stop
@section('js')
@stop
