@extends('admin._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:list> 
		<search >
			<row>
				<div id="name-form-item" class="u-fitem clearfix ">
		            <span class="f-tt">
		                物业公司:
		            </span>
		            <div class="f-boxr">
		                {{$seller['name']}}
		                <input type="hidden" name="sellerId" value="{{$seller['id']}}" >
		            </div>
		        </div>
				<div id="name-form-item" class="u-fitem clearfix ">
		            <span class="f-tt">
		                小区名称:
		            </span>
		            <div class="f-boxr">
		               	{{$seller['district']['name']}}
		                <input type="hidden" name="directId" value="{{$seller['district']['id']}}" >
		            </div>
		        </div>
				<item name="name" label="门禁名称"></item> 
                <item name="userName" label="姓名"></item>   
				<item name="beginTime" label="开始时间" type="date"></item>
	            <item name="endTime" label="结束时间" type="date"></item>
				<btn type="search"></btn>
			</row>
		</search>
		<btns> 
			<linkbtn label="导出到EXCEL" >
				<attrs>
					<url>{{ u('Property/dooropenlogexport', ['sellerId'=>$seller['id'],'districtId'=>$seller['district']['id']]) }}</url>
				</attrs>
			</linkbtn>
		</btns>
		<table >
			<columns> 
				<column code="id" label="编号"  ></column>  
				<column label="门禁名称"  >
					{{$list_item['door']['name']}}
				</column>   
				<column label="房间" align="center" >
					{{$list_item['room']['owner']}}
				</column> 
				<column label="业主姓名" align="center" >
					{{$list_item['puser']['name']}}
				</column> 
				<column code="contacts" label="联系人"  >
					{{$list_item['puser']['mobile']}}
				</column>  
				<column label="开门时间" >
					{{yztime($list_item['createTime'])}}
				</column>  
			</columns>
		</table>
	</yz:list>
	@yizan_end
	<script type="text/javascript">
	$(function(){ 

	})
	</script>
@stop