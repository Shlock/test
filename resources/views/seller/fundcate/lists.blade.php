@extends('seller._layouts.base')
@section('css')
<style type="text/css">
    .m-tab table tbody td{padding: 10px 0px;}
    #checkListTable p{padding:0px 0px 0px 10px;}
</style>
@stop
@section('content')
<div>
    <div class="m-zjgltbg">
        <div class="p10">
            <!-- 订单管理 -->
            <div class="g-fwgl">
                <p class="f-bhtt f14 clearfix" style="border-bottom:0;">
                    <span class="ml15 fl">所在商户：{{$name}}</span>
                </p>
            </div>
            @yizan_begin
            <php> 
                $navs = ['nav0','nav1','nav2','nav3','nav4','nav5','nav6'];
                $nav = in_array(Input::get('nav'),$navs) ? Input::get('nav') : 'nav0' ; 
                $$nav = "on";
            </php>
            <yz:list>
                <tabs>
                    <navs>
                        <nav label="已完成">
                            <attrs>
                                <url>{{ u('FundCate/lists',['status'=>'1','nav'=>'nav1','sellerId'=>$args['sellerId'],'beginTime'=>$args['beginTime'],'endTime'=>$args['endTime']]) }}</url>
                                <css>{{$nav1}}</css>
                            </attrs>
                        </nav>
                        <nav label="待发货">
                            <attrs>
                                <url>{{ u('FundCate/lists',['status'=>'2','nav'=>'nav2','sellerId'=>$args['sellerId'],'beginTime'=>$args['beginTime'],'endTime'=>$args['endTime']]) }}</url>
                                <css>{{$nav2}}</css>
                            </attrs>
                        </nav>
                        <nav label="待完成">
                            <attrs>
                                <url>{{ u('FundCate/lists',['status'=>'3','nav'=>'nav3','sellerId'=>$args['sellerId'],'beginTime'=>$args['beginTime'],'endTime'=>$args['endTime']]) }}</url>
                                <css>{{$nav3}}</css>
                            </attrs>
                        </nav>
                        <nav label="已取消">
                            <attrs>
                                <url>{{ u('FundCate/lists',['status'=>'4','nav'=>'nav4','sellerId'=>$args['sellerId'],'beginTime'=>$args['beginTime'],'endTime'=>$args['endTime']]) }}</url>
                                <css>{{$nav4}}</css>
                            </attrs>
                        </nav>
                        <nav label="已退款">
                            <attrs>
                                <url>{{ u('FundCate/lists',['status'=>'5','nav'=>'nav5','sellerId'=>$args['sellerId'],'beginTime'=>$args['beginTime'],'endTime'=>$args['endTime']]) }}</url>
                                <css>{{$nav5}}</css>
                            </attrs>
                        </nav>
                        <nav label="已删除">
                            <attrs>
                                <url target="_blank">{{ u('FundCate/lists',['status'=>'6','nav'=>'nav6','sellerId'=>$args['sellerId'],'beginTime'=>$args['beginTime'],'endTime'=>$args['endTime']]) }}</url>
                                <css>{{$nav6}}</css>
                            </attrs>
                        </nav>
                    </navs>
                </tabs>
                <search url="{{ $searchUrl }}">
                    <row>
                        <item name="beginTime" label="开始时间" type="date"></item>
                        <item name="endTime" label="结束时间" type="date"></item>
                        <btn type="search" css="btn-gray"></btn>
                    </row>
                </search>
                <table>
                    <columns>
                        <column code="sn" label="订单SN码"></column>
                        <column code="money" label="订单金额"></column>
                        <column code="create_time" label="订单时间" type="time"></column>
                        <actions width="80">
                            <action label="订单详情" css="blu">
                                <attrs>
                                    <url>{{ u('FundCate/detail',['orderId'=>$list_item['id']]) }}</url>
                                </attrs>
                            </action>
                        </actions>
                    </columns>
                </table>
            </yz:list>
            @yizan_end
        </div>
    </div>
</div>
@stop
@section('js')
@stop
