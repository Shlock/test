@extends('seller._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop
@section('content')
	@yizan_begin
		<yz:list>
			<search>  
				<row>
					<item name="beginDate" label="开始时间" type="date"></item>
					<item name="endDate" label="结束时间" type="date"></item>
					<linkbtn label="周" css="btn-gray fr mr5"></linkbtn>
					<linkbtn label="月" css="btn-gray fr mr5"></linkbtn>
					<linkbtn label="年" css="btn-gray fr mr5"></linkbtn>
				</row>
			</search>
		</yz:list>
		<div id="container" style="min-width:700px;height:400px"></div>
	@yizan_end

@stop
@section('js')
<script>
	$(function () {
	    $('#container').highcharts({
	        title: {
	            text: '这里是标题',
	            x: -20 //center
	        },
	        subtitle: {
	            text: '这里是副标题',
	            x: -20
	        },
	        xAxis: {
	            categories: ['一月', '二月', '三月', '四月', '五月', '六月','七月', '八月', '九月', '十月', '十一月', '十二月']
	        },
	        yAxis: {
	            title: {
	                text: 'Y轴名称'
	            },
	            plotLines: [{
	                value: 0,
	                width: 1,
	                color: '#808080'
	            }]
	        },
	        tooltip: {
	            valueSuffix: '°C'
	        },
	        legend: {
	            layout: 'vertical',
	            align: 'right',
	            verticalAlign: 'middle',
	            borderWidth: 0
	        },
	        series: [{
	            name: '第一条线',
	            data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
	        }, {
	            name: '第二条线',
	            data: [-0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
	        }, {
	            name: '第三条线',
	            data: [-0.9, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0]
	        }, {
	            name: '第四条线',
	            data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
	        }]
	    });
	});
</script>
@stop
