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
                <item name="pid" label="门禁ID"></item>   
				<btn type="search"></btn>
			</row>
		</search>
		<btns>
			<linkbtn label="添加门禁">
				<attrs>
					<url>{{ u('Property/dooredit', ['sellerId'=>$seller['id']]) }}</url>
				</attrs>
			</linkbtn>
			<linkbtn label="导出到EXCEL" >
				<attrs>
					<url>{{ u('Property/doorexport', ['sellerId'=>$seller['id'],'districtId'=>$seller['district']['id']]) }}</url>
				</attrs>
			</linkbtn>
		</btns>
		<table >
			<columns> 
				<column code="id" label="编号" ></column>  
				<column label="门禁名称" align="center" >
					{{$list_item['name']}}
				</column> 
				<column code="pid" label="门禁ID"  ></column>  
				<column label="楼栋" >
					{{$list_item['build']['name']}}
				</column> 
				<column code="type" label="门禁类型"  >
                    @if($list_item['type'] == 1)
                    小区门禁
                    @else
                    楼宇门禁
                    @endif
                </column> 
				<column code="remark"  label="备注"></column>
				<actions> 
					<action label="编辑" >
						<attrs>
							<url>{{ u('Property/dooredit',['sellerId'=>$seller['id'], 'id'=>$list_item['id']]) }}</url>
						</attrs>
					</action>
					<!-- <action type="destroy" css="red"></action>  -->
				</actions>
			</columns>
		</table>
	</yz:list>
	@yizan_end
	<script type="text/javascript">
	$(function(){ 

	})
	</script>
@stop