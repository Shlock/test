@extends('admin._layouts.base')

<?php
$type = [
        ['key'=>'1','name'=>'商家分类'],
    // ['key'=>'2','name'=>'商品'],
        ['key'=>'3','name'=>'商品'],
        ['key'=>'4','name'=>'商家'],
        ['key'=>'5','name'=>'URL'],
        ['key'=>'6','name'=>'服务'],
        ['key'=>'7','name'=>'文章'],
];
$data['type'] = isset($data['type']) ? $data['type'] : 1;
?>

@section('css')
    <style type="text/css">
        .add_content{ width: 80px; height: 30px; cursor: pointer}
        .y-fenlei tr td{padding: 5px;}
        .y-fenlei ,.y-fenlei tr th,.y-fenlei tr td{border: 1px #ccc solid;text-align: center;}
        .y-fenlei{clear: both;width: 450px;margin-left:105px;}

        .form-tip{background-color: #F9F9F9;padding: 10px 0px;margin-bottom: 10px;}

        .sle{float: left;margin-right: 10px;}
        .y-yhqsl{width:70px;line-height: 30px;border: 1px solid #ddd;margin-right: 10px;text-align: center;}

        .ioio{border: 1px solid #000;padding-left: 5px; max-width: 650px; margin-top: 2px;}
        .cur{cursor: pointer;margin-left: 15px;}

        .my{
            width: 120px;
            border: 1px solid #ccc;
            height: 20px;
            padding: 5px;
        }
        .my2{
            width: 120px;
            border: 1px solid #ccc;
            height: 20px;
            padding: 5px;
        }
    </style>
@stop

@section('right_content')
    @yizan_begin
    <yz:form id="yz_form" action="save">
        <div id="name-form-item" class="u-fitem clearfix ">
            <span class="f-tt">
                 名称:
            </span>
            <div class="f-boxr">
                <input type="text" maxlength="8" style="width:224px;" value="{{ $data['name'] }}" name="name" class="my2 f-ipt fl" placeholder="限8个字符内">
            </div>
        </div>

        <yz:fitem label="城市">
            <php> $data['cityId'] = isset($data['cityId']) ? $data['cityId'] : 0; </php>
            <yz:select name="cityId" options="$city" textfield="name" valuefield="id" selected="$data['cityId']"></yz:select>
        </yz:fitem>

        <div id="menuIcon-form-item" class="u-fitem clearfix ">
            <span class="f-tt">
                 菜单图片:
            </span>
            <div class="f-boxr">
                <ul class="m-tpyllst clearfix">
                    <li id="menuIcon-box" class="">
                        <a id="img-preview-menuIcon" class="m-tpyllst-img" href="javascript:;" target="_self">
                            @if(!empty($data['menuIcon']))
                                <img src="{{$data['menuIcon']}}" alt="">
                            @else
                                <img src="" alt="" style="display:none;">
                            @endif
                        </a>
                        <a class="m-tpyllst-btn img-update-btn" href="javascript:;" data-rel="menuIcon">
                            <i class="fa fa-plus"></i> 上传图片
                        </a>
                        @if(!empty($data['menuIcon']))
                            <input type="hidden" data-tip-rel="#menuIcon-box" name="menuIcon" id="menuIcon" value="{{$data['menuIcon']}}">
                        @else
                            <input type="hidden" data-tip-rel="#menuIcon-box" name="menuIcon" id="menuIcon" value="">
                        @endif
                    </li>
                    &nbsp;<span>建议尺寸：100px*100px,支持JPG\PNG格式</span>
                </ul>
            </div>
        </div>

        <yz:fitem name="type" label="链接类型">
            <yz:select name="type" css="type" options="$type" textfield="name" valuefield="key" selected="$data['type']"></yz:select>
        </yz:fitem>


        <fitem type="script">
            <script type="text/javascript">
                jQuery(function($){
                    $("select[name=type]").change(function() {
                        $("input[name='arg']").val("");
                        var type = $("select[name='type'] option:selected").val();
                        if(type == 1){
                            $('#sellerCate-form-item').show();
                            $('#sellerGoods-form-item').hide();
                            $('#serviceGoods-form-item').hide();
                            $('#systemSellers-form-item').hide();
                            $('#article-form-item').hide();
                            $('#url-form-item').hide();
                        }else if(type == 7){
                            $('#sellerCate-form-item').hide();
                            $('#sellerGoods-form-item').hide();
                            $('#serviceGoods-form-item').hide();
                            $('#systemSellers-form-item').hide();
                            $('#article-form-item').show();
                            $('#url-form-item').hide();
                        }else if(type == 3){
                            $('#sellerCate-form-item').hide();
                            $('#sellerGoods-form-item').show();
                            $('#serviceGoods-form-item').hide();
                            $('#systemSellers-form-item').hide();
                            $('#article-form-item').hide();
                            $('#url-form-item').hide();
                            window.open("{!! u('Service/index') !!}");
                        }else if(type == 4){
                            $('#sellerCate-form-item').hide();
                            $('#sellerGoods-form-item').hide();
                            $('#serviceGoods-form-item').hide();
                            $('#systemSellers-form-item').show();
                            $('#article-form-item').hide();
                            $('#url-form-item').hide();
                            window.open("{!! u('Service/index') !!}");
                        }else if(type == 5){
                            $('#sellerCate-form-item').hide();
                            $('#sellerGoods-form-item').hide();
                            $('#serviceGoods-form-item').hide();
                            $('#systemSellers-form-item').hide();
                            $('#article-form-item').hide();
                            $('#url-form-item').show();
                        }else if(type == 6){
                            $('#sellerCate-form-item').hide();
                            $('#sellerGoods-form-item').hide();
                            $('#serviceGoods-form-item').show();
                            $('#systemSellers-form-item').hide();
                            $('#article-form-item').hide();
                            $('#url-form-item').hide();
                            window.open("{!! u('Service/index') !!}");
                        }else {
                            $('#sellerCate-form-item').hide();
                            $('#sellerGoods-form-item').hide();
                            $('#serviceGoods-form-item').hide();
                            $('#systemSellers-form-item').hide();
                            $('#article-form-item').hide();
                            $('#url-form-item').hide();
                        }
                    });
                    $("select[name=type]").trigger('change');
                    $("#sellerGoods option[value='-1']").attr("disabled","disabled");
                });
            </script>
        </fitem>
        <yz:fitem pstyle="display:none;"  name="sellerCate" label="选择商家分类">
            <select id="sellerCate" name="sellerCate" style="min-width:200px;width:auto" class="sle ">
                @foreach($sellerCate as $item)
                    @if($item['childs'])
                        <optgroup label="{{$item['name']}}">
                            @foreach($item['childs'] as $child)
                                <option value="{{$child['id']}}" @if($data['arg'] == $child['id'])selected="selected"@endif>{{$child['name']}}</option>
                            @endforeach
                        </optgroup>
                    @else
                        <option value="{{$item['id']}}" @if($data['arg'] == $item['id'])selected="selected"@endif>{{$item['name']}}</option>
                    @endif
                @endforeach
            </select>
            <span class="ts ts1"></span>
        </yz:fitem>
        <yz:fitem pstyle="display:none;"  name="sellerGoods" label="商品编号参数">
            <input type="text" name="sellerGoods" id="sellerGoods" class="u-ipttext" value="">
            <span class="ts ts1">请到商家页面查看商品编号后填写</span>
        </yz:fitem>
        <yz:fitem pstyle="display:none;"  name="serviceGoods" label="服务编号参数">
            <input type="text" name="serviceGoods" id="serviceGoods" class="u-ipttext" value="">
            <span class="ts ts1">请到商家页面查看服务编号后填写</span>
        </yz:fitem>
        <yz:fitem  pstyle="display:none;" name="systemSellers" label="商家编号参数">
            <input type="text" name="systemSellers" id="systemSellers" class="u-ipttext" value="">
            <span class="ts ts1">请到商家页面查看商家编号后填写</span>
        </yz:fitem>
        <yz:fitem pstyle="display:none;"  name="article" label="选择文章">
            <select id="article" name="article" style="min-width:200px;width:auto" class="sle ">
                <option value="0" >请选择</option>
                @foreach($article as $item)
                    <option value="{{$item['id']}}" @if($data['arg'] == $item['id'])selected="selected"@endif>{{$item['title']}}</option>
                @endforeach
            </select>
            <span class="ts ts1"></span>
        </yz:fitem>
        <yz:fitem  pstyle="display:none;" name="url" label="输入路径"></yz:fitem>
        <yz:fitem name="arg" type="hidden" val="$data['arg']"></yz:fitem>
        <script type="text/javascript">
            jQuery(function($){
                $("input[name='arg']").val("{{$data['arg']}}");
                $("#url").val("{{$data['arg']}}");
                $("#sellerGoods").val("{{$data['arg']}}");
                $("#serviceGoods").val("{{$data['arg']}}");
                $("#systemSellers").val("{{$data['arg']}}");
                $("#article").val("{{$data['arg']}}");
                $('#sellerCate').change(function() {
                    $("input[name='arg']").val($("select[name='sellerCate'] option:selected").val());
                });
                $('#sellerGoods').blur(function() {
                    $("input[name='arg']").val($("input[name='sellerGoods']").val());
                });
                $('#serviceGoods').blur(function() {
                    $("input[name='arg']").val($("input[name='serviceGoods']").val());
                });
                $('#systemSellers').blur(function() {
                    $("input[name='arg']").val($("input[name='systemSellers']").val());
                });
                $('#article').change(function() {
                    $("input[name='arg']").val($("select[name='article'] option:selected").val());
                });
                $('#url').blur(function() {
                    $("input[name='arg']").val($("input[name='url']").val());
                });
                var types = "{{$data['type']}}";
                if(types == 1){
                    $('#sellerCate-form-item').show();
                    $('#sellerGoods-form-item').hide();
                    $('#serviceGoods-form-item').hide();
                    $('#systemSellers-form-item').hide();
                    $('#article-form-item').hide();
                    $('#url-form-item').hide();
                }else if(types == 7){
                    $('#sellerCate-form-item').hide();
                    $('#sellerGoods-form-item').hide();
                    $('#serviceGoods-form-item').hide();
                    $('#systemSellers-form-item').hide();
                    $('#article-form-item').show();
                    $('#url-form-item').hide();
                    window.open("{!! u('Service/index') !!}");
                }else if(types == 3){
                    $('#sellerCate-form-item').hide();
                    $('#sellerGoods-form-item').show();
                    $('#serviceGoods-form-item').hide();
                    $('#systemSellers-form-item').hide();
                    $('#article-form-item').hide();
                    $('#url-form-item').hide();
                    window.open("{!! u('Service/index') !!}");
                }else if(types == 4){
                    $('#sellerCate-form-item').hide();
                    $('#sellerGoods-form-item').hide();
                    $('#serviceGoods-form-item').hide();
                    $('#systemSellers-form-item').show();
                    $('#article-form-item').hide();
                    $('#url-form-item').hide();
                    window.open("{!! u('Service/index') !!}");
                }else if(types == 5){
                    $('#sellerCate-form-item').hide();
                    $('#sellerGoods-form-item').hide();
                    $('#serviceGoods-form-item').hide();
                    $('#systemSellers-form-item').hide();
                    $('#article-form-item').hide();
                    $('#url-form-item').show();
                }else if(types == 6){
                    $('#sellerCate-form-item').hide();
                    $('#sellerGoods-form-item').hide();
                    $('#serviceGoods-form-item').show();
                    $('#systemSellers-form-item').hide();
                    $('#article-form-item').hide();
                    $('#url-form-item').hide();
                    window.open("{!! u('Service/index') !!}");
                }else {
                    $('#sellerCate-form-item').hide();
                    $('#sellerGoods-form-item').hide();
                    $('#serviceGoods-form-item').hide();
                    $('#systemSellers-form-item').hide();
                    $('#article-form-item').hide();
                    $('#url-form-item').hide();
                }
            });
        </script>

        <div id="sort-form-item" class="u-fitem clearfix ">
            <span class="f-tt">
                 排序:
            </span>
            <div class="f-boxr">
                <input type="text" name="sort" id="sort" class="u-ipttext my" style="width:80px;" value="{{$data['sort']}}">
            </div>
        </div>
        
        <yz:fitem label="状态">
            <php> $data['status'] = isset($data['status']) ? $data['status'] : 1; </php>
            <yz:radio name="status" options="1,0" texts="启用,禁用" checked="$data['status']"></yz:radio>
        </yz:fitem>

    </yz:form>
    @yizan_end
@stop
@section('js')
    <script type="text/javascript">
        $(document).on('keyup afterpaste', '.my', function(event) {
            var value = parseInt(this.value);
            if (isNaN(value)) {
                $(this).val('');
                return;
            }
            $(this).val(value);
        });
    </script>
@stop