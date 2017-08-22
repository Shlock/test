@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('css')
    <style>
        .modal.show_img_x  {
            width:inherit;
            left: 38%;
        }
    </style>
@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{$nav_back_url}}','{{$css}}',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <span class="button button-link button-nav f_r" onclick="$.goodssave({{Input::get("tradeId")}},{{Input::get("type")}})">
            完成
        </span>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('distance')id="service-add" @stop
@if($data)
@section('preview')
    <div class="flex service-deal">
        <div class="flex-2 tc">
            @if($data['status'] == 1)
                <i class="icon iconfont">&#xe67e;</i>
                <span class="focus-color-f" id="opgoods" onclick="$.opgoods(2,{{$data['id']}},{{$args['tradeId']}})">下架</span>
            @else
                <i class="icon iconfont" id="opgoods_iconfont">&#xe67f;</i>
                <span class="focus-color-f" id="opgoods" onclick="$.opgoods(1,{{$data['id']}},{{$args['tradeId']}})" >上架</span>
            @endif
        </div>
        <div class="flex-2 tc">
            <a href="#" onclick="JumpURL('{{u('Seller/preview',['id'=>$data['id'],'type'=>$args['type'],'tradeId'=>$args['tradeId']])}}','#seller_preview_view',2)">
                <i class="icon iconfont">&#xe680;</i>
                <span>预览</span>
            </a>
        </div>
        <div class="flex-2 tc" onclick="$.opgoods(3,{{$data['id']}},{{$args['tradeId']}})">
            <i class="icon iconfont">&#xe61e;</i>
            <span>删除</span>
        </div>
    </div>
