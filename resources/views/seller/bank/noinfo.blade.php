@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.m-tab table tbody td{
		padding: 10px 0px;
		font-size: 12px;
  		text-align: center;
	}
	.m-tab{
		margin-top: -11px;
	}
	#money-form-item,#waitConfirmMoney-form-item,#lockMoney-form-item{
		  margin-right: 80px;
	}

    .banks p{text-align: center;}
    .bank_font{ font-weight: bold; font-size: 18px;}
    #butn{width:220px; height: 40px; background: red; color: white; border-radius: 10px; font-size:15px;cursor: pointer;}
    #butn_margin{ margin-top:20px;}
</style>
@stop
@section('content')
	<div >
		<div class="m-zjgltbg">
			<p class="f-bhtt f14">
				<span class="ml15">银行管理</span>
			</p>										
			<div  class="p10">				
				@yizan_begin				
				<!-- 账户交易记录 -->
                          <div class="banks" >
                              <p><img src="{{asset('images/tu.png')}}"></p>
                              <p><span class="bank_font">暂无银行卡</span></p>
                              <p id="butn_margin"><a href="{{u('bank/addInfo')}}"><input type="button" id="butn" value="添加银行卡"></a></p>
                          </div>
				@yizan_end  
			</div>
		</div>
	</div>
@stop

@section('js')
@stop