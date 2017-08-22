<nav class="m-nyy">
	<p style="height:6px;"></p>
    <div class="m-fwlst clearfix">
		<a href="javascript:;" class="on">{{ Lang::get('wap.goods_name') }}</a>
		<a href="{{ u('Staff/index',$option) }}">{{ Lang::get('wap.staff_name') }}</a>
	</div>		
    <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" class="sj-l"></a>
    <a href="{{ u('Goods/search') }}" class="u-ssicob"></a>
</nav> 