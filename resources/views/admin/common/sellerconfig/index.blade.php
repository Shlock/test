@extends('admin._layouts.base')
@section('right_content') 
<yz:table actionwidth="100">   

	<a href="{{ url("SellerAuthentication/edit") }}">编辑</a>

	<a href="{{ url("SellerAuthentication/destroy") }}">删除</a>
	
</yz:table> 
@stop