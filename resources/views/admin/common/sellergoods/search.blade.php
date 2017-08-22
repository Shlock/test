@yizan_begin
	<yz:list>
		<search ajax="1"> 
			<row>
				<item name="name" label="服务名称"></item>
				<item name="sellerName" label="服务人员"></item>
				<item label="分类">
					<yz:select name="cateId" options="$cate" textfield="levelname" valuefield="id" attr="style='min-width:160px;width:auto'" selected="$search_args['cateId']">
					</yz:select>
				</item> 
				<btn type="search"></btn>
			</row>
		</search>
		<table pager="ajaxpager">
			<columns>
				<column code="name" label="服务名称" align="left"></column>
				<column label="服务人员">
					{{ $list_item['seller']['name'] }}
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