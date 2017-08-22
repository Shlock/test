@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{$nav_back_url}}','{{$csss}}',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a href="#" external class="button button-link pull-right" id="J_end">完成</a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('contentcss')hasbottom @stop
@section('content')
    <div class="admin-shop-goods-cate">
        <h2 class="content-padded">
            <span class="f_999">行业分类</span>
        </h2>
        <div class="list-block">
            <ul>
                <li class="item-content select show_selected{{$type}}">
                    <div class="item-inner"  style="background-position:97% center;" >
                        <div class="item-input val_selected" data-type="{{$data['type']}}" data-id="{{$data['id']}}" data-tradeid="{{$data['tradeId']}}">
                            {{$data['cates']['name'] or '请选择'}}
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <h2 class="content-padded">
            <span class="f_999">分类名称</span>
        </h2>
        <div class="list-block">
            <ul>
                <li>
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-input">
                                <input type="text" placeholder="请输入分类名称" value="{{$data['name']}}" name="cate_name">
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
@stop
@section($js)
    <script type="text/javascript">
        $(document).on('click','.page-current .show_selected{{$type}}', function () {
                var buttons1 = [
                {
                    text: '请选择',
                    label: true
                },
                @foreach($trade as $v)
                    @if($v['type'] == $type)
                    {
                        text: '{{$v['name']}}',
                        bold: true,
                        color: '#525252',
                        onClick: function() {
                            $(".page-current .val_selected").html("{{$v['cates']['name']}}").attr("data-tradeid","{{$v['cateId']}}").attr("data-type","{{$v['type']}}").attr("data-id","{{$id}}");
                            $(".actions-modal").remove();
                        }
                    },
                    @endif
                @endforeach
                ];
                    var buttons2 = [
                    {
                        text: '取消',
                        bg: 'danger',
                        onClick: function() {
                            $(".actions-modal").remove();
                        }
                    }
                ];
                var groups = [buttons1, buttons2];
                $.actions(groups);
        });
    </script>
@stop