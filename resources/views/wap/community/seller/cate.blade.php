@extends('wap.community._layouts.base')
@section('css')
    <style>
        #avatar-upload-loading {
            background:rgba(255,255,255,0.5);
            width:64px;
            height:64px;
            position:absolute;
            left:20px;
            top:0px;
            color:#08c894;
            line-height:64px;
            text-align: center;
            display: none;
            overflow: hidden;
            font-size:0.75em
        }
        #avatar-img{
            display: block;
            border-radius:64px;
            width:64px;
            height: 64px;
            z-index:0;
            top:-7px;
            left: 20px;
        }
        #upload-a{
              width: 64px;
              height: 64px;
              border-radius: 100%;
              position: absolute;
              right: 2.5em;
              top: 1em;
        }
        .upload-div{
            display:none
        }
		.y-but{
            position:fixed;
            bottom:0px;
            left:0;
            width: 100%;
            background-color:#eee;
            height:60px;
            margin:0px;
        }
        .y-but .confirm_btn{
            width:95%;
            margin:10px 10px 0px 10px;
        }
		.y-jylx ul li.on{
			background:url({{asset('wap/community/client/images/ico/red-ok.png')}}) no-repeat 90% center;
			background-size:30px 20px;
		}
    </style>
@stop
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>经营类型</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else {{u('Index/index')}} @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div> 
@stop
@section('content')
    <!-- /content -->
    <div class="y-jylx">
        <p class="f14 c-green">请选择经营类型</p>
        <ul class="cate_item" style="padding-bottom:60px;">
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
                <li @if($flag) class="on" @endif data-id="{{$value['id']}}">{{$value['name']}}</li> 
            @else
                <p style="padding-left:0px;border-top:1px solid #e6e6e6;line-height: 35px;padding-top:5px;">{{$value['name']}}</p> 
                @foreach($value['childs'] as $k => $v)
                <li @if(in_array($v['id'], $current)) class="on" @endif data-id="{{$v['id']}}"><span>{{$v['name']}}</span></li> 
                @endforeach
            @endif
            @endforeach
        </ul>
        <div class="y-but"><button class="confirm_btn">确定</button></div>
    </div>
    <script type="text/javascript">
    $(".y-jylx li").touchend(function(){
        if($(this).hasClass("on")){
            $(this).removeClass("on");
        }else{
           // $('.cate_item li').removeClass('on');
            $(this).addClass("on");
        }
    });
    $(".confirm_btn").click(function(){
        var obj = new Object();
        var arr = new Array();
        $(".cate_item .on").each(function(){ 
            arr.push($(this).data('id'))  
        })
        obj.cateIds = arr;
        $.post("{{u('Seller/saveCate')}}", obj, function(res){
            if(res.status == false){
                $.showError(res.msg);
            } else {
                window.location.href = "{{u('Seller/reg')}}";
            }
        }, 'json');
    });
    </script>
@stop 

