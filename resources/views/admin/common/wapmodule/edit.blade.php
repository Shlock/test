@yizan_include('admin.common.adv.edit')
@yizan_section('adv_wapmodule')
    	<yz:fitem name="image" label="模块图片" type="image"></yz:fitem>
    	<yz:fitem name="positionId" label="编号" type="hidden" val="{{$positionsId}}"></yz:fitem> 
    	<yz:fitem name="bgColor" label="背景颜色">
    		<yz:Color name="bgColor" val="{{$data['bgColor']}}"></yz:Color>
    	</yz:fitem>        
    	<yz:fitem name="type" label="触发类型">
    	<yz:select name="type" css="type" options="$type" textfield="name" valuefield="key" selected="$data['type']"></yz:select>
    	</yz:fitem>
@yizan_stop