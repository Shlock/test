@yizan_include('admin.common.adv.index')

@yizan_section('adv_wapmodule')
	<column code="id" label="编号" width="10" ></column>  
	<column code="image" label="模块图片" type="image" width="50">
		<php>
			$color = empty($list_item['color']) ? '#ccc' : $list_item['color'];
		</php>
		<a href="{{ $list_item['image'] }}" target="_blank" style="display:block; max-width:100px; max-height:100px;border:solid 1px {{ $color }}; background:#ccc;">
			<img src="{{ formatImage($list_item['image'],60,60) }}"/>
		</a>
	</column>
	<column code="name" label="模块名称" width="100" align="center"></column>
	<column code="name" label="关联分类" width="100" align="center">		
	{{ Lang::get('admin.relevancetype.'.$list_item['type']) }}
	</column>
	<column code="sort" width="50" label="排序"></column>
@yizan_stop