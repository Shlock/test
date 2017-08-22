@extends('seller._layouts.base')
@section('css')
<style type="text/css"> 
    #month{margin-top: 15px;}
    .btn-gray{margin-left: 10px; margin-bottom: 10px;}
</style>
@stop
@section('content') 
<div class="ma">
    <div class="m-ydtt" style="margin-top:0px;">
        <div class="x-bbmain">
            <div class="x-pjgltt">对帐单</div>
            @yizan_begin
                <yz:list>
                    <search>
                        <row>
                            <item label="统计时间">
                            <yz:select name="year" css="year_choose" options="$orderyear" textfield="yearName" valuefield="yearName"  selected="$args['year']"></yz:select> 
                            </item> 
                            <yz:select name="month" css="month_choose" options="1,2,3,4,5,6,7,8,9,10,11,12" texts="1月,2月,3月,4月,5月,6月,7月,8月,9月,10月,11月,12月" selected="$args['month']"></yz:select>  
                            <btn type="search" css="btn-gray"></btn>
                        </row>
                    </search>
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
                              <td>汇总</td>
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
        </div>
    </div>
</div>
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

   