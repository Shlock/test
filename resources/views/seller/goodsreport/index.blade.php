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
								<url>{{ u('GoodsReport/index',['type'=>0]) }}</url>
							</attrs>
						</linkbtn>
						<linkbtn label="近七天" css="btn-gray mr5 @if($args['type']=='7') deepGrey @endif">
							<attrs>
								<url>{{ u('GoodsReport/index',['type'=>7]) }}</url>
							</attrs>
						</linkbtn>
						<linkbtn label="近30天" css="btn-gray mr5 @if($args['type']=='30') deepGrey @endif">
							<attrs>
								<url>{{ u('GoodsReport/index',['type'=>'30']) }}</url>
							</attrs>
						</linkbtn>
						<item name="beginDate" label="开始时间" type="date"></item>
						<item name="endDate" label="结束时间" type="date"></item>
						<btn type="search" css="btn-gray" label="查询"></btn>
					</row>
				</search>
				

				<dl class="m-ddl">
    				<dt>详细数据</dt>
        			<dd class="clearfix">
        				<table id="checkListTable">
        				    <thead>
                        		<tr>
                        		  <td>商品</td>
                        		  <td>单价</td>
                                    @if($args['numOrder'] == 0 || $args['numOrder'] ==2)
                                        <td><a href="{{ u('GoodsReport/index',['type'=>$args['type'],'numOrder'=>1]) }}">销量<i class="fa fa-long-arrow-down"></i> <i class="fa fa-long-arrow-up"></i></a></td>
                                    @else
                                        <td><a href="{{ u('GoodsReport/index',['type'=>$args['type'],'numOrder'=>2]) }}">销量<i class="fa fa-long-arrow-down"></i> <i class="fa fa-long-arrow-up"></i></a></td>
                                    @endif

                                    @if($args['priceOrder'] == 0 || $args['priceOrder'] ==2)
                                        <td><a href="{{ u('GoodsReport/index',['type'=>$args['type'],'priceOrder'=>1]) }}">销售额<i class="fa fa-long-arrow-down"></i> <i class="fa fa-long-arrow-up"></i></a></td>
                                    @else
                                        <td><a href="{{ u('GoodsReport/index',['type'=>$args['type'],'priceOrder'=>2]) }}">销售额<i class="fa fa-long-arrow-down"></i> <i class="fa fa-long-arrow-up"></i></a></td>
                                    @endif
                        		</tr>
                    		</thead>
                    		<tbody>
                            @foreach ($data['list'] as $key=>$val)
                                <tr>
                                    <td>{{ $val['goodsName'] }}</td>
                                    <td>{{ $val['price'] }}</td>
                                    <td>{{ $val['num'] }}</td>
                                    <td>{{ $val['totleprice'] }}</td>
                                </tr>
                            @endforeach
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
	$(function () {
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