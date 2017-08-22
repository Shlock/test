<div class="m-fyct clearfix">                   
    <div class="m-fycon pt15 mr20 fr">
        @if ($pager['page'] > 1) 
        <a href="{{ $pager['page_prev'] }}" page="{{ $pager['prev_page'] }}" class="prec ajaxlink"><i class="fa fa-angle-double-left"></i></a>
        @endif
        @foreach ($pager['page_nums'] as $page_num) 
            @if ($pager['page'] == $page_num['name']) 
            <a href="javascript:;" class="num on" page="{{ $page_num['name'] }}">{{ $page_num['name'] }}</a>
            @elseif($page_num['name'] == '...')
            <a href="javascript:;" class="num">...</a>
            @else
            <a href="{{ $page_num['url'] }}" page="{{ $page_num['name'] }}" class="num ajaxlink">{{ $page_num['name'] }}</a>
            @endif
        @endforeach
        @if ($pager['page'] < $pager['page_count']) 
        <a href="{{ $pager['page_next'] }}" page="{{ $pager['prev_page'] }}" class="next ajaxlink"><i class="fa fa-angle-double-right"></i></a>
        @endif
    </div>
    <p class="fr mr20 hcolor" style="line-height:60px;">
        @if ($pager['total_count'] > 0) 
        {{ $pager['total_count'] }} 条记录，共 {{ $pager['page_count'] }} 页
        @else
        暂无相关数据
        @endif
    </p>
</div>