@stop
@endif
@section('content')


    <form action="javascript:;" id="img">
        <input type="file"  id="upload_but" onchange="$.fileonchange(this)" accept="image/*"/>
    </form>

    <form action="javascript:;" id="goods-form">
        <div id="preview" @if($data)style="height: 200px;" @endif>
            @if($data)
                <img id="imghead" src="{{$data['images'][0]}}" alt="" style="width: 100%" >
            @else
                <img id="imghead" class="imghead">
            @endif
            @if($data)
                <div class="upload_again">@if($args['type'] == 1)点击上传商品图片@else点击上传服务图片@endif</div>
            @else
                <div class="upload_instructions">
                    <i class="icon iconfont right-ico">&#xe689;</i>
                    <p>点击上传图片</p>
                </div>
            @endif
        </div>
        <input type="hidden" name="imgs[]" id="imgs" value="{{ $data['images'][0] }}"/>
        <input type="hidden" name="type" value="{{ $data['type'] or $args['type'] }}"/>
        <input type="hidden" value="{{ $data['cateId'] or $args['tradeId']}}" name="tradeId" />
        <input type="hidden" value="{{ (int)$data['id'] }}" name="id" />
        @if($args['type'] == 1)
            <div class="list-block">
                <ul>
                    <li>
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label">商品名称:</div>
                                <div class="item-input">
                                    <input type="text" placeholder="必填" name="name" id="name" value="{{$data['name']}}">
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="list-block add-b @if($data) @if($data['norms'])add-block @endif @endif show_norms">
                @if($data)
                    @if($data['norms'])
                        @foreach( $data['norms'] as $k=> $v)
                            <div  id="del{{$v['id']}}" >
                                <div class="delete-but" onclick ="$.deletebut({{$v['id']}})">
                                    <i class="icon iconfont right-ico">&#xe619;</i>
                                </div>
                                <ul class="goods-editer-b s-goods-editer-b">
                                    <li>
                                        <div class="item-content">
                                            <div class="item-inner">
                                                <div class="item-title label">型&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号:</div>
                                                <div class="item-input">
                                                    <input type="hidden" placeholder="" name="norms[{{$k}}][id]" id="id" value="{{ $v['id'] }}">
                                                    <input type="text" placeholder="尺寸，颜色，大小等" name="norms[{{$k}}][name]" id="norms" value="{{$v['name']}}">
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="item-content">
                                            <div class="item-inner">
                                                <div class="item-title label">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;价:</div>
                                                <div class="item-input">
                                                    <input type="text" placeholder="请输入金额（元）"  name="norms[{{$k}}][price]" id="price" value="{{$v['price']}}">
                                                </div>
                                                <span class="unit">元</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="item-content">
                                            <div class="item-inner">
                                                <div class="item-title label">库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存:</div>
                                                <div class="item-input">
                                                    <input type="text" name="norms[{{$k}}][stock]" placeholder="必须是数字"  id="stock" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" value="{{$v['stock']}}">
                                                </div>

                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        @endforeach
                    @else
                        <ul class="goods-editer-b h-goods-editer-b">
                            <li>
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title label">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;价:</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="请输入金额（元）"  name="price" id="price" value="{{$data['price']}}">
                                        </div>
                                        <span class="unit">元</span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title label">库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存:</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="必须是数字"   value="{{$data['stock']}}" name="stock" id="stock" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" >
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endif
                @else
                    <ul class="goods-editer-b h-goods-editer-b">
                        <li>
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="item-title label">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;价:</div>
                                    <div class="item-input">
                                        <input type="text" placeholder="请输入金额（元）"  name="price" id="price">
                                    </div>
                                    <span class="unit">元</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="item-title label">库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存:</div>
                                    <div class="item-input">
                                        <input type="text" placeholder="必须是数字"  name="stock" id="stock" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" >
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                @endif
            </div>
            <div class="add_goods_specifications w_b">
                <i class="icon iconfont">&#xe618;</i>
                <p class="w_b_f_1">添加商品规格</p>
            </div>
            <div class="list-block">
                <ul>
                    <li class="align-top">
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label">商品描述:</div>
                                <div class="item-input">
                                    <textarea id="brief" placeholder="请输入描述" name="brief">{{$data['brief']}}</textarea>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        @else
            <div class="list-block">
                <ul>
                    <li>
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label">服务名称:</div>
                                <div class="item-input">
                                    <input type="text" placeholder="必填" name="name" value="{{$data['name']}}">
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="list-block">
                <ul>
                    <!-- Text inputs -->
                    <li>
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label">价&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;格:</div>
                                <div class="item-input">
                                    <input type="text" placeholder="请输入价格" name="price" value="{{$data['price']}}">
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label">时&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;长:</div>
                                <div class="item-input">
                                    <input type="text" placeholder="请输入时长" name="duration" value="{{$data['duration']}}" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
                                </div>
                                <span class="unit">分钟</span>
                            </div>
                        </div>
                    </li>
                    <li onclick="showStaffIframe();">
                        <div class="item-content">
                            <a href="#">
                                <div class="item-inner">
                                    <div class="item-title label">服务人员:</div>
                                    <div class="item-input">
                                        <input type="text" name="staffName" readonly="readonly" placeholder="请选择" value="{{$data['allStaffName']}}"><!--禁止输入-->
                                        <input type="hidden" name="staffId" value="{{$data['allStaffId']}}"/>
                                    </div>
                                    <span class="icon iconfont unit">&#xe64b;</span>
                                </div>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="list-block">
                <ul>
                    <li class="align-top">
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label">服务描述:</div>
                                <div class="item-input">
                                    <textarea placeholder="请输入描述" name="brief">{{$data['brief']}}</textarea>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        @endif
    </form>
    <div class="blank0825"></div>
    <div class="all-add"></div>
@stop
@section($js)
    <script type="text/javascript">
        $.fileonchange = function(sender)
        {
            PhotoCutUpload(sender, 230, 200, "{{u('Resource/upload')}}",function (result)
            {
                if(result.status == true)
                {
                    var div = document.getElementById('preview');
                    var upload_but=document.getElementById('upload_but');
                    var img = document.getElementById('imghead');
                    div.innerHTML ='<img id="imghead" src="'+result.data+'" style="width:100%"><div class=upload_again>点击上传商品图片</div>';
                    div.style.height='200px';
                    upload_but.style.height='200px';

                    var imgs=document.getElementById('imgs');
                    imgs.value = result.data;

                }
            });
        }
    </script>
@stop
@section('show_nav')@stop