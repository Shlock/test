@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else {{u('Index/index')}} @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">经营类型</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <div class="content-block-title f14 c-gray">请选择经营类型</div>
        <div class="list-block y-syt y-jyclass cate_item">
            <ul>
                @foreach($cate as $key => $value) 
                <php>
                    $flag = false;
                    foreach($current as $id){
                        if($value['id'] == $id){
                            $flag = true;
                            break;
                        }
                    }           
                </php>
                @if(empty($value['childs']))
                    <li class="item-content checks @if($flag) active @endif" data-id="{{$value['id']}}">
                        <div class="item-inner">
                            <div class="item-title f16 c-gray">{{$value['name']}}</div>
                            <div class="item-after c-black"><i class="icon iconfont c-red f24">&#xe610;</i></div>
                        </div>
                    </li>
                @else
                    <li class="item-content y-jylxtitle">
                        <div class="item-inner">
                            <div class="item-title f16 c-gray">{{$value['name']}}</div>
                        </div>
                    </li>
                    @foreach($value['childs'] as $k => $v)
                        <li class="item-content checks @if(in_array($v['id'], $current)) active @endif" data-id="{{$v['id']}}">
                            <div class="item-inner ml10">
                                <div class="item-title f16">{{$v['name']}}</div>
                                <div class="item-after c-black"><i class="icon iconfont c-red f24">&#xe610;</i></div>
                            </div>
                        </li>
                        @endforeach
                    @endif
                @endforeach
            </ul>
        </div>
        <p class="y-bgnone"><a class="y-paybtn f16 confirm_btn">确定</a></p>
    </div>
@stop 

@section($js)
<script type="text/javascript">
    $(document).on("touchend",".confirm_btn",function(){
        var obj = new Object();
        var arr = new Array();
        $(".cate_item .active").each(function(){ 
            arr.push($(this).data('id'))  
        })
        obj.cateIds = arr;
        $.post("{{u('Seller/saveCate')}}", obj, function(res){
            if(res.status == false){
                $.alert(res.msg);
            } else {
                $.router.load("{{u('Seller/reg')}}", true);
            }
        }, 'json');
    });
    $(document).on("touchend", ".y-syt ul li", function(){
        if($(this).hasClass("checks")){
            if($(this).hasClass("active")){
                $(this).removeClass("active");
            }else{
                // $(this).addClass("active").siblings().removeClass("active");
                $(this).addClass("active");
            }
        }
    });
</script>
@stop
