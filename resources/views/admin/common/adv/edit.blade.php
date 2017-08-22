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

@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="update">
	<yz:fitem name="name" label="名称"></yz:fitem>
    @yizan_yield('adv_wapmodule')
    	<yz:fitem name="positionId" label="广告位编号">
    		<yz:select name="positionId" options="$positions" valuefield="id" textfield="name" selected="$data['position']['id']"></yz:select>
    	</yz:fitem>
    	<yz:fitem name="bgColor" label="背景颜色">
    		<yz:Color name="bgColor" val="{{$data['bgColor']}}"></yz:Color>
    	</yz:fitem>
    	<yz:fitem name="image" label="图片" type="image"></yz:fitem>
    		<yz:fitem name="type" label="广告类型">
    		<yz:select name="type" css="type" options="$type" textfield="name" valuefield="key" selected="$data['type']"></yz:select>
    	</yz:fitem>
    @yizan_stop
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
	<yz:fitem name="sort" label="排序"></yz:fitem>
	<yz:fitem name="status" label="状态">
		<yz:radio name="status" options="0,1" texts="禁用,启用" checked="$data['status']"></yz:radio>
	</yz:fitem>
</yz:form>
@yizan_end
@stop

