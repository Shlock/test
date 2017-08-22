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
            @yizan_begin
            <!-- 账户交易记录 -->
            <div class="m-jyjlct"  style="margin-top: 10px;"> 
                <yz:list> 
                    <search>  
                        <php>
                            $search_args = $args;
                        </php>
                        <row>									
                            <item name="beginTime" label="开始时间" type="date"></item>
                            <item name="endTime" label="结束时间" type="date"></item>
                            <btn label="查询" type="search" css="btn-gray fr"></btn> 
                        </row>
                    </search>
                    <table>
                        <columns>  
                            <column code="name" label="市场商家"></column>
                            <column code="money" label="可结算金额">
                                @if($list_item['money'] > 0)+@endif{{number_format($list_item['money'], 2)}}
                            </column>
                        <actions width="80">
                            <action label="对账单" css="blu">
                                <attrs>
                                    <url>{{ u('FundCate/seller',['sellerId'=>$list_item['cate_id'],'beginTime'=>$args['beginTime'],'endTime'=>$args['endTime']]) }}</url>
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
