@extends('admin._layouts.base')

@section('right_content')
	@yizan_begin
	<php> 
		$navs = ['nav1','nav2','nav3','nav4'];
		$nav = in_array(Input::get('nav'),$navs) ? Input::get('nav') : 'nav1' ; 
		$$nav = "on";
	</php>
	<yz:list>
			<tabs>
				<navs>
					<nav label="业绩排行榜">
						<attrs>
							<url>{{ u('Performance/index',['nav'=>'nav1']) }}</url>
							<css>{{$nav1}}</css>
						</attrs>
					</nav>
					<nav label="抽成排行榜">
						<attrs>
							<url>{{ u('Performance/bonus',['nav'=>'nav2']) }}</url>
							<css>{{$nav2}}</css>
						</attrs>
					</nav>
					<nav label="卖家业绩查看">
						<attrs>
							<url>{{ u('Performance/sellerperformance',['nav'=>'nav3']) }}</url>
							<css>{{$nav3}}</css> 
						</attrs>
					</nav>
					<nav label="员工业绩查看">
						<attrs>
							<url>{{ u('Performance/staffperformance',['nav'=>'nav4']) }}</url>
							<css>{{$nav4}}</css>
						</attrs>
					</nav>
				</navs>
			</tabs>
			
			
				<search>
				<row>
					<item name="staff" label="员工信息"></item>
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
						<th width="50%">今日营业额</th>
						<th width="50%">历史营业额</th>
					</thead>
					<tbody>
						<tr>
							<td>{{$data['sum']}}</td>
							<td>{{$data['allSum']}}</td>
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
                text: '卖家统计图表',
                x: -20 //center
            },
            xAxis: {
                categories: [@foreach($data['list'] as $val)'{{Time::toDate($val["appoint_day"],"Y-m-d")}}',@endforeach]
            },
            yAxis: {
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
            {
                name: '营业额',
                data: [@foreach($data['list'] as $vo){{$vo['totalPay']}},@endforeach],
            },
            {
                name: '抽成',
                data: [@foreach($data['list'] as $vo){{$vo['totalService']}},@endforeach],
            },
            ]
        });
    });
</script>
@stop
