@extends('admin._layouts.base')

@section('right_content')
	@yizan_begin
	<yz:list> 
		<btns>
			<linkbtn type="add" url="create"></linkbtn>
		</btns>
		<table relmodule="Adv">
			<columns>			
				@yizan_yield('adv_wapmodule')
					<column code="position" label="广告位">
						{{$list_item['position']['name']}}
					</column> 
					<column code="name" label="名称"></column>  
					<column code="image" label="图片" type="image" width="60" iscut="1">
						<php>
							$color = empty($list_item['color']) ? '#ccc' : $list_item['color'];
						</php>
						<a href="{{ $list_item['image'] }}" target="_blank" style="display:block; width:60px; height:60px;border:solid 1px {{ $color }}; background:#ccc;">
							<img src="{{ formatImage($list_item['image'],60,60,1) }}"/>
						</a>
					</column> 
					<column code="sort" width="50" label="排序"></column>
					<column code="status" width="50" label="状态"  type="status"></column>  
					<column code="createTime" label="时间" type="time"></column>
				@yizan_stop
				<actions width="70"> 
					<action type="edit" css="blu"></action>
					<action type="destroy" css="red"></action>
				</actions> 
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop