@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.hscolor{color: #ccc}
</style>
@stop
<?php 
$actType = [
        ['id'=>'0','name'=>'全部'],
        ['id'=>'1','name'=>'分享活动'],
        ['id'=>'2','name'=>'注册活动'],
        ['id'=>'3','name'=>'线下发放'],
];
$status = [
	['id'=>'0','name'=>'全部'],
	['id'=>'1','name'=>'未兑换'],
	['id'=>'2','name'=>'未使用'],
    ['id'=>'3','name'=>'已使用'],
    ['id'=>'4','name'=>'已过期']
];
 ?>
@section('right_content')
	@yizan_begin
		<yz:list>
			<search> 
				<row>
                    <item label="发放时间">
                        <input type="text" name="beginTime" id="beginTime" class="date u-ipttext" style="width: 100px;" value="{{ $search_args['beginTime'] }}"> -
                        <input type="text" name="endTime" id="endTime" class="date u-ipttext" style="width: 100px;" value="{{ $search_args['endTime'] }}">
                    </item>
					<item name="sn" label="序列号"></item>
                    <item label="活动类型">
                        <yz:select name="actType" options="$actType" textfield="name" valuefield="id" attr="style='min-width:100px;width:auto'" selected="$search_args['actType']">
                        </yz:select>
                    </item>
				</row>
                <row>
                    <item name="actName" label="活动名称"></item>
                    <item name="mobile" label="会员手机号"></item>
                    <item label="状态">
                        <yz:select name="status" options="$status" textfield="name" valuefield="id" attr="style='min-width:100px;width:auto'" selected="$search_args['status']">
                        </yz:select>
                    </item>
                    <btn type="search"></btn>
                </row>
			</search>
            <btns>
                <linkbtn label="删除" type="destroy"></linkbtn>
            </btns>
			<table checkbox="1">
				<columns>
                    <column code="id" label="编号" width="30"></column>
					<column label="优惠券" align="left" width="90">
						<p>{{ $list_item['promotion']['name'] }}</p>
						<p>{{ $list_item['sn'] }}</p>
                        <p>{{ $list_item['money'] }}元</p>
					</column>
					<column label="活动名称" align="left">
						{{ $list_item['activity']['name'] }}
					</column>
					<column label="发放时间" align="left" type="time" code="createTime"></column>
                    <column label="有效期" align="left" width="80">
                        {{ yztime($list_item['beginTime'],'Y-m-d H:i') }} - {{ yztime($list_item['expireTime'],'Y-m-d H:i') }}
                    </column>
                    <column label="兑换时间" align="left" type="time" code="sendTime"></column>
                    <column label="会员手机号" align="left" width="80">{{ $list_item['user']['mobile'] }}</column>
					<column label="状态" code="statusStr"></column>
					<actions>
						<action type="destroy"></action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop
@section('js')

@stop