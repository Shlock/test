@extends('admin._layouts.base')

@section('css')

@section('right_content')
<?php 
$responsible = [
	['id'=>1,'name'=>'负责小区人员'],
	['id'=>2,'name'=>'未负责小区人员']
];
 ?>
	@yizan_begin
		<yz:list>
			<search>
				<row>
					<item name="name" label="员工姓名"></item>
					<item name="mobile" label="员工电话"></item>
					<item label="所属小区">
						<yz:select name="responsible" options="$responsible" textfield="name" valuefield="id" selected="$search_args['responsible'] ? $search_args['responsible'] : 2">
						</yz:select>
					</item>
					<item name="districtId" type="hidden"></item>
					<btn type="search"></btn>
				</row>
			</search>
			<table>
				<columns>
					<column code="avatar" label="头像" type="image" width="80" iscut="1"></column>
					<column label="员工信息" align="left">
						<p>名称：{{$list_item['name']}}</p>
						<p>电话：{{$list_item['mobile']}}</p>
						<p>地址：{{$list_item['address']}}</p>
					</column>
					<column label="所属小区" css="cellName">
						{{$list_item['districtName'] or '无'}}
					</column>
					<actions width="100">
						@if($responsible_search == 1)
						<action label="更换小区" css="blu" click="$.changeResponsible({{ $list_item['id'] }},'{{ $list_item['districtName'] }}')"></action>
						@elseif($responsible_search == 2)
						<action label="添加" css="blu" click="$.addresponsible({{$list_item['id']}})"></action>
						@else
						@endif
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/tpl" id="changeResponsible"> 
	<div style="width:500px; height:auto;text-align:center;margin:10px 0 " id="searchResponsible">
		<input type="text" value="" placeholder="请输入小区名称" class="u-ipttext keywords" style="width:380px;">
		<button onclick="$.searchResponsible()" class="btn mr5">搜索</button>
	</div>
	<div class="m-ssxqlst">
		<ul id="searchResult" >
			<center style='color:#999'>输入小区名称或者地址可以搜索哦~</center>
		</ul>
	</div>
</script> 
<script type="text/javascript">
var districtId = "{{$districtId}}";
	$(function(){
		//添加小区
		$.addresponsible = function(staffId){
			$.post("{{ u('District/addresponsible') }}",{'districtId':districtId,'staffId':staffId},function(res){
				$.ShowAlert(res.msg);
				if(res.code == 0){
					window.location.reload();
				}
			})
		}

		$.changeResponsible = function(staffId, districtName) {
			var dialog = $.zydialogs.open($("#changeResponsible").html(), {
		        boxid:'SET_GROUP_WEEBOX',
		        width:300,
		        title:'所属小区：'+districtName,
		        showClose:true,
		        showButton:true,
		        showOk:true,
		        showCancel:false,
		        okBtnName: '确定更换',
				cancelBtnName: '取消',
		        contentType:'content',
		        onOk: function(){
		            var query = new Object();
		            query.staffId = staffId;
		            query.districtId_old = districtId;
		            query.districtId_new = $('ul#searchResult li.on').find('span').data('id');
		            if(query.districtId_new == ""){
		            	$.ShowAlert("请选择小区");
		            }else{
			            dialog.setLoading();
			            	$.post("{{ u('District/changeStaffDistrict') }}",query,function(result){ 
			                	dialog.setLoading(false);  
			                	if(result.code == 0){
			                   	 	window.location.reload();
			                	}else{
			                    	$.ShowAlert(result.msg);
			                    $.zydialogs.close("SET_GROUP_WEEBOX");
			                }
			            },'json');
		            }
		        },
		        onCancel:function(){ 
		        	var query = new Object();
		            query.id = id;
		            query.status = 3;  
		            query.content = $("#disposeRemark").val(); 
		            if(query.content == ""){
		            	$.ShowAlert("请输入举报处理内容！");
		            }else{
			            dialog.setLoading();
			            	$.post("{{ u('OrderComplain/dispose') }}",query,function(result){ 
			                	dialog.setLoading(false);  
			                	if(result.code == 0){
			                   	 	window.location.reload();
			                	}else{
			                    	$.ShowAlert(result.msg);
			                    $.zydialogs.close("SET_GROUP_WEEBOX");
			                }
			            },'json');
		            }
		            $.zydialogs.close("SET_GROUP_WEEBOX");
		        }
	    	});
		}

		$.searchResponsible = function(){
			var keywords = $("#searchResponsible input.keywords").val();
			$.post("{{ u('District/searchResponsible') }}",{'keywords':keywords},function(res){
				var html = "";
				if(res.length < 1){
					html += "<li><center style='color:#999'>没有查到相关小区</center></li>";
				}else{
					$.each(res,function(k,v){
						if( (k+1) == res.length){
							html += "<li class='last check-"+v.id+"' onclick='$.checkResponsible("+v.id+")'>";
						}else{
							html += "<li class='check-"+v.id+"' onclick='$.checkResponsible("+v.id+")'>";
						}
						html += "<span data-id='"+v.id+"'>"+v.name+"</span><span class='fr pr5' style='color:green'>已开通</span></li>";
					});
				}
				$('ul#searchResult').html(html);

			});
		};

		$.checkResponsible = function(id){
			$('#searchResult li').removeClass('on');
			$('.check-'+id).addClass('on');
		};
	});
</script>
@stop
