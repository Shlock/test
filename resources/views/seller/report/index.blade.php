@extends('seller._layouts.base')
@section('css')
<style>
	.search-row .f-tt{line-height:20px;}
	.m-tab table tbody td{padding: 5px 5px;}
	.deepGrey{background-color: #999 !important;color: #fff !important;}
	.search-row .u-ipttext {width: 110px;padding-right: 5px;}
    .x-bbmain .m-ddl li {float: left;height: 23px;line-height: 23px;border: 1px solid #ccc;background: #fff;margin-right: 10px;border-radius: 3px;margin-top: 5px;}
    .x-bbmain .m-ddl li.on {background: #e4e4e4;}
    .x-bbmain .m-ddl li a { color: #555; display: block; padding: 0 15px;}
    .x-bbmain .rev div{float:left; width:33%; margin:auto; text-align:center; border-right:1px solid #d5d5d5;}
</style>
@stop
@section('content')
<?php 
	$type = [
		'0' => '今天',
		'7' => '近七天',
		'30' => '近30天',
	];
 ?>
    @yizan_begin
        <yz:list>
    	<div class="m-ydtt" style="margin-top:0px;">
    		<div class="x-bbtt ma">营业统计</div>
            <div class="x-bbmain ma">
				<search>  
					<row>
						<linkbtn label="今天" css="btn-gray mr5 @if($args['type']=='0') deepGrey @endif">
							<attrs>
								<url>{{ u('Report/index',['type'=>0]) }}</url>
							</attrs>
						</linkbtn>
						<linkbtn label="近七天" css="btn-gray mr5 @if($args['type']=='7') deepGrey @endif">
							<attrs>
								<url>{{ u('Report/index',['type'=>7]) }}</url>
							</attrs>
						</linkbtn>
						<linkbtn label="近30天" css="btn-gray mr5 @if($args['type']=='30') deepGrey @endif">
							<attrs>
								<url>{{ u('Report/index',['type'=>'30']) }}</url>
							</attrs>
						</linkbtn>
						<item name="beginDate" label="开始时间" type="date"></item>
						<item name="endDate" label="结束时间" type="date"></item>
						<btn type="search" css="btn-gray" label="查询"></btn>
					</row>
				</search>
				
				<dl class="m-ddl">
    				<dt>营业总览</dt>
        			<dd class="clearfix rev">
        				<div><h1>{{ $data['total'] or 0 }}</h1><span>营业额</span></div>
        				<div><h1>{{ $data['num'] or 0 }}</h1><span>有效订单数</span></div>
        				<div style="border-right:none;"><h1>{{ $data['price'] }}</h1><span>客单价</span></div>
        			</dd>
				</dl>

				<dl class="m-ddl" @if($args['type']=='0' || $args['rs'] == 0) style="display: none;" @endif>
    				<dt>营业趋势图</dt>
        			<dd class="clearfix">
        				<div class="x-srb ma">
                        	<div id="container" style="width:100%;height:400px"></div>
                        	<!-- <ul class="fr clearfix">
                                <li class="on  wobble-top">
                                    <a href="#">营业额</a>
                                </li>
                                <li class="wobble-top">
                                    <a href="#">有效订单数</a>
                                </li>
                                <li class="wobble-top">
                                    <a href="#">客单价</a>
                                </li>
                            </ul> -->
                        </div>
        			</dd>
				</dl>

				<dl class="m-ddl">
    				<dt>详细数据</dt>
        			<dd class="clearfix">
        				<table id="checkListTable">
        				    <thead>
                        		<tr>
                        		  <td>日期</td>
                        		  <td>营业额</td>
                        		  <td>有效订单数</td>
                        		  <td>客单价</td>
                        		</tr>
                    		</thead>
                    		<tbody>
                    		  @if($data['list'])
                    		  @foreach ($data['list'] as $key=>$val)
                    		  <tr>
                    		      <td>{{ $key }}</td>
                    		      <td>{{ $val['total'] }}</td>
                    		      <td>{{ $val['num'] or 0}}</td>
                    		      <td>{{ $val['price'] }}</td>
                    		  </tr>
                    		  @endforeach
                    		  @else
                    		  <tr>
                    		      <td>{{ $date }}</td>
                    		      <td>{{ $data['total'] }}</td>
                    		      <td>{{ $data['num'] or 0}}</td>
                    		      <td>{{ $data['price'] }}</td>
                    		  </tr>
                    		  @endif
                    		</tbody>
                    	</table>
        			</dd>
				</dl>
            </div>
    	</div>
    	</yz:list>
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
	            text: '曲线走势图',
	            x: -20 //center
	        },
	        subtitle: {
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
	        	{	name: '有效订单数量(份)',  
	            	data: {{ json_encode($data['stat_num']) }}
		        }, 
		        {	name: '营业额(元)', 
        			data: {{ json_encode($data['stat_total']) }}
		        }, 
		        {
		        	name: '客单价(元)', 
        			data: {{ json_encode($data['stat_price']) }}
		        }
	        ]
	    });

		$('#yzForm').submit(function(){
            var beginTime = $("#beginDate").val();
            var endTime = $("#endDate").val();

            var timestamp2 = Date.parse(new Date(beginTime));
            timestamp2 = timestamp2 / 1000;

            var timestamp = Date.parse(new Date(endTime));
            timestamp = timestamp / 1000;

            if(timestamp-timestamp2 >= 90*86400){
                alert("时间大于90天");return false;
            }

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