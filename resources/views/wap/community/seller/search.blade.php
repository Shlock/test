@extends('wap.community._layouts.base')
@section('show_top')
	<div data-role="header" data-position="fixed" class="x-header">
		<h1>搜索</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
	</div>
@stop
@section('js') 
	<script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> 
@stop 
@section('content') 
	<div role="main" class="ui-content">
		<div class="x-search clearfix">
			<form id="search_form" >
            <input type="text" placeholder="请输入商家名称关键词" name="keyword" value="{{$option['keyword']}}" id="keyword"/>
            <div class="x-serbtn">
                <i class="x-serico search_submit"></i>
            </div>
            </form >
        </div>
        <div class="x-lh45">热门搜索</div>
        <ul class="x-serhot clearfix">
        	@if(count($hot_data) > 5)
        	@for($i = 0;$i < 5; $i++)
            <li onclick="window.location.href='{{ u('Seller/search',['keyword'=>$hot_data[$i]['name']]) }}'">{{$hot_data[$i]['name']}}</li>
            @endfor
            @else 
            @for($i = 0;$i < count($hot_data); $i++)
            <li onclick="window.location.href='{{ u('Seller/search',['keyword'=>$hot_data[$i]['name']]) }}'">{{$hot_data[$i]['name']}}</li>
            @endfor
            @endif
        </ul>
		<!-- 有搜索记录的时候 -->
		@if($history_search)
		<ul class="x-serhis c-green">
			@foreach($history_search as $key => $item)
            <li @if($key == count($data)-1)style="border-bottom:none;"@endif onclick="window.location.href='{{ u('Seller/search',['keyword'=>$item]) }}'"><a href="javascript:;">{{$item}}</a></li>
            @endforeach
            <li class="x-clearhis"><i class="x-delico"></i>清除历史记录</li>
        </ul>
		@endif
	</div>
	@include('wap.community._layouts.swiper')  
	<script >
		$.SwiperInit('.data-content ul','li',"{{ u('Seller/search',$option) }}");
		$(function() {
			$(".search_submit").click(function(){
				var keyword = $("#keyword").val();
				window.location.href="{!! u('Seller/search') !!}?keyword=" + keyword;
				//$("#search_form").submit();
			});

			$(".x-clearhis").click(function() {
				$.post("{{u('Seller/clearsearch')}}", function(result){
                    window.location.href = "{!! u('Seller/search')!!}";
                });
			})
		})
		
	</script>
@stop 