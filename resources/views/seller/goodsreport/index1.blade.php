@extends('seller._layouts.base')
@section('css')
<style>
	.search-row .f-tt{line-height:20px;}
	.m-tab table tbody td{padding: 5px 5px;}
	.deepGrey{background-color: #999 !important;color: #fff !important;}
</style>
@stop
@section('content')
<?php 
	$type = [
		'0' => '今日',
		'1' => '昨日',
		'7' => '本周',
		'30' => '本月',
	];
 ?>
    @yizan_begin
    	<div class="m-ydtt" style="margin-top:0px;">
    		<div class="x-bbtt ma">营业统计</div>
            <div class="x-bbmain ma">
            	<!-- 本日营业统计 -->
                <div class="m-yyct mt0">
                    <p class="x-bbt f-tt clearfix">
                        <span class="f16">本日营业统计</span>
                    </p>
                    <div class="m-bgct"> 
                        <yz:list>
                            <table pager="no"> 
			                    <thead>
			                        <tr>
			                            <th width="20%">
			                                订单分类
			                            </th>
			                            <th width="20%">
			                                订单数
			                            </th>
			                            <th width="20%">
			                                时长
			                            </th>
			                            <th width="20%">
			                                交易额
			                            </th>
			                            <th width="20%">
			                                实际收入
			                            </th>
			                        </tr>
			                    </thead>
			                    <tbody>
			                        <tr>
			                            <td>完成订单</td>
			                            <td>{{ $list['comfirm']['num'] }}</td>
			                            <td>{{ $list['comfirm']['duration'] }}</td>
			                            <td>{{ $list['comfirm']['trading'] }}</td>
			                            <td>{{ $list['comfirm']['total'] }}</td>
			                        </tr>
			                        <tr>
			                            <td>未完成订单</td>
			                            <td>{{ $list['unfinished']['num'] }}</td>
			                            <td>{{ $list['unfinished']['duration'] }}</td>
			                            <td>{{ $list['unfinished']['trading'] }}</td>
			                            <td>{{ $list['unfinished']['total'] }}</td>
			                        </tr>
			                        <tr>
			                            <td>总计</td>
			                            <td>{{ $list['comfirm']['num'] + $list['unfinished']['num'] }}</td>
			                            <td>{{ $list['comfirm']['duration'] + $list['unfinished']['duration'] }}</td>
			                            <td>{{ $list['comfirm']['trading'] + $list['unfinished']['trading'] }}</td>
			                            <td>{{ $list['comfirm']['total'] + $list['unfinished']['total'] }}</td>
			                        </tr>
			                    </tbody>
			                </table>
                        </yz:list>
                    </div>
                </div>
                <p class="x-bbt">
                    <span class="f16">收入统计</span>
                </p>
               @foreach($list['stat_trading'] as $item)
					{{ $item }}
				@endforeach
                <div>
					<yz:list>
						<search>  
							<row>
								<item name="beginDate" label="开始时间" type="date"></item>
                            	<item name="endDate"   label="结始时间" type="date"></item>  
								<div class="fr mr15">
									<linkbtn label="今日" css="btn-gray mr5 @if($args['type']=='0') deepGrey @endif">
										<attrs>
											<url>{{ u('Report/index',['type'=>0]) }}</url>
										</attrs>
									</linkbtn>
									<linkbtn label="昨日" css="btn-gray mr5 @if($args['type']=='1') deepGrey @endif">
										<attrs>
											<url>{{ u('Report/index',['type'=>1]) }}</url>
										</attrs>
									</linkbtn>
									<linkbtn label="本周" css="btn-gray mr5 @if($args['type']=='7') deepGrey @endif">
										<attrs>
											<url>{{ u('Report/index',['type'=>'7']) }}</url>
										</attrs>
									</linkbtn>
									<linkbtn label="本月" css="btn-gray mr5 @if($args['type']=='30') deepGrey @endif">
										<attrs>
											<url>{{ u('Report/index',['type'=>'30']) }}</url>
										</attrs>
									</linkbtn>
								</div>
								 <btn type="search" css="btn-gray" label="查询"></btn>
							</row>
						</search>
					</yz:list> 
                </div>
                <div class="x-srt">
                	<div class="x-srtl"><span></span>收入统计</div>
                    <div class="x-srtr">
                    	{{$type[$args['type']]}}收入：<strong>{{ array_sum($data['stat_total']) }}元</strong>
                    </div>
                </div>
                <div class="x-srb ma">
                	<div id="container" style="width:100%;height:400px"></div>
                </div>
            </div>
    	</div>
    @yizan_end
@stop

@section('js')
<script>
	Highcharts.setOptions({
	lang: { 
		downloadJPEG: '导出为JPE图片选项对于的文字',
		downloadPDF: '导出为PDF文件选项显示的文字',
		downloadPNG: '导出为PNG图片选项显示的文字', 
		downloadSVG: '导出为SVG文件选项显示的文字', 
		printChart:'打印图表'
		}
	});
	$(function () { 
	    $('#container').highcharts({
	        title: {
	            text: '人员收入统计',
	            x: -20 //center
	        },
	        subtitle: {
	            text: '收入统计曲线图',
	            x: -20
	        },
	        xAxis: { 
	            categories: {!! json_encode($data['stat_x']) !!}
	        },
	        yAxis: {
	            title: {
	                text: '曲线走势图'
	            },
	            plotLines: [{
	                value: 0,
	                width: 1,
	                color: '#808080'
	            }]
	        },
	        tooltip: {
	            valueSuffix: ''
	        },
	        legend: {
	            layout: 'vertical',
	            align: 'right',
	            verticalAlign: 'middle',
	            borderWidth: 0
	        },
	        series: [
	        	{	name: '订单数量(份)',  
	            	data: {{ json_encode($data['stat_num']) }}
		        }, 
		        {	name: '订单实际交易额(元)', 
        			data: {{ json_encode($data['stat_total']) }}
		        }, 
		        {
		        	name: '订单账面交易额(元)', 
        			data: {{ json_encode($data['stat_trading']) }}
		        }
	        ]
	    });

		$('#yzForm').submit(function(){
            var beginTime = $("#beginDate").val();
            var endTime = $("#endDate").val();
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
});
</script>
@stop 