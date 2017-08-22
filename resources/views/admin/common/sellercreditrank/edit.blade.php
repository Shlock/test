@extends('admin._layouts.base')
@section('css') 
@stop
@section('right_content')
@yizan_begin

<yz:form id="yz_form" action="update">
	<yz:fitem name="name" label="昵称"></yz:fitem>
	<yz:fitem name="icon" label="图标" type="image"></yz:fitem>
	<yz:fitem name="minScore" css="minScore" label="最低信誉分"></yz:fitem>
	<yz:fitem name="maxScore" css="maxScore" label="最高信誉分"></yz:fitem> 
</yz:form>

@foreach ($errors->all() as $error)
    <p class="error">{{ $error }}</p>
@endforeach 
@yizan_end
@stop


@section('js')
<script type="text/javascript">  
	var partten = /^\d+$/;  
	$('input[name="minScore"]').keyup(function(){
		 if(!partten.test($(this).val())){
		    $(this).val(''); 
		  }
	}); 
	$('input[name="maxScore"]').keyup(function(){ 
		$(".maxScore").css("border","1px solid #ddd");
		if(!partten.test($(this).val())){
		    $(this).val(''); 
		}
	});	  
</script>
@stop