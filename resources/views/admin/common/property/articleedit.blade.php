@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
        <yz:form id="yz_form" action="articlesave">
       		<input type="hidden" name="sellerId" value="{{$seller['id']}}">
       		<yz:fitem label="物业公司">
            	{{$seller['name']}}
            </yz:fitem>
            <yz:fitem label="小区名称">
            	{{$seller['district']['name']}}
            </yz:fitem>
			<yz:fitem name="title" label="标题"></yz:fitem>    
			<yz:fitem name="content" label="公告内容"> 
				<yz:Editor name="content" value="{{ $data['content'] }}"></yz:Editor> 
			</yz:fitem> 
		</yz:form>
    @yizan_end

@stop 