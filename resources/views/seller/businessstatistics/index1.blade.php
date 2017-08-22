@extends('admin._layouts.base')
@section('css')

@stop
@section('right_content')
    <!-- 搜索 -->
	<div id="checkList" class="">
        <div class="u-ssct clearfix">
            <form id="yzForm" class="" name="yzForm" method="post" action="{{ u('BusinessStatistics/index') }}" target="_self">
                <div class="search-row clearfix">
                    <div class="u-fitem clearfix" id="show_year">
                        <span><h2><?php echo date('Y');?>年<?php echo date('m')?>月</h2></span>
                        <span style="cursor: pointer;" id="old_record">点击查看往期数据</span>
                    </div> 
                    <div class="u-fitem clearfix" style="display:none;" id="select_year">
                        <div class="u-fitem clearfix">
                            <select name="year" style="width:auto" class="sle  ">
                                <option value="-99">请选择</option>
                                @foreach($orderyear as $year)
                                <option value="{{$year['yearName']}}" @if($year['yearName'] == $args['year']) selected @endif>{{$year['yearName']}}</option>
                                @endforeach
                            </select>&nbsp;<span>年</span>
                        </div>
                        <div class="u-fitem clearfix">
                             <select name="month" style="width:auto" class="sle  ">
                                <option value="-99">请选择</option>
                                <?php for($i=1;$i<=12;$i++){?>
                                <option value="<?php echo $i;?>" @if($i == $args['month']) selected @endif><?php echo $i;?></option>
                                <?php }?>
                            </select>&nbsp;<span>月</span>
                        </div>
                    </div> 
                    <div id="staffname-form-item" class="u-fitem clearfix">
                        <span class="f-tt">商家电话:</span>
                        <div class="f-boxr">
                              <input type="text" name="name" id="name" class="u-ipttext" value="" />
                        </div>
                    </div>
                	<button type="submit" class="btn mr5">搜索</button>
                </div>
            </form>
         </div>
   </div>
   
   <!-- 列表 -->
   @yizan_begin
	<yz:list>
	   	<btns> 
    		<!--linkbtn label="导出到EXCEL" type="export" url="{{ u('StaffStatistic/export?'.$excel) }}"></linkbtn-->
    	</btns>
			
       <table>
    		<thead>
        		<tr>
        		  <td rowspan="2">商家名</td>
        		  <td rowspan="2">本月营业额</td>
        		  <td rowspan="2">有效订单数</td>
        		  <td colspan="3">收入</td> 
        		  <td>支出</td>
        		  <td rowspan="2">客单价</td> 
        		  <td rowspan="2">查看</td>
        		</tr>
        		<tr>   
        		  <td>在线支付</td>
        		  <td>现金支付</td>
        		  <td>优惠券</td>
        		  <td>佣金</td>   
        		</tr>
    		</thead>
    		
    		<tbody> 
        		<tr>
        		  <td>合计</td>
        		  <td>{{ $sum['totalPayfee'] }}</td>
        		  <td>{{ $sum['totalNum'] }}</td>
        		  <td>{{ $sum['totalOnline'] }}</td>
        		  <td>{{ $sum['totalCash'] }}</td>
        		  <td>{{ $sum['totalDiscountFee'] }}</td>
        		  <td>{{ $sum['totalDrawnfee'] }}</td> 
        		  <td>{{ number_format($sum['totalPayfee']/$sum['totalNum'], 2) }}</td>
        		  <td></td>
        		</tr>   
    		  	@foreach ($lists as $l)
    		  	<tr>
    		      <td>{{$l['name']}}</td>
        		  <td>{{$l['totalPayfee']}}</td>
        		  <td>{{$l['totalNum']}}</td>
        		  <td>{{$l['totalOnline']}}</td>
        		  <td>{{$l['totalCash']}}</td>
        		  <td>{{$l['totalDiscountFee']}}</td>
        		  <td>{{$l['totalDrawnfee']}}</td>
        		  <td>{{number_format($l['totalPayfee']/$l['totalNum'], 2)}}</td>  
        		  <td style="cursor: pointer;"><a href="{{ u('BusinessStatistics/monthAccount', ['sellerId'=>$l['id'], 'month'=>$args['month'],'year'=>$args['year']]) }}" class=" blu agree" >对账单</a></td>
    		 	</tr>
    		  	@endforeach
    		</tbody>
    	</table>
	</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
$(document).on("click","#old_record",function(){
	$("#show_year").hide();
	$("#select_year").show();
});

@if($args['month'] != date('m') && $args['year'] != date('y'))
$("#old_record").trigger('click'); 
@endif
</script>
@stop