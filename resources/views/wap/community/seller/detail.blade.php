@extends('wap.community._layouts.base')

@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>{{$seller_data['name']}}</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <span class="x-sjr ui-btn-right"><i class="x-sjsc collect_opration @if($seller_data['isCollect'] == 1)on @endif"></i></span>
    </div>
@stop

@section('content')
<div role="main" class="ui-content">
        <ul class="x-sjtab">
            <li @if(CONTROLLER_NAME == 'Goods' && ACTION_NAME == 'index') class="on" @endif><a href="{{u('Goods/index',['id'=>$seller_data['id'], 'type'=>1])}}">商品</a></li>
            <li @if(CONTROLLER_NAME == 'Goods' && ACTION_NAME == 'comment') class="on" @endif><a href="{{u('Goods/comment',['id'=>$seller_data['id']])}}">评价</a></li>
            <li @if(CONTROLLER_NAME == 'Seller' && ACTION_NAME == 'detail') class="on" @endif><a href="{{u('Seller/detail',['id'=>$seller_data['id']])}}">商家</a></li>
        </ul>
        <div class="x-topic x-sja" style="margin-top:0;">
            <img src="@if(!empty($seller_data['image'])){{formatImage($seller_data['image'],375,260)}}@else{{ asset('wap/community/client/images/sj.jpg') }}@endif" />
            <div class="x-sjdel">
                <div class="x-sjpic"><img src="@if(!empty($seller_data['logo'])) {{formatImage($seller_data['logo'],200,200)}} @else {{ asset('wap/community/client/images/x5.jpg')}} @endif" /></div>
                <div class="x-sjfont">{{$seller_data['name']}}</div>
                <ul class="x-sjbot">
                    <li>
                        <div class="x-sjbot1">
                            <p>￥{{$seller_data['serviceFee']}}</p>
                            <p class="f12">起送价</p>
                        </div>
                        <div class="x-sjbot2"></div>
                    </li>
                    <li>
                        <p>￥{{$seller_data['deliveryFee']}}</p>
                        <p class="f12">配送费</p>
                    </li>
                </ul>
            </div>
        </div>
        <ul class="x-sjdel2">
            <li>
                <div class="x-brico">
                    <img src="{{ asset('wap/community/client/images/ico/time.png')}}" width="18" />
                </div>
                <div class="x-brr">{{$seller_data['businessHours']}}</div>
            </li>
            <li>
                <div class="x-brico">
                    <img src="{{ asset('wap/community/client/images/ico/ico4.png')}}" width="18" />
                </div>
                <div class="x-brr">{{$seller_data['tel']}}<a href="tel:{{$seller_data['tel']}}"><i class="x-phone2ico"></i></a></div>
            </li>
            <li>
                <div class="x-brico">
                    <img src="{{ asset('wap/community/client/images/ico/ico2.png')}}" width="14" />
                </div>
                <div class="x-brr">{{$seller_data['address']}}</div>
            </li>
            <li>
                <div class="x-brico">
                    <img src="{{ asset('wap/community/client/images/ico/notice2.png')}}" width="18" />
                </div>
                <div class="x-brr" id="notice">
                    @if(count($articles)>0)
                    @foreach($articles as $key => $value)
                    <span>{!!$value['content']!!}</span><br/>
                    @endforeach 
                    @else 
                    <span>暂无最新公告信息</span>
                    @endif
                </div>
            </li>
        </ul>
        <div class="x-lh45 c-green">商家介绍</div>
        <ul class="x-brbg" style="margin-top:0;">
            <li class="c-black">
                {{ $seller_data['detail'] ? $seller_data['detail'] : '暂无介绍'}}
            </li>
        </ul>
    </div>
    <div data-role="footer" data-position="fixed" data-tap-toggle="false" class="x-footer">
        @if($seller_data['countService'] > 0 && $seller_data['countGoods'] < 1)
        <div class="x-choicefw c-bg" @if($seller_data['isDelivery'] == 1)onclick="window.location.href='{{ u('Goods/index',['id'=>$seller_data['id'],'type'=>2])}}'"@else style="background:#ddd;" @endif>选择服务</div>
        @elseif($seller_data['countGoods'] > 0 && $seller_data['countService'] > 0)
        <ul class="x-choicefw clearfix">
            <li class="c-bg" @if($seller_data['isDelivery'] == 1)onclick="window.location.href='{{ u('Goods/index',['id'=>$seller_data['id'],'type'=>2])}}'"@else style="background:#ddd;" @endif>选择服务</li>
            <li class="c-bg" @if($seller_data['isDelivery'] == 1)onclick="window.location.href='{{ u('Goods/index',['id'=>$seller_data['id'],'type'=>1])}}'"@else style="background:#ddd;" @endif>购买商品</li>
        </ul>
        @else
            <div class="x-choicefw c-bg" @if($seller_data['isDelivery'] == 1)onclick="window.location.href='{{ u('Goods/index',['id'=>$seller_data['id'],'type'=>1])}}'"@else style="background:#ddd;" @endif>购买商品</div>
        @endif
    </div>

<!--收藏弹框-->
<div class="x-bgtk none">
    <div class="x-bgtk1" style="position: absolute; left: 0px; top: 311px;">
        <div class="x-tkbgi">
            <div class="ts"></div>
        </div> 
    </div>
</div>
    <script>
        $(".collect_opration").click(function() {
            var obj = new Object();
            var collect = $(this);
            obj.id = "{{$seller_data['id']}}";
            obj.type = 2;
            if(collect.hasClass("on")){
                $.post("{{u('UserCenter/delcollect')}}",obj,function(result){
                    if(result.code == 0){
                        collect.removeClass("on");
                        $('.x-bgtk').removeClass('none').show().find('.ts').text('取消收藏成功');
                        $('.x-bgtk1').css({
                            position:'absolute',
                            left: ($(window).width() - $('.x-bgtk1').outerWidth())/2,
                            top: ($(window).height() - $('.x-bgtk1').outerHeight())/2 + $(document).scrollTop()
                        });
                        setTimeout(function(){
                            $('.x-bgtk').fadeOut('2000',function(){
                                $('.x-bgtk').addClass('none');
                            });
                        },'1000');
                        //$.showSuccess(result.msg);
                    } else if(result.code == 99996){
                        window.location.href = "{{u('User/login')}}";
                    } else {
                        $.showError(result.msg);
                    }
                },'json');
            }else{
                $.post("{{u('UserCenter/addcollect')}}",obj,function(result){
                    if(result.code == 0){
                        collect.addClass("on");
                        $('.x-bgtk').removeClass('none').show().find('.ts').text('收藏成功');
                        $('.x-bgtk1').css({
                            position:'absolute',
                            left: ($(window).width() - $('.x-bgtk1').outerWidth())/2,
                            top: ($(window).height() - $('.x-bgtk1').outerHeight())/2 + $(document).scrollTop()
                        });
                        setTimeout(function(){
                            $('.x-bgtk').fadeOut('2000',function(){
                                $('.x-bgtk').addClass('none');
                            });
                        },'1000');
                       // $.showSuccess(result.msg);
                    } else if(result.code == 99996){
                        window.location.href = "{{u('User/login')}}";
                    } else {
                        $.showError(result.msg);
                    }
                },'json');
            }
        });

    </script>
@stop

 