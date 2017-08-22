@yizan_include('seller.staffschedule.index')

@yizan_section('actions')
<actions width="80">
	<action label="查看日程" css="blu">
		<attrs>
			<url>{{ u('DeliverySchedule/edit',['id'=>$list_item['id'],'type'=>$list_item['type']]) }}</url>
		</attrs>
	</action>            						
</actions>
@yizan_stop