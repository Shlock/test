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
        margin-right: 40px;
    }
</style>
@stop
@section('content')
<div>
    <div class="m-zjgltbg">									
        <div  class="p10">			
				<p class="f-bhtt f14">
					<span class="ml15">所在商户：{{$name}}</span>
				</p>	
            @yizan_begin
            <!-- 账户交易记录 -->
            <div class="m-jyjlct"  style="margin-top: 10px;"> 
                <yz:list> 
                    <table>
                        <columns>  
                            <column code="name" label="订单状态"></column>
                            <column code="money" label="订单金额">
                                @if($list_item['money'] > 0)+@endif{{number_format($list_item['money'], 2)}}
                            </column>
                        <actions width="80">
                            <action label="订单列表" css="blu">
                                <attrs>
                                    <url>{{ u('FundCate/lists',['nav'=>'nav'.$list_item['status'],'status'=>$list_item['status'],'sellerId'=>$args['sellerId'],'beginTime'=>$args['beginTime'],'endTime'=>$args['endTime']]) }}</url>
                                </attrs>
                            </action>
                            <!--<action type="destroy" css="red"></action>-->
                        </actions>
                        </columns>
                    </table>
                </yz:list>
            </div>
            @yizan_end		 
        </div>
    </div>
</div>
@stop

@section('js')

<script type="text/javascript">
    $(function () {
        $('.date').datepicker({
            changeYear: true,
            changeMonth: false,
        });
    });
</script>
@stop
