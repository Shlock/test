@extends('wap.community._layouts.base')

@section('css') 
<style type="text/css">

</style>
@stop
@section('js') 
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
    <script src="{{ asset('js/dot.js') }}"></script>
@stop 

@section('show_top') 
    <div data-role="header" data-tap-toggle="false" data-position="fixed" class="x-header">
        <h1>{{$seller['name']}}</h1>
        <a href="{{u('Goods/index',['id'=>$data['seller']['id'], 'type'=>$data['type']])}}" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
        <span class="x-sjr ui-btn-right"><i class="x-sjsc collect_opration @if($data['iscollect']) on @endif" data-id="{{$data['id']}}"></i></span>
    </div>
@stop  

@section('content')
    <div role="main" class="ui-content x-menu">
        @include('wap.community.goods.sellergoodshead')
        <ul class="x-index4" style="margin-bottom:0;">
            <li class="clearfix">
                <a href="#">
                    <div class="x-naimg">
                        <img src="@if(!empty($seller['logo'])) {{formatImage($seller['logo'],200,200)}} @else {{ asset('wap/community/client/images/x5.jpg')}} @endif" />
                    </div>
                    <div class="x-index4r">
                        <p class="c-black">{{$seller['name']}}</p>
                        <p class="c-green f12 time">
                            <span>营业时间：{{$seller['businessHours']}}</span>
                        </p>
                        <p class="c-green f12">
                            {!! $seller['freight'] !!}
                        </p>
                    </div>
                </a>
            </li>
        </ul>
        <ul class="x-brbg">
            <li class="c-black">
                <div class="x-brico">
                    <img src="{{ asset('wap/community/client/images/ico/ico4.png')}}" width="20" />
                </div>
                <div class="x-brr">商家电话：{{$seller['tel']}}</div>
            </li>
            <li class="c-black">
                <div class="x-brico">
                    <img src="{{ asset('wap/community/client/images/ico/ico2.png')}}" width="14" />
                </div>
                <div class="x-brr">{{$seller['address']}}</div>
            </li>
        </ul>
        <ul class="x-brbg" style="margin-top:0;border-top:0;">
            <li class="c-black">
                <div class="fl">商家介绍：</div>
                <div class="x-sjintro">{{ $seller['detail'] ? $seller['detail'] : '暂无介绍'}}</div>
            </li>
        </ul>
		
    </div>	
    <script>
    $(document).on("touchend",".collect_opration",function(){
            var obj = new Object();
            var collect = $(this);
            obj.id = "{{$seller['id']}}";
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
                        $.router.load("{{u('User/login')}}", true);
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
                        $.router.load("{{u('User/login')}}", true);
                    } else {
                        $.showError(result.msg);
                    }
                },'json');
            }
        });

    </script>
@stop 
 