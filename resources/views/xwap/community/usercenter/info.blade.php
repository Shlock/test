@extends('xwap.community._layouts.base')

@section('css')
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="{{ u('UserCenter/index') }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">我的账号</h1>
    </header>
@stop

@section('content')
    <!-- new -->
    <div class="content" id=''>
        <div class="list-block">
            <ul class="y-wdzh y-sz">
                <!-- Text inputs -->
                <li>
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label f14">头  像 </div>
                            <div class="item-after">
                                <form>
                                <span class="c-black y-wdtxi">
                                    <label for="fileAvatar">
                                        @if(!empty($user['avatar']))
                                            <img src="{{ formatImage($user['avatar'],100,100) }}" alt="">
                                        @else
                                            <img src="{{ asset('wap/community/client/images/wdtt.png') }}" alt="">
                                        @endif
                                     </label>
                                     </span>
                                    
                                    <input id="fileAvatar" type="file" onchange="fileAvatar_onchange(this)" accept="image/*" style="display:none" />
                                    <script type="text/javascript">
                                        function fileAvatar_onchange(sender)
                                        {
                                            PhotoCutUpload(sender, 200, 200, "{{u('Resource/upload')}}", function (result)
                                            {
                                                if (result.status == true)
                                                {
                                                    $("img", $(sender.form)).attr("src", result.data);

                                                    $.post("{{ u('UserCenter/updateinfo') }}", { "name": $("#name").val(), "avatar": result.data }, function (res)
                                                    {
                                                        if (res.code == '99996')
                                                        {
                                                            $.router.load("{{ u('User/login') }}", true);
                                                        }
                                                        else if (res.code != 0)
                                                        {
                                                            $.alert(res.msg);
                                                        }
                                                    }, "json")
                                                }
                                            });
                                        }
                                    </script>
                                </form>
                                <i class="icon iconfont ml10 c-gray2 vat">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li onclick="$.href('{{ u('UserCenter/nick') }}')">
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label f14">会员名称</div>
                            <div class="item-input">
                                <input type="text" value="{{$user['name']}}" id="name" placeholder="经自己取个会员名" readonly>
                                <i class="icon iconfont c-gray2 f18">&#xe63e;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li onclick="$.href('{{ u('UserCenter/verifymobile') }}')">
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label f14">手机号码</div>
                            <div class="item-input">
                                <input type="text" value="{{$user['mobile']}}" placeholder="手机号码" class="" readonly>
                                <i class="icon iconfont c-gray2 f18">&#xe63e;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li onclick="$.href('{{ u('UserCenter/repwd') }}')">
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label f14">修改密码</div>
                            <div class="item-input">
                                <input type="password" value="**********" class="c-gray2" readonly>
                                <i class="icon iconfont c-gray2 f18">&#xe63e;</i>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
@stop