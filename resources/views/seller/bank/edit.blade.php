@extends('seller._layouts.base')
@section('css')

@stop
@section('content')
    <div >
        <div class="m-zjgltbg">
            <p class="f-bhtt f14">
                <span class="ml15">银行管理</span>
            </p>
            <div  class="p10">

            </div>
        </div>
    </div>


    <div class="m-tab m-smfw-ser pt20">
        @yizan_begin
        <yz:form id="yz_form" action="editBank">
            <yz:fitem name="bankNo"  label="卡号"></yz:fitem>
            <yz:fitem name="bank"  label="开户行" append="1"></yz:fitem>
            <yz:fitem name="name" label="户主名" append="1"></yz:fitem>
            <yz:fitem name="mobile" label="手机号"></yz:fitem>
            <div id="sameMobile" style="display: none">
            <yz:fitem name="verifys" label="验证码" >
                <div class="f-boxr">
                    <div class="u-iptboxct fl">
                        <input type="text" name="verifyCode" style="width:100px;margin-top:0px;" >
                    </div>
                    <a href="javascript:;" class="btn f-btn fl ml10 verify" style="width: 120px;margin-top:1px;line-height:28px;">获取验证码</a>
                </div>
            </yz:fitem>
            </div>
        </yz:form>
        @yizan_end

    </div>


@stop
@section('js')


    <script type="text/javascript">
        //$('input[name=mobile]').val('');

        $(".verify").click(function(){
            mobile = $("input[name=mobile]").val();
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
            },'json');
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
                $(".verify").text(wait + "秒后获取验证码");
                wait--;
                setTimeout(function () {
                            time();
                        },
                        1000)
            }
        }
        
        $("#mobile").blur(function () {
            if($(this).val() != "{{$data['mobile']}}"){
                $("#sameMobile").show();
            } else {
                $("#sameMobile").hide();
            }
        })
    </script>


@stop