@extends('admin._layouts.base')
@section('css')

@stop
@section('right_content')
    @yizan_begin
        <yz:list>
			<search>
				<row>
					<item name="name" label="活动名称"></item>
                    <yz:fitem label="状态">
                        <yz:select name="status" options="0,1,2" texts="全部,启用,禁用" selected="$args['status']"></yz:select>
                    </yz:fitem>
                    <yz:fitem label="活动类型">
                        <yz:select name="status" options="0,1,2,3" texts="全部,分享活动,注册活动,线下优惠券发放活动" selected="$args['type']"></yz:select>
                    </yz:fitem>
				</row>
				<row>
                    <item name="beginTime" label="活动开始时间"></item>
                    <item name="endTime" label="活动结束时间"></item>
					<btn type="search"></btn>
				</row>
			</search>
            <btns>
                <linkbtn label="添加活动" url="{{ u('Activity/create') }}" css="btn-green"></linkbtn>
            </btns>
			<table relmodule="SystemGoods">
				<columns>
                    <column code="name" label="活动名称" width="100" iscut="1"></column>
                    <column label="活动类型" width="80">
                        @if($list_item['type'] == 1)
                            分享活动
                        @elseif($list_item['type'] == 2)
                            注册活动
                        @elseif($list_item['type'] == 2)
                            线下优惠券发放活动
                        @endif
                    </column>
                    <column label="创建时间" width="80" iscut="1">{{ yztime($list_item['createTime']) }}</column>
					<column label="活动时间" width="150">
                        {{ yztime($list_item['startTime'],'Y-m-d') }} 至 {{ yztime($list_item['endTime'],'Y-m-d') }}
					</column>
					<column label="活动状态" width="40">
						@if($list_item['status'] == 1)
							开启
						@else
							关闭
						@endif
					</column>

					<actions width="60">
					    <a href="{{ u('Activity/edit',['id'=>$list_item['id']]) }}" class=" blu" data-pk="1" target="_self">编辑</a>
                        @if($list_item['del'] < 1)
                                <action type="destroy" css="red"></action>
                        @endif
                    </actions>
                </columns>
			</table>
        </yz:list>
    @yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		//设置抢购价格
		$(document).on('keypress','.shopping_price',function(e){
			var key = e.which;
            if (key == 13) {
                e.preventDefault();
                var id = $(this).data('id');
                var price = $(this).val();
                $.post('{{ u("ShoppingSpree/setPrice") }}',{'id':id,'price':price},function(res){
					if(res.code == 0){
						window.location.reload();
					}else{
						$.ShowAlert(res.msg);
					}
                },"json");
            }
		});
		//通过一级分类查找二级分类
		$("#catePid").change(function(){
			var pid = $(this).val();
			$("#cateId").html("<option value>全部</option>");
			if(pid < 1){
				return false;
			}
			$.post("{{ u('Goods/selectSecond') }}",{'pid':pid,'status':1},function(res){
				if(res.length > 0){
					var html = "<option value>全部</option>";
					$.each(res, function(k,v){
						html += "<option value='"+this.id+"'>"+this.name+"</option>";
					});
					$("#cateId").html(html);
				}
			},'json');
		});
		
		$(document).on('click','.y-qghd a',function(){
			var type = $(this).data('type');
			var url = '{{ u("ShoppingSpree/setStaus") }}';
			$.post(url,{'type':type},function(res){
				if(res.code == 0){
					window.location.reload();
				}else{
					$.ShowAlert(res.msg);
				}
			},"json");
		});
	});
</script>
@stop
