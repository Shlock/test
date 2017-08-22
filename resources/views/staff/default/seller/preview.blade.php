@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Seller/editnew',['id'=>$data['id'],'type'=>$data['type'],'tradeId'=>Input::get('tradeId')])}}','#seller_editnew_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('preview')
    <a href="#" onclick="JumpURL('{{u('Seller/editnew',['id'=>$data['id'],'type'=>$data['type'],'tradeId'=>Input::get('tradeId')])}}','#seller_editnew_view',2)" class="preview_but"><i class="icon iconfont right-ico"></i>&nbsp;退出预览</a>
@stop
@section('show_nav')@stop
@section('content')
    <div class="admin-shop-deal-preview">
        <div class="admin-shop-deal-hd">
            <div class="imgbox">
                <img src="{{$data['images'][0]}}" />
            </div>
            <div class="list-block media-list">
                <ul>
                    <li class="item-content">
                        <div class="item-inner">
                            <div class="item-title">{{$data['name']}}</div>
                            <div class="item-subtitle">
                                <span class="f_red">¥{{$data['price']}}</span>
                                <span class="f_999">{{$data['seller']['address']}}</span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        @if($data['norms'])
        <div class="admin-shop-deal-bd">
            <div class="list-block">
                <ul>
                    @foreach($data['norms'] as $key => $v)
                        <li class="item-content item">
                            <div class="item-inner flex-start">
                                <div class="item-title">规格信息({{$key+1}})</div>
                            </div>
                        </li>
                        <li class="item">
                            <ul>
                                <li class="item-content item">
                                    <div class="item-inner flex-start">
                                        <div class="item-title">规&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;格</div>
                                        <div class="item-after">{{$v['name']}}</div>
                                    </div>
                                </li>
                                <li class="item-content item">
                                    <div class="item-inner flex-start">
                                        <div class="item-title">价&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;格</div>
                                        <div class="item-after">{{$v['price']}}</div>
                                    </div>
                                </li>
                                <li class="item-content item">
                                    <div class="item-inner flex-start">
                                        <div class="item-title">库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</div>
                                        <div class="item-after">{{$v['stock']}}</div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
        <div class="admin-shop-deal-bd">
            <div class="list-block">
                <ul>
                    @if($data['type'] == 2)
                    <li class="item-content item">
                        <div class="item-inner flex-start">
                            <div class="item-title">价&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;格</div>
                            <div class="item-after">{{$data['price'] or 0}}</div>
                        </div>
                    </li>
                    <li class="item-content item">
                        <div class="item-inner">
                            <div class="item-title">服务时长</div>
                            <div class="item-after">{{$data['duration']}}分钟</div>
                        </div>
                    </li>
                    <li class="item-content item">
                        <div class="item-inner">
                            <div class="item-title">服务人员</div>
                            <div class="item-after">{{ explode(",",$data['allStaffName'])[0]}},...</div>
                        </div>
                    </li>
                    @else
                        @if(!$data['norms'])
                            <li class="item-content item">
                                <div class="item-inner flex-start">
                                    <div class="item-title">价&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;格</div>
                                    <div class="item-after">{{$data['price'] or 0}}</div>
                                </div>
                            </li>
                            <li class="item-content item">
                                <div class="item-inner flex-start">
                                    <div class="item-title">库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</div>
                                    <div class="item-after">{{$data['stock']}}</div>
                                </div>
                            </li>
                        @endif
                        <li class="item-content item">
                            <div class="item-inner flex-start">
                                <div class="item-title">当前销量</div>
                                <div class="item-after">{{$data['extend']['salesVolume'] or 0}}</div>
                            </div>
                        </li>
                    @endif
                    <li class="item-content item">
                        <div class="item-inner flex-start">
                            <div class="item-title">当前状态</div>
                            <div class="item-after">@if($data['status'] == 0 )下架@else上架@endif</div>
                        </div>
                    </li>
                    <li class="item-content item">
                        <div class="item-inner flex-start">
                            <div class="item-title">@if($data['status'] == 0 )下架@else上架@endif时间</div>
                            <div class="item-after">{{Time::toDate($data['createTime'],'Y-m-d')}}</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="blank050"></div>
    <div class="blank050"></div>
@stop


@section($js)
    <script type="text/javascript">
        $(function(){
            $("#J_save").on('click',function(){
                $.showIndicator();
                $.post("{{ u('Seller/savename') }}",{'name':$("#name").val()},function(){
                    $.hideIndicator();
                    $.toast("更新成功");
                    window.location.href ="{{ u('Seller/info') }}";
                },"json");
            })
        });
    </script>
@stop
@section('preloader')@stop