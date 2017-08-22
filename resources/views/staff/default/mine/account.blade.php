@extends('staff.default._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left"  href="#" onclick="JumpURL('{{u('Mine/index',['code'=>3])}}','#mine_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop

@section('title'){{$title}}@stop
@section('content')
    <div class="admin-shop-setting-account">
        <div class="list-block list-block-full">
            <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">姓名</div>
                        <div class="item-after">
                            <span class="f_999">{{$staff['name']}}</span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="list-block list-block-full">
            <ul>
                <li class="item-content item-link avatar_box">
                    <div class="item-inner auto_height">
                        <div class="item-title">头像</div>
                        <div class="item-after">
                            <div class="imgupload">
                                <form>
                                    <label>
                                        @if(!empty($staff['avatar']))
                                            <img src="{{ formatImage($staff['avatar'],100,100) }}" alt="">
                                        @else
                                            <img src="{{ asset('wap/community/client/images/wdtt.png') }}" alt="">
                                        @endif
                                        <input type="file" onchange="file_onchange(this)" accept="image/*" style="display:none" />
                                        <script type="text/javascript">
                                            function file_onchange(sender)
                                            {
                                                PhotoCutUpload(sender, 230, 200, "{{u('Resource/upload')}}",function (result)
                                                {
                                                    if(result.status == true)
                                                    {
                                                        $.showPreloader('图片上传中');
                                                        $("img", $(sender.form)).attr("src", result.data);
                                                        $.post("{{ u('Mine/updateinfo') }}", {"avatar": result.data }, function (res)
                                                        {
                                                            $.hidePreloader();
                                                            if (res.code == '99996')
                                                            {
                                                                $.toast("登录已退出,重新登录");
                                                                window.location.href = "{{ u('User/login') }}";
                                                            }
                                                            $.toast("更新成功");
                                                        }, "json")
                                                    }
                                                });
                                            }
                                        </script>
                                    </label>
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <a class="item-content item-link"  href="#" onclick="JumpURL('{{u('Mine/repwd')}}','#mine_repwd_view',2)">
                        <div class="item-inner">
                            <div class="item-title">密码</div>
                            <div class="item-after">
                                <span class="f_999">修改密码</span>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="item-content item-link"  href="#" onclick="JumpURL('{{u('Mine/mobile')}}','#mine_mobile_view',2)">
                        <div class="item-inner">
                            <div class="item-title">手机号</div>
                            <div class="item-after">
                                <span class="f_999">{{ substr_replace($staff['mobile'],'*****',3,5) }}</span>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
@stop