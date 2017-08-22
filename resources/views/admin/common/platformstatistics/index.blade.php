@extends('admin._layouts.base')
@section('css') 
@stop 
@section('right_content')
@yizan_begin 
        <yz:list>
            <search url="{{ $url }}">
                <row>
                    <item label="统计年份">
                    <yz:select name="year" css="year_choose" options="$orderyear" textfield="yearName" valuefield="yearName"  selected="$args['year']"></yz:select> 
                    </item> 
                    <item label="月份">
                    <yz:select name="month" css="month_choose" options="1,2,3,4,5,6,7,8,9,10,11,12" texts="1月,2月,3月,4月,5月,6月,7月,8月,9月,10月,11月,12月" selected="$args['month']"></yz:select> 
                    </item>  
                    <btn type="search"></btn>
                </row>
            </search>
            <yz:tabs>
                <navs>
                    <nav name="status" label="平台统计">
                        <attrs>
                            <url>{{ u('PlatformStatistics/index',['nav'=>0, 'year'=>$args['year'], 'month'=>$args['month']]) }}</url>
                            <css>@if( $args['nav'] == 0) on @endif</css>
                        </attrs>
                    </nav>
                    <nav name="status" label="销售统计">
                        <attrs>
                            <url>{{ u('PlatformStatistics/index',['nav'=>1, 'year'=>$args['year'], 'month'=>$args['month']]) }}</url>
                            <css>@if( $args['nav'] == 1 ) on @endif</css>
                        </attrs>
                    </nav> 
                </navs>
            </yz:tabs>
            @if($args['nav'] == 0)
            <table pager="no">
                <thead> 
                    <tr>   
                      <td>日期</td>
                      <td>新会员</td>
                      <td>佣金</td>
                      <td>商家提现</td>
                      <td>会员充值</td>
                      <td>商家充值</td>   
                    </tr>
                </thead>
                
                <tbody> 
                    <tr>
                      <td>汇总</td> 
                      <td>{{ $sum['totalRegNum'] }}</td>
                      <td>{{number_format($sum['totalDrawnFee'], 2)}}</td>
                      <td>{{number_format($sum['totalSellerFee'], 2)}}</td>
                      <td>{{number_format($sum['totalBuyerCharge'], 2)}}</td>
                      <td>{{number_format($sum['totalSellerCharge'], 2)}}</td>  
                    </tr>   
                    @foreach ($lists as $list_item)
                    <tr>
                      <td>{{$list_item['daytime']}}</td> 
                      <td>{{$list_item['totalRegNum']}}</td>
                      <td>{{number_format($list_item['totalDrawnFee'], 2)}}</td>
                      <td>{{number_format($list_item['totalSellerFee'], 2)}}</td>
                      <td>{{number_format($list_item['totalBuyerCharge'], 2)}}</td>
                      <td>{{number_format($list_item['totalSellerCharge'], 2)}}</td> 
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <table pager="no">
                <thead> 
                    <tr>   
                      <td>日期</td>
                      <td>营业额</td>
                      <td>有效订单数量</td>
                      <td>退款/取消订单</td>
                      <td>在线支付</td>
                      <td>现金支付</td>   
                      <td>优惠券</td>   
                    </tr>
                </thead>
                
                <tbody> 
                    <tr>
                      <td>汇总</td> 
                      <td>{{number_format($sum['totalPayfee'], 2)}}</td>
                      <td>{{ $sum['totalNum'] }}</td>
                      <td>{{ $sum['totalCancleNum'] }}</td>
                      <td>{{number_format($sum['totalOnline'], 2)}}</td>
                      <td>{{number_format($sum['totalCash'], 2)}}</td>  
                      <td>{{number_format($sum['totalDiscountFee'], 2)}}</td>  
                    </tr>   
                    @foreach ($lists as $list_item)
                    <tr>
                      <td>{{$list_item['daytime']}}</td> 
                      <td>{{number_format($list_item['totalPayfee'], 2)}}</td>
                      <td>{{ $list_item['totalNum'] }}</td>
                      <td>{{ $list_item['totalCancleNum'] }}</td>
                      <td>{{number_format($list_item['totalOnline'], 2)}}</td>
                      <td>{{number_format($list_item['totalCash'], 2)}}</td> 
                      <td>{{number_format($list_item['totalDiscountFee'], 2)}}</td>  
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
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
                    html += '<option value="'+i+'" selected>'+i+'月</option>';
                  } else {
                    html += '<option value="'+i+'">'+i+'月</option>';
                  }
                }
            } else if(year < cuyear && year > 0){
                for (var i = 1; i <= 12; i++) {
                  if(i == mh){ 
                    html += '<option value="'+i+'" selected>'+i+'月</option>';
                  } else {
                    html += '<option value="'+i+'">'+i+'月</option>';
                  }
                }
            } 
            $(".month_choose").html(html); 
        }).trigger("change");
    })
</script>  
@stop