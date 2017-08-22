@extends('wap.community._layouts.base')
@section('js')
@stop
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>我要报修</h1>
        <a href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{u('Repair/index',['districtId'=>Input::get('districtId')])}} @endif" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop
@section('content')
<div role="main" data-role="content">
        <ul class="y-wybx f14">
            <li>
                <span>小区名称：</span><span>{{$data['name']}}</span>
            </li>
            <li>
                <span class="fl">故障类型：</span>
                <span class="y-gzlx fl">
                    <span class="y-select">请选择故障类型</span>
                    <ul class="y-wybxoption none">
                        @foreach($list as $item)
                        <li data-id="{{$item['id']}}">{{$item['name']}}</li>
                        @endforeach
                    </ul>
                </span>
            </li>
            <li>
                <span>故障描述：</span><span><textarea placeholder="请填写故障内容" id="content"></textarea></span>
            </li>
        </ul>
        <div class="y-postpic clearfix">
            <div class="x-pjpic clearfix">
                <ul>
                    <li id="image-form-1-li"><div><label for="image-form-file-1"><img src="{{asset('wap/community/client/images/addpic.png')}}"  id="img1"></label><a href="javascript:;" class="delete none" data-index="1"></a></div></li>
                    <li id="image-form-2-li"><div><label for="image-form-file-2"><img src="{{asset('wap/community/client/images/addpic.png')}}"  id="img2"></label><a href="javascript:;" class="delete none" data-index="2"></a></div></li>
                    <li id="image-form-3-li"><div><label for="image-form-file-3"><img src="{{asset('wap/community/client/images/addpic.png')}}"  id="img3"></label><a href="javascript:;" class="delete none" data-index="3"></a></div></li>
                    <li id="image-form-4-li"><div><label for="image-form-file-4"><img src="{{asset('wap/community/client/images/addpic.png')}}"  id="img4"></label><a href="javascript:;" class="delete none" data-index="4"></a></div></li>
                </ul>
                <div style="display:none">
                    @yizan_begin
                        <yz:imageFrom name="images" toimg="img1" iscropper="1"></yz:imageFrom>
                        <yz:imageFrom name="images" toimg="img2" iscropper="1"></yz:imageFrom>
                        <yz:imageFrom name="images" toimg="img3" iscropper="1"></yz:imageFrom>
                        <yz:imageFrom name="images" toimg="img4" iscropper="1"></yz:imageFrom>
                    @yizan_end
                </div>
            </div>
        </div>
        <div class="x-bxbtn">
            <p><a href="javascript:;" class="ui-btn x-btnbr" id="submit">我要报修</a></p>
        </div>
    </div>
<script type="text/javascript">
    $(function() {
        var districtId = "{{ Input::get('districtId') }}";

        $(".y-wybxoption").css("min-height",$(window).height()-139);
        //下拉列表框
        $(".y-select").touchend(function(){
            if($(this).siblings(".y-wybxoption").hasClass("none")){
                $(this).siblings(".y-wybxoption").removeClass("none");
            }else{
                $(this).siblings(".y-wybxoption").addClass("none");
            }
            return false;
        });
        $(".y-wybxoption li").touchend(function(){
            $(this).parent().siblings(".y-select").text($(this).text());
            $(this).parent().addClass("none");
            $(this).parent().siblings(".y-select").attr('data-id', $(this).data('id'));
            return false;
        });
        
        //上传图片
        $(document).on("uploadsucc",".x-pjpic form",function(){
            $("#" + this.id + "-li .delete").removeClass('none');
        });
        //照片删除
        $(".x-pjpic .delete").touchend(function ()
        {
            $(this).parents("li").find("img").attr("src", "{{asset('wap/community/client/images/addpic.png')}}");
            $(this).addClass("none");
            $("#image-from-val-" + index).val("");
        });

        $("#submit").touchend(function(){
            var images = new Array();
            $("input[name=images]").each(function(index,val){
                if($(this).val() != "" ){
                    images.push($(this).val());
                }
            })
            var content = $("#content").val();
            var typeId = $('.y-select').data('id');
            var data = {
                content: content,
                images: images,
                typeId: typeId,
                districtId: districtId
            };
            // console.log(data);
            // return;
            $.post("{{ u('Repair/save') }}", data, function(res){
                if(res.code == 0) {
                    $.showSuccess(res.msg);
                    window.location.href = "{!! u('Repair/index',['districtId'=>Input::get('districtId')]) !!}";
                }else if(res.code == '99996'){
                    window.location.href = "{{ u('User/login') }}";
                }else{
                    $.showError(res.msg);
                }
            },"json");
        })
        
        // $(document).bind("click",function(e){
        //     var target = $(e.target);
        //     if(target.closest(".y-wybxoption li").length == 0){
        //         $(".y-wybxoption").addClass("none");
        //         return false;
        //     }
        // })
    })
</script>
@stop