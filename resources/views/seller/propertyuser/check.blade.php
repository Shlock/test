@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
<div>
<div class="m-zjgltbg">					
	<div class="p10">
		<div class="g-fwgl">
			<p class="f-bhtt f14 clearfix">
				<span class="ml15 fl">门禁查看</span>
			</p>
		</div>
		<div class="m-tab m-smfw-ser">
			@yizan_begin
			<yz:list>
				<table>
					<search> 
						<row>
							<item name="name" label="业主名称">
								<p>{{ $data['name'] }}</p>
							</item>  
							<item name="build" label="楼栋号">
								<p>{{ $data['build']['name'] }}</p>
							</item> 
							<item name="roomNum" label="房间号">
								<p>{{ $data['room']['roomNum'] }}</p>
							</item> 
						</row>
					</search>
					<btns>
						<linkbtn type="add" css="btn-gray">
							<attrs>
								<url>{{ u('PropertyUser/edit',['puserId'=>$args['puserId']]) }}</url>
							</attrs>
						</linkbtn>
					</btns>
					<columns>
						<column code="door" label="门禁号">
							<p>{{ $list_item['door']['name'] }}</p>
						</column>
						<column code="endTime" label="门禁截止时间">
							{{ yzday($list_item['endTime']) }}
						</column>
						<actions width="100">
							<action label="续时" >
								<attrs>
									<url>{{ u('PropertyUser/edit',['puserId'=>$args['puserId'], 'id'=> $list_item['id']]) }}</url>
								</attrs>
							</action>
							<!-- <action label="续时" click="ContinueTimes( {{ $list_item['id'] }},{{ $list_item['door']['id']}} )" css="blu"></action> -->
							<action label="删除" css="red">
								<attrs>
									<click>$.RemoveItem(this, '{!!u('PropertyUser/destroydoor',['puserId'=>$args['puserId'], 'id'=>$list_item['id']])!!}', '你确定要删除该门禁吗？');</click>
								</attrs>
							</action>
						</actions>
					</columns>
				</table>
			</yz:list>
			@yizan_end
		</div>
		</div>
	</div>
</div>
@stop

@section('js')
<script type="text/tpl" id="ContinueTimes">
<div style="width:500px;text-align:center;margin:15px 0"> 
	<span class="msgs"style="display: block;text-align:left;margin:5px"></span>
	<div class="f-boxr">
		<span>截止日期：</span>
	    <input type="text" name="deadLine" id="deadLine" class="date u-ipttext" onclick="$(function(){$('.date').datepicker();})">
	</div>
</div>
</script>  
<script type="text/javascript"> 
//提现处理
function ContinueTimes(id, doorId) {  
    var dialog = $.zydialogs.open($("#ContinueTimes").html(), {
        boxid:'SET_GROUP_WEEBOX',
        width:300,
        title:'续时',
        showClose:true,
        showButton:true,
        showOk:true,
        showCancel:true,
        okBtnName: '确定',
		cancelBtnName: '取消',
        contentType:'endTime',
        onOk: function(){
            var query = new Object();
            query.id = id;
            query.doorId = doorId;
            query.puserId = "{{$args['puserId']}}";
            query.endTime = $("#deadLine").val(); 
            if(query.endTime == ""){
            	$.ShowAlert("截止时间不能为空");
            }else{
	            dialog.setLoading();
            	$.post("{{ u('PropertyUser/save')  }}",query,function(result){  
                	dialog.setLoading(false);  
                	if(result.status == true){ 
                   	 	window.location.reload();
                	}else{
                    	$.ShowAlert(result.msg);
                    	$.zydialogs.close("SET_GROUP_WEEBOX");
                	}
	            },'json');
            }
        }, 
		onCancel:function(){
            $.zydialogs.close("SET_GROUP_WEEBOX");
        }  
	});
}  
</script>
@stop