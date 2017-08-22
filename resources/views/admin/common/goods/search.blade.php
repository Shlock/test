@yizan_begin
	<yz:list>
		<search ajax="1"> 
			<row>
				<item name="name" label="服务名称"></item>
				<item name="SellerName" label="机构信息"></item>
				<item label="服务分类">
					<yz:select name="cateId" options="$cate" textfield="levelname" valuefield="id" attr="style='min-width:100px;'" selected="$search_args['cateId']">
					</yz:select>
				</item> 
				<btn type="search" style="margin-bottom:10px"></btn>
			</row>
		</search>
		<table pager="ajaxpager">
			<columns>
				<column code="name" label="服务名称" align="left"></column>
				<column label="机构信息">
					{{ $list_item['seller']['name'] }}
				</column>
				<column code="priceType" label="价格类型">
					{{ Lang::get('admin.PriceType.'.$list_item['priceType']) }}
				</column>
				<column code="price" label="价格"></column>
				<column code="marketPrice" label="市场价格"></column>
				<column label="时长">
					{{ $list_item['duration'] / 3600 }}
					<textarea style="display:none" id="goods-json-{{ $list_item['id'] }}">{{ json_encode($list_item) }}</textarea>
				</column>
				<actions>
					<action label="选择">
						<attrs>
							<click>$.selectedGoods({{ $list_item['id'] }});</click>
						</attrs>
					</action>
				</actions>
			</columns>
		</table>
	</yz:list>
@yizan_end