@extends('xwap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="javascript:$.back();" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">{{$cate['name']}}</h1>
    </header>
@stop
<php>
    $sorts = array(
        0 => '综合排序',
        1 => '按销量倒序',
        2 => '按起送价',
        3 => '距离最近',
        4 => '评分最高',
    );
    $tab1 = $tab2 = 1;
</php>
@section('content') 
    @include('xwap.community._layouts.bottom')

    <div class="content" id=''>
        <div class="buttons-tab x-sjfl mb10">
            <a href="#tab1" class="tab-link button f16 x-searchname active">
                <span>@if($args['id'] > 0 && $args['id'] == $cate['id']){{$cate['name']}} @else 全部 @endif</span>
                <i class="icon iconfont up">&#xe623;</i>
                <i class="icon iconfont down ml0 none">&#xe624;</i>
            </a>
            <a href="#tab2" class="tab-link button f16 create-actions content-block">
                <span class="create-actions">{{$sorts[$args['sort']]}}</span>
                <i class="icon iconfont up">&#xe623;</i>
                <i class="icon iconfont down ml0 none">&#xe624;</i>
            </a>
            <!-- 全部筛选 -->
            <div class="x-sjfltab pa none">
                <div class="mask pa"></div>
                <div class="buttons-tab fl pr">
                    @foreach ($seller_cates as $key => $value)
                        <a href="#tab1_{{$key+1}}" class="tab-link button @if($tab1 == 1) active @endif">{{ $value['name'] }}</a>
                        <?php $tab1++; ?>
                    @endforeach
                </div>
                <div class="tabs c-bgfff fl">
                    @foreach($seller_cates as $k => $item)
                        <div id="tab1_{{$k+1}}" class="tab @if($tab2 == 1) active @endif">
                            <div class="list-block x-sortlst f14">
                                <ul>
                                    <li class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title" data-id="{{$item['id']}}">全部</div>
                                            <i class="icon iconfont c-red f20">&#xe60f;</i>
                                        </div>
                                    </li>
                                    @foreach($seller_cates[$k]['childs'] as $val)
                                    <li class="item-content">
                                        <div class="item-inner typecatess">
                                            <div class="item-title seller-cate" data-id="{{$val['id']}}">{{$val['name']}}</div>
                                            <i class="icon iconfont c-red f20">&#xe60f;</i>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <?php $tab2++; ?>
                    @endforeach
                </div>
            </div>
        </div>
        
        @if(!empty($data))
            <div class="tabs">
                @include('xwap.community.seller.seller_item')
            </div>
        @else
            <div class="x-null pa w100 tc">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">很抱歉！没有找到相关商家！</p>
            </div>
        @endif
    </div>

    
@stop 

@section($js)
<script type="text/javascript">
var pageArgs = {!! json_encode($args) !!};

    $(function(){
        $(".x-sjfltab").css("height",$(window).height()-139);
        var sjfltabs_h = $(".x-sjfltab .x-sjflheight").height();
        $(".x-sjfltab .buttons-tab").css("height",sjfltabs_h);

        // 下拉
        $(document).on("touchend",".x-sjfl .x-searchname",function(){
            if($(this).hasClass("on")){
                $(".x-sjfltab").addClass("none");
                $(this).removeClass("on");
                $(this).find(".up").removeClass("none");
                $(this).find(".down").addClass("none");
            }else{
                $(".x-sjfltab").removeClass("none");
                $(this).addClass("on");
                $(this).find(".up").addClass("none");
                $(this).find(".down").removeClass("none");
            }
        });
        $(document).on("touchend",".x-sjfltab .x-sortlst li",function(){
            $(this).addClass("active").siblings().removeClass("active");
            $(".x-sjfl .x-searchname span").text($(this).find(".item-title").text());
            $(this).parents(".x-sjfltab").addClass("none");
            $(".x-sjfl .x-searchname").removeClass("on");
        });
        $(document).on("touchend",".x-sjfltab .mask",function(){
            $(this).parent().addClass("none");
            $(".x-sjfltab").addClass("none");
            $(".x-sjfl .x-searchname").removeClass("on");
            $(".x-sjfl .up").removeClass("none");
            $(".x-sjfl .down").addClass("none");
        });
        $(document).on('touchend','.create-actions', function () {
            $(".x-sjfltab").addClass("none");
            $(".x-sjfl .x-searchname").removeClass("on");
            $(".x-sjfl .up").removeClass("none");
            $(".x-sjfl .down").addClass("none");
            var buttons1 = [
            {
                text: '请选择',
                label: true
            },
            {
                text: '综合排序',
                bold: true,
                color: 'danger',
                onClick: function() {
                    $(".create-actions").text("综合排序");
                    $.rightSort(0);
                }
            },
            {
                text: '按销量倒序',
                onClick: function() {
                    $(".create-actions").text("按销量倒序");
                    $.rightSort(1);
                }
            },
            {
                text: '按起送价',
                onClick: function() {
                    $(".create-actions").text("按起送价");
                    $.rightSort(2);
                }
            },
            {
                text: '距离最近',
                onClick: function() {
                    $(".create-actions").text("距离最近");
                    $.rightSort(3);
                }
            },
            {
                text: '评分最高',
                onClick: function() {
                    $(".create-actions").text("评分最高");
                    $.rightSort(4);
                }
            }
            ];
            var buttons2 = [
            {
                text: '取消',
                bg: 'danger'
            }
            ];
            var groups = [buttons1, buttons2];
            $.actions(groups);
        });
    });

    //左侧
    $(document).on("touchend", "div.seller-cate", function(){
            $(this).addClass("on").siblings().removeClass("on");
            var id = $(this).data('id');
            pageArgs.id = id;
            $.router.load("{!! u('Seller/index')!!}?" + $.param(pageArgs), true);
    });

    //右侧
    $.rightSort = function(sort) {
        pageArgs.sort = sort;

        if (sort == 3) {
            if (clientLatLng == null) {
                isSortPosition = true;
                $("#showalertposition").removeClass('none');
                return false;
            }
            pageArgs.mapPoint = clientLatLng.getLat() + "," + clientLatLng.getLng();
        }
        $.router.load("{!! u('Seller/index')!!}?" + $.param(pageArgs), true);
    };
</script>
@stop
