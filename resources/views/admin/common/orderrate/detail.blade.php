@extends('admin._layouts.base')  
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="saveRate" > 
			<yz:fitem label="星级"> 
				<yz:radio name="star" options="1,2,3,4,5" texts="1星,2星,3星,4星,5星" checked="$data['star']"></yz:radio>
			</yz:fitem>
			<yz:fitem label="评价内容" name="content" type="textarea"></yz:fitem> 
			<yz:fitem label="评价图片">
				<yz:imageList name="images." images="$data['images']"></yz:imageList>
			</yz:fitem>
			<yz:fitem label="商家回复" name="reply" type="textarea"></yz:fitem> 
		</yz:form>
	@yizan_end
@stop 