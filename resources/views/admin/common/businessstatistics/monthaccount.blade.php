@extends('admin._layouts.base')
@section('css')

@stop
@section('right_content')
    <!-- 搜索 -->
	<div id="checkList" class="">
        <div class="u-ssct clearfix">
            <form id="yzForm" class="" name="yzForm" method="post" action="{{ u('BusinessStatistics/monthAccount') }}" target="_self">
                <div class="search-row clearfix"> 
                    <div class="u-fitem clearfix" id="show_year"> 
                        <span >商家名：{{$sum['seller']['name']}}</span>
                    </div> 
                    <div class="u-fitem clearfix" id="select_year">
                        <div class="u-fitem clearfix">
                            <select name="year" style="width:auto" class="sle year_choose">
                                <option value="-99">请选择</option>
                                <option value="2015">2015</option>
                                @foreach($orderyear as $year)
                                <option value="{{$year['yearName']}}" @if($year['yearName'] == $args['year']) selected @endif>{{$year['yearName']}}</option>
                                @endforeach
                            </select>&nbsp;<span>年</span>
                        </div>
                        <div class="u-fitem clearfix">
                             <select name="month" style="width:auto" class="sle month_choose">
                                <option value="-99">请选择</option>
                                <?php for($i=1;$i<=12;$i++){?>
                                <option value="<?php echo $i;?>" @if($i == $args['month']) selected @endif><?php echo $i;?></option>
                                <?php }?>
                            </select>&nbsp;<span>月</span>
                        </div>
                        <input type="hidden" value="{{$sum['seller']['id']}}" name="sellerId" />
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
			
       <table pager="no">
    		<thead>
        		<tr>
        		  <td rowspan="2">日期</td>
        		  <td rowspan="2">营业额</td>
        		  <td rowspan="2">有效订单数</td>
        		  <td colspan="3">收入</td> 
        		  <td>支出</td>
                  <td rowspan="2">入账金额</td> 
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
                  <td>{{number_format($sum['totalPayfee'], 2)}}</td>
        		  <td>{{ $sum['totalNum'] }}</td>
                  <td>{{number_format($sum['totalOnline'], 2)}}</td>
                  <td>{{number_format($sum['totalCash'], 2)}}</td>
                  <td>{{number_format($sum['totalDiscountFee'], 2)}}</td>
                  <td>{{number_format($sum['totalDrawnfee'], 2)}}</td>
                  <td>{{number_format($sum['totalSellerFee'], 2)}}</td>
        		  <td>{{ number_format($sum['totalPayfee']/$sum['totalNum'], 2) }}</td>
        		  <td></td>
        		</tr>   
    		  	@foreach ($lists as $l)
    		  	<tr>
    		      <td>{{$l['daytime']}}</td>
        		  <td>{{number_format($l['totalPayfee'], 2)}}</td>
        		  <td>{{$l['totalNum']}}</td>
        		  <td>{{number_format($l['totalOnline'], 2)}}</td>
        		  <td>{{number_format($l['totalCash'], 2)}}</td>
        		  <td>{{number_format($l['totalDiscountFee'], 2)}}</td>
                  <td>{{number_format($l['totalDrawnfee'], 2)}}</td>
                  <td>{{number_format($l['totalSellerFee'], 2)}}</td>
        		  <td>{{number_format($l['totalPayfee']/$l['totalNum'], 2)}}</td>  
        		  <td style="cursor: pointer;"><a href="{{ u('BusinessStatistics/dayAccount', ['sellerId'=>$args['sellerId'], 'day'=>$l['daytime']]) }}" class=" blu agree" >明细</a></td>
    		 	</tr>
    		  	@endforeach
    		</tbody>
    	</table>
	</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
    var mh = {{$args['month']}};
    $(function(){
        $(".year_choose").change(function(){ 
            var year = $(this).val();
            var date = new Date();
            var cumonth = date.getMonth();
            var cuyear = date.getFullYear();
            var html = '<option value="-99">请选择</option>';
            if(year == cuyear){ 
                for (var i = 1; i <= cumonth+1; i++) {
                    if(i == mh){ 
                        html += '<option value="'+i+'" selected>'+i+'</option>';
                    } else {
                        html += '<option value="'+i+'">'+i+'</option>';
                    }
                }
            } else if(year < cuyear && year > 0){
                for (var i = 1; i <= 12; i++) {
                    if(i == mh){ 
                        html += '<option value="'+i+'" selected>'+i+'</option>';
                    } else {
                        html += '<option value="'+i+'">'+i+'</option>';
                    }
                }
            } 
            $(".month_choose").html(html); 
        }).trigger("change");
    })
</script>
@stop