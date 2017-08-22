@extends('seller._layouts.base')
@section('css')
<style>
    .m-zjgltbg .m-ghkh ul li>p.fl {
        width: 340px;
        border-left: 1px solid #ccc;
    }
    .bianjikuang{ margin-left:100px; }
    #bianji{width: 100px; height: 35px; background: red; border-radius: 5px; color:white; font-size: 15px; cursor: pointer;}
</style>
@stop
@section('content')
    <div >
        <div class="m-zjgltbg">
            <p class="f-bhtt f14">
                <span class="ml15">银行管理</span>
            </p>
            <div class="p10">
                <!-- 更换绑定银行卡号 -->
                <div class="clearfix mt10">
                    <div class="m-yhk m-ghkh" style="width:739px;">
                        <div class="m-ftabct mb15">
                            <ul>
                                <li class="clearfix">
                                    <span class="fl">卡号</span>
                                    <p class="fl clearfix">
                                        <span class="fl">{{ substr_replace($list['banks'][0]['bankNo'],'**** **** ****',0,-4) }}</span>
                                    </p>
                                </li>
                                <li class="clearfix even">
                                    <span class="fl">开户行</span>
                                    <p class="fl  clearfix">
                                        <span class="fl">{{ $list['banks'][0]['bank'] }}</span>
                                    </p>
                                </li>
                                <li class="clearfix">
                                    <span class="fl">户主名</span>
                                    <p class="fl clearfix">
                                        <span class="fl">{{substr_replace($list['banks'][0]['name'],'***',0,3) }}</span>
                                    </p>
                                </li>
                                <li class="clearfix">
                                    <span class="fl">手机号</span>
                                    <p class="fl clearfix">
                                        <span class="fl">{{ substr_replace($list['banks'][0]['mobile'],'****',3,4) }}</span>
                                    </p>
                                </li>
                            </ul>
                        </div>
                        <div class="bianjikuang"><p><input type="button" value="编辑" id="bianji"></p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop

@section('js')

    <script type="text/javascript" src="{{ asset('js/jsform.js') }}"></script>
    <script type="text/tpl" id="serviceContent">
	<div style="width:400px; height:100px;text-align:center;padding:10px;">
	    <div style="margin-top:30px; margin-left:20px;">
	     <div class="u-iptboxct fl" >
			<input type="text" name="verifyCode" style="width:180px;margin-top:0px; border:1px solid #009DDA;" placeholder="验证码">
		 </div>
			<a href="javascript:;" class="btn f-btn fl ml10 verify" style="background:#009DDA;color:white; width: 120px;margin-top:1px;line-height:28px;">获取验证码</a>
	    </div>
	</div>
  </script>
<script>
    $(function() {
        $(document).on('click','#bianji',function(){
            mobile = '{{$list['banks'][0]['mobile']}}';
            if(mobile != ""){
                var reg = /^1[\d+]{10}$/;
                if(!reg.test(mobile)){
                    alert('请输入正确的手机号码');
                    return false;
                }
            }else{
                alert("手机号码不能为空");
                return false;
            }
            time();
            $.post("{{ u('bank/getVerify') }}",{'mobile':mobile},function(result){
                if(result.code==10003){
                    alert('同一个号码一天只能发送10次验证码');
                    window.location.href = "{{u('bank/index')}}";
                    return false;
                }else if(result.data==null){
                    $(document).on('click','.verify',function(){
                        mobile = '{{$list['banks'][0]['mobile']}}';
                        if(mobile != ""){
                            var reg = /^1[\d+]{10}$/;
                            if(!reg.test(mobile)){
                                alert('请输入正确的手机号码');
                                return false;
                            }
                        }else{
                            alert("手机号码不能为空");
                            return false;
                        }
                        time();
                        $.post("{{ u('bank/getVerify') }}",{'mobile':mobile},function(resu){

                        },'json');
                    })
                }else{
                    $(document).on('click','.verify',function(){
                        alert('验证码已发送');
                        return false;
                    })
                }

            },'json');

        });



        $("#bianji").click(function () {
            var dialog = $.zydialogs.open($("#serviceContent").html(), {
                boxid: 'SET_GROUP_WEEBOX',
                width: 300,
                title: '提示',
                showClose: true,
                showButton: true,
                showOk: true,
                showCancel: true,
                okBtnName: '确定',
                cancelBtnName: '取消',
                contentType: 'content',
                onOk: function () {
                    var verifyCode = $("[name='verifyCode']").val();
                    var mobile     = '{{$list['banks'][0]['mobile']}}';
                    $.post("{{ u('bank/checkVerify') }}", {'verifyCode': verifyCode,'mobile':mobile}, function (res) {
                        $.ShowAlert(res.msg);
                        if (res.code == 0) {
                            window.location.href='{{u('bank/edit')}}';
                        }
                    }, 'json');
                },
                onCancel: function () {
                    $.zydialogs.close("SET_GROUP_WEEBOX");
                }
            });
        });

    });


    var partten = /^\d+$/;
    $('input[name="mobileold"]').keyup(function(){
        if(!partten.test($(this).val())){
            $(this).val('');
        }
    });
    $('input[name="mobile"]').keyup(function(){
        if(!partten.test($(this).val())){
            $(this).val('');
        }
    });
    var wait = 60;
    function time() {
        if (wait == 0) {
            $(".verify").removeAttr("disabled") ;
            $(".verify").text("免费获取验证码");
            wait = 60;
        } else {
            $(".verify").attr('disabled',"true");
            $(".verify").text(wait + "秒后重新获取");
            wait--;
            setTimeout(function () {
                        time();
                    },
                    1000)
        }
    }

    </script>


@stop
