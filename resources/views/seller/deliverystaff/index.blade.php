@yizan_include('seller.staff.index')

@yizan_section('actions')
<actions width="80">
	<action label="编辑" css="blu">
		<attrs>
			<url>{{ u('DeliveryStaff/edit',['id'=>$list_item['id'],'type'=>$list_item['type']]) }}</url>
		</attrs>
	</action>
	<action type="destroy" css="red"></action>                						
</actions>
@yizan_stop

@yizan_section('btns')
<btns>
	<linkbtn label="添加人员" url="{{ u('DeliveryStaff/create') }}" css="btn-gray"></linkbtn>
</btns>
@yizan_stop