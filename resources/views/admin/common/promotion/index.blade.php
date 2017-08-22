@extends('admin._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop
<?php
$useType = [
        ['id'=>0,'name'=>'请选择'],
        ['id'=>1,'name'=>'无限制'],
        ['id'=>2,'name'=>'指定分类'],
        ['id'=>3,'name'=>'指定商家']
];
 ?>
@section('right_content')
	@yizan_begin
		<yz:list>
			<search> 
				<row>
					<item name="name" label="优惠名称" ></item>
                    <item label="创建时间">
                        <input type="text" name="beginTime" id="beginTime" class="date u-ipttext" style="width: 150px;" value="{{ $search_args['beginTime'] }}"> -
                        <input type="text" name="endTime" id="endTime" class="date u-ipttext" style="width: 150px;" value="{{ $search_args['endTime'] }}">
                    </item>
				</row>
                <row>
                    <item name="money" label="面额"></item>
                    <item label="类型">
                        <yz:select name="useType" label="类型" options="$useType" valuefield="id" textfield="name" selected="$search_args['useType']"></yz:select>
                    </item>
                    <item name="sellerName" label="商户名称" ></item>
                    <btn type="search"></btn>
                </row>
			</search>
			<btns>
				<linkbtn label="添加优惠券" type="add"></linkbtn>
				
			</btns>
			<table>
				<columns>
					<column label="优惠券名称" align="center" code="name"></column>
                    <column label="面额" align="center" code="money" width="50">{{  $list_item['money'] }}元</column>
                    <column label="创建时间" align="center" code="createTime" type="time"></column>
                    <column label="有效期" align="center" code="ableDateTime"></column>
                    <column label="类型" align="center" code="useTypeStr"></column>
					<actions width="50">
						@if($list_item['sendType']==null)
						<p>
							<action label="发放" css="blu">
								<attrs>
									<url>{{ u('Promotion/sendsn',['id'=>$list_item['id']]) }}</url>
								</attrs>
							</action>
						</p>
						@endif
						<p>
							<action label="列表" css="blu">
								<attrs>
									<url>
									{{ u('PromotionSn/index',['promotionId'=>$list_item['id'],'promotionName'=>$list_item['name'],]) }}
									</url>
									<target>_new</target>
								</attrs>
							</action>
						</p>
						<p>
							<action label="编辑" css="blu" type="edit"></action>
						</p>
						@if($list_item['activityCount'] == 0 && $list_item['promotionSnCount'] == 0)
						<p><action type="destroy"  css="red"></action></p>
						@endif
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		$('#yzForm').submit(function(){
            var beginTime = $("#beginTime").val();
            var endTime = $("#endTime").val();
            if(beginTime!='' || endTime!='') {
                if(beginTime==''){
                    alert("开始时间不能为空");return false;
                }
                else if(endTime==''){
                    alert("结束时间不能为空");return false;
                }
                else if(endTime < beginTime){
                    alert("结束时间不能大于开始时间");return false;
                }
            }
        });

		$('#cate_id').prepend("<option value='0' selected>全部分类</option>");
	});
</script>
@stop

