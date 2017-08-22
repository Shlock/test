@extends('admin._layouts.base')

@section('right_content')
	@yizan_begin
	<yz:list>
		<search>  
			<row>
				<item name="beginTime" label="开始时间" type="date"></item>
				<item name="endTime" label="结束时间" type="date"></item>
				<btn type="search"></btn> 
			</row> 
		</search>  
	</yz:list> 
	
	<div class="m-tab" style="background: #f3f6fa;">
		<!-- 报表tip -->
		<div class="m-bbtipct clearfix">
			
			<div class="m-btb fr">
				<table>
					<thead>
						<th width="50%">今日总订单数</th>
						<th width="50%">历史总订单数</th>
					</thead>
					<tbody>
						<tr>
							<td>{{$data['todayNum']}}</td>
							<td>{{$data['totalNum']}}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="m-biaoqy mt10" style="padding-bottom: 10px;padding-right:10px;">
			
		</div>
	</div>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
    $(function () {
        $('.m-biaoqy').highcharts({
            title: {
                text: '订单统计数据图表',
                x: -20 //center
            },
            xAxis: {
                categories: [@foreach($data['time'] as $val)'{{$val}}',@endforeach]
            },
            yAxis: {
				min:0,
                title: {
                    text: ''
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },

            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [
            @foreach($data['data'] as $val)
            {
                name: '{{$val['name']}}',
                data: [@foreach($val['val'] as $vo){{$vo}},@endforeach],
            },
            @endforeach
            ]
        });
    });
</script>
@stop
