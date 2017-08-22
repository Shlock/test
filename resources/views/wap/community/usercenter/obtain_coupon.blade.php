@extends('wap.community._layouts.base')
@section('show_top')
@stop

@section('css')
    <style type="text/css">
        /*优惠券链接*/
        /*分享功能*/
        .y-fxbanner{width: 100%;}
        .y-fxbanner img{width: 100%;vertical-align: top;}
        .y-fxmain{background: url({{ asset('wap/images/ybg.jpg') }}) no-repeat top center #fef7e4;}
        .y-fxkp{margin: 0 27px;padding-bottom: 30px;}
        .y-fxkptitle{background: url({{ asset('wap/images/y2.png') }}) no-repeat bottom center;background-size: 100% 100%;height: 34px;line-height: 34px;border-radius: 5px 5px 0 0;overflow: hidden;text-align: center;color: #fff;font-weight: normal;}
        .y-fxkpmain{background: url({{ asset('wap/images/y3.png') }}) no-repeat top center;background-size: 100% 100%;border-radius: 0 0 5px 5px;text-align: center;overflow: hidden;}
        .y-fxkpmainlw{background-size: 100%;}
        .y-fxkpmoney{background: url({{ asset('wap/images/y4.png') }}) no-repeat center center;margin: 28px auto 0;width: 143px;height: 99px;background-size: 100%;color: #ff6a3f;font-size: 20px;line-height: 100px;}
        .y-fxkpmoney span{font-size: 45px;font-weight: bold;margin-left: -15px;}
        .y-fxkpmain p{color: #898888;font-size: 14px;margin: 14px 0;}
        .y-fxkpmain p a{color: #ff0000!important;}
        .y-fxkpmain .y-fxkpbtn{width: 145px;height: 48px;line-height: 40px;font-size: 18px;background: url({{ asset('wap/images/y5.png') }}) no-repeat;background-size: 100%;margin: 0 auto 15px;padding: 0;}
        .y-getcoupons{border-top: 1px solid #f3ebd6;padding: 0 10px;}
        .y-gettitle{text-align: center;color: #fe2d4a;font-size: 14px;margin: 20px 0;}
        .y-getmain{margin-bottom: 30px;}
        .y-getmain li{position: relative;background: #fff;border-bottom: 1px dashed #efd285;height: 70px;margin-top: 10px;}
        .y-getmain li:first-child{margin-top: 0;}
        .y-fxlqimg{width: 47px;height: 47px;border-radius: 100%;overflow: hidden;position: absolute;top: 50%;left: 15px;margin-top: -24px;}
        .y-fxlqimg img{width: 100%;}
        .y-fxlqmain{margin: 0 10px 0 78px;padding-top: 15px;}
        .y-fxlqmain p.f14{margin-top: 3px;color: #898888;}
        .y-fxlqmain p.f14 span{color: #fe2d4a;margin-top: -14px;font-weight: bold;}
        .y-hdgz{color: #666;font-size: 14px;line-height: 24px;padding-bottom: 30px;}
        .y-fxkpmain .ui-input-text{margin: 23px 33px;border-color: #fe2d4a;}
        .y-fxkpmain .ui-input-text .y-fxkpinput{padding: 0 .4em;line-height: 45px;font-size: 14px;color: #aaa;}
        .y-over{padding: 35px 0 8px;}
        .y-textover{margin-bottom: 28px!important;}
    </style>
@stop

@section('content')
    @if($data['code'] == 0)
        <div role="main">
            <div class="y-fxbanner">
                <img src="{{ $activity['bgimage'] }}">
            </div>
            <div class="y-fxmain">
                <div class="y-fxkp">
                    <h3 class="y-fxkptitle f14">请输入您的手机号码</h3>
                    <div class="y-fxkpmain y-fxkpmainlw">
                        <input type="text" placeholder="请在这里输入您的手机号码" class="y-fxkpinput" id="mobile">
                        <a href="javascript:;" class="ui-btn y-fxkpbtn" id="sure">立即领取</a>
                    </div>
                </div>
                <div class="y-getcoupons">
                    <div class="y-gettitle"><p>活动规则</p><img src="{{ asset('wap/images/icobtm.png') }}" height="9"></div>
                    <div class="y-hdgz">
                        {!! $activity['brief'] !!}
                    </div>
                </div>
            </div>
        </div>
    @elseif($data['code'] == 1)
        <div role="main">
            <div class="y-fxbanner">
                <img src="{{ $activity['bgimage'] }}">
            </div>
            <div class="y-fxmain">
                <div class="y-fxkp">
                    <h3 class="y-fxkptitle f14">恭喜您抢到了</h3>
                    <div class="y-fxkpmain">
                        <div class="y-fxkpmoney"><span>{{ $activity['promotion'][0]['promotion']['money'] }}</span>元</div>
                        <p>优惠券已放入<a href="{{ u('UserCenter/index') }}">{{$name}}</a>账户<br>快去使用吧，不要过期噢！</p>
                        <a href="{{ $activity['buttonUrl'] }}" class="ui-btn y-fxkpbtn">{{ $activity['buttonName'] }}</a>
                    </div>
                </div>
                @if(!empty($activity['logs']))
                    <div class="y-getcoupons">
                        <div class="y-gettitle"><p>看谁领了优惠券</p><img src="{{ asset('wap/images/icobtm.png') }}" height="9"></div>
                        <ul class="y-getmain">
                            @foreach($activity['logs'] as $v)
                                <li>
                                    <div class="y-fxlqimg fl">
                                        @if(!empty($v['user']['avatar']))
                                            <img id="avatar-img" src="{{ formatImage($v['user']['avatar'],50,50) }}" alt="">
                                        @else
                                            <img id="avatar-img" src="{{ asset('wap/community/client/images/wdtt.png') }}" alt="">
                                        @endif
                                    </div>
                                    <div class="y-fxlqmain">
                                        <p class="f16">{{$v['user']['name']}}</p>
                                        <p class="f14">{{ Time::toDate($v['createTime'],'Y-m-d H:i') }}<span class="fr f18">{{ $activity['promotion'][0]['promotion']['money'] }}元</span></p>
                                    </div>
                                </li>
                            @endforeach

                        </ul>
                    </div>
                @endif

                <div class="y-getcoupons">
                    <div class="y-gettitle"><p>活动规则</p><img src="{{ asset('wap/images/icobtm.png') }}" height="9"></div>
                    <div class="y-hdgz">
                        {!! $activity['brief'] !!}
                    </div>
                </div>
            </div>
        </div>
    @elseif($data['code'] == 2)
        <div role="main">
            <div class="y-fxbanner">
                <img src="{{ $activity['bgimage'] }}">
            </div>
            <div class="y-fxmain">
                <div class="y-fxkp">
                    <h3 class="y-fxkptitle f14">抱歉</h3>
                    <div class="y-fxkpmain">
                        <div class="y-over">
                            <img src="{{ asset('wap/images/y8.png') }}" width="174">
                        </div>
                        <p class="y-textover">来晚了，已经被抢光啦！</p>
                        <a href="{{ $activity['buttonUrl'] }}" class="ui-btn y-fxkpbtn">{{ $activity['buttonName'] }}</a>
                    </div>
                </div>
                @if(!empty($activity['logs']))
                    <div class="y-getcoupons">
                        <div class="y-gettitle"><p>看谁领了优惠券</p><img src="{{ asset('wap/images/icobtm.png') }}" height="9"></div>
                        <ul class="y-getmain">
                            @foreach($activity['logs'] as $v)
                                <li>
                                    <div class="y-fxlqimg fl">
                                        @if(!empty($v['user']['avatar']))
                                            <img id="avatar-img" src="{{ formatImage($v['user']['avatar'],50,50) }}" alt="">
                                        @else
                                            <img id="avatar-img" src="{{ asset('wap/community/client/images/wdtt.png') }}" alt="">
                                        @endif
                                    </div>
                                    <div class="y-fxlqmain">
                                        <p class="f16">{{$v['user']['name']}}</p>
                                        <p class="f14">{{ Time::toDate($v['createTime'],'Y-m-d H:i') }}<span class="fr f18">{{ $activity['promotion'][0]['promotion']['money'] }}元</span></p>
                                    </div>
                                </li>
                            @endforeach

                        </ul>
                    </div>
                @endif
                <div class="y-getcoupons">
                    <div class="y-gettitle"><p>活动规则</p><img src="{{ asset('wap/images/icobtm.png') }}" height="9"></div>
                    <div class="y-hdgz">
                        {!! $activity['brief'] !!}
                    </div>
                </div>
            </div>
        </div>
    @elseif($data['code'] == 3)
        <div role="main">
            <div class="y-fxbanner">
                <img src="{{ $activity['bgimage'] }}">
            </div>
            <div class="y-fxmain">
                <div class="y-fxkp">
                    <h3 class="y-fxkptitle f14">抱歉</h3>
                    <div class="y-fxkpmain">
                        <div class="y-over">
                            <img src="{{ asset('wap/images/y9.png') }}" width="174">
                        </div>
                        <p class="y-textover">来晚了，活动已结束！</p>
                        <a href="{{ $activity['buttonUrl'] }}" class="ui-btn y-fxkpbtn">{{ $activity['buttonName'] }}</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('js')
    <script type="text/javascript">
        $(document).on("touchend","#sure",function(){
            var mobile = $("#mobile").val();
            var orderId = "{{ $args['orderId'] }}";
            var activityId = "{{ $args['activityId'] }}";
            var openId = "{{ $args['openId'] }}";
            var reg = /^1\d{10}$/;
            if(!reg.test(mobile)){
                $.showError("请输入正确的手机号码");
                return false;
            }

            $.post("{{ u('UserCenter/docheckmobile') }}",{mobile:mobile,orderId:orderId,activityId:activityId,openId:openId},function(result){
                    location.reload();
            },"json");
        });
    </script>
@stop