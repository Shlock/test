@extends('admin._layouts.base')
@section('css')

@stop
@section('right_content')
@yizan_begin 
		<yz:list>
		<search> 
			<row>
				<item name="sellerName" 	label="服务人员名称"></item>  
				<item name="beginTime" label="开始时间" type="date"></item>
			</row>
			<row>
				<item name="sellerMobile" label="服务人员手机"></item>
				<item name="endTime" label="结束时间" type="date"></item>
				<btn type="search"></btn>
			</row>
		</search> 
		<table>
		<columns>	
			<column code="sn" 	   label="流水号" width="160"></column>
			@yizan_yield('staff')
			<column code="seller"  label="基本信息" align="left" >
				<p>服&nbsp;务&nbsp;人&nbsp;员：{{$list_item['seller']['name']}}</p>
				<p>金&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;额：{{$list_item['money']}}</p>
				<p>变动时余额：{{$list_item['balance']}}</p>
			</column> 
			@yizan_stop
			<column code="content" label="资金流动信息" width="200" align="left" ></column> 
			<column code="createTime" label="创建时间" type="time"></column> 
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
    });
</script>
@stop 