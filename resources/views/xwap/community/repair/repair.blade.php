@extends('xwap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">我要报修</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <!-- 报修内容 -->
        <div class="card x-posting x-repair">
            <div class="card-header ml0 pl10 f14">
                <div class="fl wa">小区名称：</div>
                <div>{{$data['name']}}</div>
            </div>
            <div class="card-header f14">
                <div class="fl wa">故障类型：</div>
                <div class="toselect">
                    <span class="mr10">请选择</span>
                    <i class="icon iconfont down">&#xe623;</i>
                    <i class="icon iconfont up none">&#xe624;</i>
                    <div class="list-block x-reoption w100 pf f14 none">
                        <ul>
                            @foreach($list as $item)
                                <li class="item-content" data-id="{{$item['id']}}">
                                    <div class="item-inner">
                                        <div class="item-title">{{$item['name']}}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-content pr f14">
                <div class="fl wa">故障描述：</div>
                <div class="postr ml0"><textarea placeholder="请填写故障内容" class="w100" id="content"></textarea></div>
            </div>
        </div>
        <!-- 添加图片 -->
        <div class="card x-postdelst m0">
            <div class="card-content x-pjpic">
                <div class="card-content-inner oh">
                    <ul class="x-postpic clearfix">
                        @for($i = 1; $i <= 4; $i++)
                            <form>
                                <label for="image-form-{{$i}}">
                                <li id="image-form-{{$i}}-li">
                                    <img src="{{asset('wap/community/client/images/addpic.png')}}" id="img{{$i}}" class="upimage">
                                    <i class="delete none showdelete{{$i}}" data-index="{{$i}}"><i class="icon iconfont f20">&#xe605;</i></i>
                                </li>
                                </label>
                                <div style="display:none"><input type="text" name="images" id="upimage_{{$i}}"></div>
                                <input id="image-form-{{$i}}" type="file" onchange="image_form_onchange(this,{{$i}})" accept="image/*" style="display:none" />                               
                            </form>
                        @endfor
                         <script type="text/javascript">
                            function image_form_onchange(sender,i)
                            { 
                                PhotoCutUpload(sender, 200, 200, "{{u('Resource/upload')}}", function (result)
                                {
                                    if (result.status == true)
                                    {
                                        //, $(sender.form))
                                        $("#img"+i).attr("src", result.data);
                                        $("#upimage_"+i).val(result.data);
                                        $("#upimage_"+i).val(result.data);                                                
                                        $(".showdelete"+i).removeClass("none");
                                    }
                                });
                            }
                        </script>
                    </ul>
                </div>
            </div>
        </div>
        <div class="y-ddxqbtn2">
            <a href="javascript:repairSave();" class="ui-btn fr" id="submit">我要报修</a>
        </div>
    </div>
@stop

@section($js)
<script type="text/javascript">
    var districtId = "{{ Input::get('districtId') }}";
    var typeId = '';

    $(function() {
        //照片删除
        $(document).on("click",".x-postpic .delete",function(){
            $(this).parents("li").find("img").attr("src", "{{asset('wap/community/client/images/addpic.png')}}");
            $(this).addClass("none");
            $(this).parents("li").find("input").val("");
            return false;
        });

        // 故障类型option
        $(".x-reoption").css("min-height",$(window).height()-140);
        //下拉列表框
        $(document).on("click",".toselect",function(){
            if($(this).find(".x-reoption").hasClass("none")){
                $(this).find(".x-reoption").removeClass("none");
                $(".toselect .up").removeClass("none");
                $(".toselect .down").addClass("none");
            }else{
                $(this).find(".x-reoption").addClass("none");
                $(".toselect .up").addClass("none");
                $(".toselect .down").removeClass("none");
            }
            return false;
        });
        $(document).on("click",".x-reoption li",function(){
            var text = $(this).find(".item-title").text();
            $(this).parents(".x-reoption").parents(".toselect").find("span").text(text);
            $(this).parents(".x-reoption").addClass("none");
            $(".toselect .up").addClass("none");
            $(".toselect .down").removeClass("none");

            typeId = $(this).attr("data-id");
            return false;
        });

        // $(document).bind("click",function(e){
        //     var target = $(e.target);
        //     if(target.closest(".x-reoption li").length == 0){
        //         $(".x-reoption").addClass("none");
        //         $(".toselect .up").addClass("none");
        //         $(".toselect .down").removeClass("none");
        //         return false;
        //     }
        // })
    });

    function repairSave() {
        var images = new Array();
        $("input[name=images]").each(function(index,val){
            if($(this).val() != "" ){
                images.push($(this).val());
            }
        })
        var content = $("#content").val();
        var data = {
            content: content,
            images: images,
            typeId: typeId,
            districtId: districtId
        };

        if(data.typeId == ""){
            $.alert("请选择故障类型");
            return false;
        }
        // console.log(data);
        // return false;
        $.post("{{ u('Repair/save') }}", data, function(res){
            if(res.code == 0) {
                $.toast(res.msg);
                $.router.load("{!! u('Repair/index',['districtId'=>Input::get('districtId')]) !!}", true);
            }else if(res.code == '99996'){
                $.router.load("{{ u('User/login') }}", true);
            }else{
                $.alert(res.msg);
            }
        },"json");
    }
</script>
@stop