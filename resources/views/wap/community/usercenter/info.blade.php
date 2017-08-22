@extends('wap.community._layouts.base')
@section('css')
    <style>
        #avatar-upload-loading {
            background:rgba(255,255,255,0.5);
            width:64px;
            height:64px;
            position:absolute;
            right:40px;
            top:50%;
			margin-top:-32px;
            color:#ff2d4b;
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
    </style>  
@stop
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>我的账号</h1>
        <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:history.back(-1); @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
    <div role="main">
        <div class="y-wdzh">
            <ul>
                <li>
                    <span class="y-wdtxpho c-black">头<b style="width:2em; display:inline-block"></b>像</span>

                    <label for="image-form-file-1" id="upload-a">
                        @if(!empty($user['avatar']))
                            <img id="avatar-img" src="{{ formatImage($user['avatar'],100,100) }}" alt="">
                        @else
                            <img id="avatar-img" src="{{ asset('wap/community/client/images/wdtt.png') }}" alt="">
                        @endif
					</label>
                        <div id="avatar-upload-loading">
                            上传中...
                        </div>
                        <div class="upload-div">
                            @yizan_begin
                            <yz:imageFrom name="avatar" id="avatarInput" image="$user['avatar']" toimg="avatar-img" loading="avatar-upload-loading" maxwidth="200" maxhight="200" iscropper="1"></yz:imageFrom>
                            @yizan_end
                        </div>
                    <i class="x-rightico y-wdtxr"></i>
                </li>
                <li onclick="window.location.href='{{ u("UserCenter/nick") }}'">
                    <div class="y-wdzhl f16">会员名称</div>
                    <div class="y-wdzhr clearfix">
                        <input type="text" value="{{$user['name']}}" id="name" class="y-edittxt f14" style="background: none;" readonly />
                    </div>
                    <i class="x-rightico y-wdtxr"></i>
                </li>
                 <li onclick="window.location.href='{{ u("UserCenter/verifymobile") }}'">
                 <div class="y-wdzhl f16">手机号码</div>
                    <div class="y-wdzhr clearfix">
                        <input type="text" value="{{$user['mobile']}}" class="y-edittxt f14" style="background: none;" readonly />
                    </div>
                     <i class="x-rightico y-wdtxr"></i>
                </li> 
                <li onclick="window.location.href='{{ u("UserCenter/repwd") }}'">
                    <div class="y-wdzhl f16">修改密码</div>
                    <div class="y-wdzhr clearfix">
                        <input type="password" value="******" class="y-edittxt f14" style="background: none;" readonly/>
                    </div>
                    <i class="x-rightico y-wdtxr"></i>
                </li>
            </ul>
        </div>
    </div> 
@stop
@section('js')
    <script type="text/javascript">
        $(function() {
            $(document).on("uploadsucc",".upload-div form",function(){
                var name = $.trim($("#name").val());
                var avatar = $.trim($("#avatarInput").val());
                $.post("{{ u('UserCenter/updateinfo') }}",{"name":name,"avatar":avatar},function(res){
                    if(res.code == 0){
                        //$.showSuccess(res.msg,"{{ u('UserCenter/index') }}");
                    }else if(res.code == '99996'){
                        window.location.href = "{{ u('User/login') }}";
                    }else{
                        $.showError(res.msg);
                    }
                },"json")
            })
        })
    </script>
@stop

