@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	#checkListTable{table-layout:fixed;}
	.hoverred{color: #000}
	.hoverred:hover{color: red}
</style>
@stop
<?php
 $result = [
 	['id'=>'','name'=>'全部'],
 	['id'=>'good','name'=>'好评'],
 	['id'=>'neutral','name'=>'中评'],
 	['id'=>'bad','name'=>'差评'],
 ];
  $replyStatus = [
 	['id'=>'0','name'=>'全部'],
 	['id'=>'1','name'=>'未回复'],
 	['id'=>'2','name'=>'已回复'],
 ];
 ?>
@section('right_content')
	@yizan_begin
		<yz:list>
			<search>
				<row>
					<item name="userMobile" label="会员信息"></item>
					@yizan_yield("search_sellerMobile")
					<item name="sellerMobile" label="机构信息"></item>
					@yizan_stop
					<item label="评价结果">
						<yz:select name="result" options="$result" textfield="name" valuefield="id" selected="$search_args['result']">
						</yz:select>
					</item>
				</row>
				<row>
					<item name="orderSn" label="订单号"></item>
					@yizan_yield("search_staffMobile")
					<item name="staffMobile" label="服务员工"></item>
					@yizan_stop
					<item label="是否回复">
						<yz:select name="replyStatus" options="$replyStatus" textfield="name" valuefield="id" selected="$search_args['replyStatus']">
						</yz:select>
					</item>
					<btn type="search"></btn>
				</row>
			</search>
			<table>
				<rows>
					<headers>
						<header code="user" label="会员" width="100"></header>
						@yizan_yield("seller")
						<header code="seller" label="商家" width="100"></header>
						@yizan_stop
						@yizan_yield("staff")
						<header code="staff" label="员工" width="100"></header>
						@yizan_stop
						<header label="评分" width="100"></header>
						<header code="createTime" label="点评时间" width="120"></header>
					</headers>
					<row>

						<tr class="{{ $list_item_css }}">

							<td style="text-align:left">
								<p>{{ $list_item['user']['name'] or '-' }}</p>
								<p>{{ $list_item['user']['mobile'] or '-' }}</p>
							</td>

							@yizan_yield("seller_td")
							<td style="text-align:left">
								<p>{{ $list_item['seller']['name'] or '-' }}</p>
								<p>{{ $list_item['seller']['mobile'] or '-' }}</p>
							</td>
							@yizan_stop
							@yizan_yield("staff_td")
							<td style="text-align:left">
								<p>{{ $list_item['staff']['name'] or '-' }}</p>
								<p>{{ $list_item['staff']['mobile'] or '-' }}</p>
							</td>
							@yizan_stop
							<td>
								<span>{{$list_item['star']}}</span>
							</td>
							<td>
								{{yztime($list_item['createTime'])}}
							</td>
							<td rowspan="2">
								<actions> 
									<p>
										<action label="编辑/回复" css="blu">
											<attrs>
												<url>{{ u('OrderRate/detail', ['id'=>$list_item['id']]) }}</url>
											</attrs>
										</action>
									</p>
									<p>
										<action label="订单" style="color:#000" target="_new">
											<attrs>
												<url>{{ u('Order/detail',['id'=> $list_item['orderId']]) }}</url>
											</attrs>
										</action>
									</p>
									<p><action type="destroy" css="red"></action></p>
								</actions>
							</td>
						</tr>
						<tr class="{{ $list_item_css }}">
							<td colspan="2" style="text-align:left;vertical-align:top;word-wrap:break-word">
								<p>评价：{{ $list_item['content'] }}</p>
								<!--@foreach($list_item['images'] as $image)-->
								<a href="{{ $image }}" target="_blank"><img src="{{ $image }}" alt="" height="40"></a>
								<!-- @endforeach -->
							</td>
							@yizan_yield("colspan")
							<td colspan="3" style="text-align:left;vertical-align:top;word-wrap:break-word">
							@yizan_stop
                                <p>回复时间：{{Time::toDate($list_item['replyTime'], 'Y-m-d H:i:s')}}</p>
								回复：{{ $list_item['reply'] }}
							</td>
						</tr>
                        <tr class="{{ $list_item_css }}">
                            <td colspan="6" style="text-align:left;vertical-align:top;word-wrap:break-word">
                                订单编号：{{ $list_item['order']['sn']}}
                            </td>
                        </tr>
					</row>
				</rows>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/tpl" id="replyTpl">
<textarea id="replyContent" style="width:400px;height:100px;border:1px #ccc solid"></textarea>
</script>
<script type="text/javascript">
	//function reply(url) {
		//window.location.href = url;
		// $.ShowConfirm($("#replyTpl").html(), function(){
		// 	var content = $("#replyContent").val();
		// 	if(content.length > 0) {
		// 		$.post("{{ u('OrderRate/rateReply') }}",{"id":id,"content":content},function(res){
		// 			if(res.status==true){
		// 				window.location.reload();
		// 			}else{
		// 				$.ShowAlert(res.msg);
		// 			}
		// 		},'json')
		// 	}else{
		// 		$.ShowAlert("请填写回复内容");
		// 	}
		// },null,'评价回复');
	//}
</script>
@stop
