@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Seller/goodslists')}}','#seller_seller_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a href="#" onclick="JumpURL('{{u('Seller/addnew',['type' => 1,'tradeId'=>(int)Input::get('id')])}}','#seller_addnew_view',2)" class="button external pageloding button-link button-nav f_r" data-popup=".popup-about">
            添加商品
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('contentcss')infinite-scroll infinite-scroll-bottom @stop
@section('distance')data-distance="20" @stop
@section('content')
    <div class="p075" style=" padding-bottom:0!important; overflow:hidden;">
        <input type="text" name="keywords" id="keywords_word" onkeydown="$.keywords_goods({{$status}},{{$id}});" class="icon iconfont tc search-box-input" placeholder="&#xe63e; 请输入想要搜索的商品" value="{{Input::get("keywords")}}"/>
    </div>
    <div class="management-editor plr085 clearfix">
        <span class="f_l name">商品分类列表</span>
        <div class="check_all f_l">
            <input type="radio" name="all" id="all"        class="mt "   onclick="$.checkAll('goodsId')" />
            <span  class="  focus-color-f">全选</span>
            <input type="radio" name="all" id="Checkbox1"  class="mt "   onclick="$.uncheckAll('goodsId')" />
            <span  class="  focus-color-f">全不选</span>
        </div>
        <span  class=" f_r focus-color-f" id="seller_editor-but">编辑</span>
    </div>
    <div class="buttons-tab flex" id="service">
        <div class="flex-1 p-0-085 shuxian"><a  href="#" onclick="JumpURL('{{u('Seller/goods',['status'=>1,'id'=>$id,'type'=>1])}}','#seller_goods_view_1',2)" class="tab @if($status == 1)active @endif button">上架商品</a></div>
        <div class="flex-1 p-0-085"><a  href="#" onclick="JumpURL('{{u('Seller/goods',['status'=>2,'id'=>$id,'type'=>1])}}','#seller_goods_view_2',2)" class="tab button  @if($status == 2)active @endif">下架商品</a></div>
    </div>
    <div>
        <div class="tabs">
            <div id="shangjia" class="tab active">
                @if($goods)
                    <ul class="service-list lists_item_ajax">
                        @include('staff.default.seller.service_item')
                    </ul>
                @else
                    <div class="x-null tc"  style="top:40%">
                        <i class="icon iconfont">&#xe60c;</i>
                        <p>很抱歉，暂无商品</p>
                    </div>
                @endif
                <div class="flex service-deal hide">
                    <div class="flex-1 tc">
                        @if($status == 1)
                            <i class="icon iconfont">&#xe67e;</i>
                            <span class="focus-color-f" id="op-goods" onclick="$.opgoods_all(2,{{$id}},{{$status}},{{Input::get('type')}})" data-type="2" data-goodstype="1">下架</span>
                        @else
                            <i class="icon iconfont">&#xe67f;</i>
                            <span class="focus-color-f" id="op-goods"  onclick="$.opgoods_all(1,{{$id}},{{$status}},{{Input::get('type')}})"  data-type="1" data-goodstype="1">上架</span>
                        @endif
                    </div>
                    <div class="flex-1 tc" id="op-del" data-type="3" onclick="$.opgoods_all(3,{{$id}},{{$status}},{{Input::get('type')}})"  >
                        <i class="icon iconfont">&#xe61e;</i>
                        <span>删除</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section($js)
    <script type="text/javascript">
        $(function(){
            //复选框全选
            $.checkAll = function (formvalue) {
                var roomids = document.getElementsByName(formvalue);
                for ( var j = 0; j < roomids.length; j++) {
                    if (roomids.item(j).checked == false) {
                        roomids.item(j).checked = true;
                    }
                }
            }

            //复选框全不选
            $.uncheckAll = function (formvalue) {
                var roomids = document.getElementsByName(formvalue);
                for ( var j = 0; j < roomids.length; j++) {
                    if (roomids.item(j).checked == true) {
                        roomids.item(j).checked = false;
                    }
                }
            }

            //复选框选择转换
            $.switchAll = function (formvalue) {
                var roomids = document.getElementsByName(formvalue);
                for ( var j = 0; j < roomids.length; j++) {
                    roomids.item(j).checked = !roomids.item(j).checked;
                }
            }

        });
    </script>
@stop
@section('preloader')@stop
