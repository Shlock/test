@extends('admin._layouts.base')
@section('css')

@stop
@section('right_content')
    <!-- 搜索 -->
    <div id="checkList" class="">
        <div class="u-ssct clearfix">
            <form id="yzForm" class="" name="yzForm" method="post" action="{{ u('BusinessStatistics/dayaccount') }}" target="_self"> 
                <div class="search-row clearfix">
                    <div class="u-fitem clearfix" > 
                        <span >商家名：{{$sum['seller']['name']}}</span>
                    </div> 
                    <div class="u-fitem clearfix" > 
                        <span >账单日期：{{$args['day']}}</span>
                    </div> 
                    <div class="u-fitem clearfix" > 
                        <span >有效订单数：{{$sum['totalNum']}}</span>
                    </div> 
                    <div class="u-fitem clearfix" > 
                        <span >已入账总额：<b style="color: red">￥{{number_format($sum['totalSellerFee'], 2)}}</b></span>
                    </div> 
                    <br>
                    <div id="staffname-form-item" class="u-fitem clearfix">
                        <span class="f-tt">订单号:</span>
                        <div class="f-boxr">
                              <input type="text" name="sn" id="sn" class="u-ipttext" value="{{$args['sn']}}" />
                        </div>
                    </div>
                    <div class="u-fitem clearfix">
                        <span>状态：</span>&nbsp;
                         <select name="status" style="width:auto" class="sle  ">
                            <option value="0">全部</option> 
                            <option value="1" @if(1 == $args['status']) selected @endif>已完成</option> 
                            <option value="2" @if(2 == $args['status']) selected @endif>已取消</option> 
                            <option value="3" @if(3 == $args['status']) selected @endif>未完成</option> 
                        </select>
                    </div>
                    <input type="hidden" name="sellerId" value="{{$args['sellerId']}}" />
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
                  <td rowspan="2" width="180">订单号</td> 
                  <td colspan="3">收入</td> 
                  <td>支出</td>
                  <td rowspan="2">入账金额</td> 
                  <td rowspan="2">状态</td>
                </tr>
                <tr>   
                  <td>在线支付</td>
                  <td>现金支付</td>
                  <td>优惠券</td>
                  <td>佣金</td>   
                </tr>
            </thead>
            
            <tbody>  
                @foreach ($lists as $list_item) 
                <tr>
                  <td>@if($list_item['orderType'] == 1)<a href="{{u('Order/detail', ['id'=>$list_item['id']])}}">{{$list_item['sn']}}@else<a href="{{u('ServiceOrder/detail', ['id'=>$list_item['id']])}}">{{$list_item['sn']}}@endif</a></td>
                  <td>@if(!$list_item['isCashOnDelivery']){{$list_item['payFee']}}@else 0 @endif</td>
                  <td>@if($list_item['isCashOnDelivery']){{$list_item['payFee']}}@else 0 @endif</td>
                  <td>{{$list_item['discountFee'] > $list_item['totalFee']  ? $list_item['totalFee'] : $list_item['discountFee']}}</td>
                  <td>{{$list_item['drawnFee']}}</td>
                  <td>@if($list_item['isCashOnDelivery'])-{{$list_item['drawnFee']}}@else{{$list_item['sellerFee']}}@endif</td>
                  <td>{{$list_item['orderStatus']}}</td> 
                </tr>
                @endforeach
            </tbody>
        </table>
    </yz:list>
    @yizan_end
@stop

@section('js')
<script type="text/javascript"> 
</script>
@stop