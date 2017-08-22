@extends('admin._layouts.base')
@section('right_content')

@yizan_begin

<!--$id 		 = !isset($attrs['id']) ? 'checkList' : $attrs['id'];
$relmodule 	 = !isset($attrs['relmodule']) ? '' : ' relmodule = "'.$attrs['relmodule'].'"';
$datasource  = !isset($attrs['datasource']) ? 'list' : $attrs['datasource']; //列表显示的数据源
$pk 		 = !isset($attrs['pk']) ? 'id' : $attrs['pk'];//主键名，默认为id
$checkbox 	 = !isset($attrs['checkbox']) ? 0 : (int)$attrs['checkbox'];
$actionwidth = !isset($attrs['actionwidth']) ? '' : ' width="'.$attrs['actionwidth'].'"';
$pager 		 = !isset($attrs['pager']) ? 'pager' : ($attrs['pager'] == 'no' ? '' : $attrs['pager']);

list:id, pk:数据主键(默认id), datasource:数据源(默认$list)
search:同yz:form参数
item:同yz:fitem
btn:同yz:btn
linkbtn:同yz:linkbtn
table:id,pager:定页分页调用模板(默认显示pager,no:为不显示),checkbox:是否显示选择框
column:code:数据字段, label:列名称, width,列宽度, align:对齐方式,type:格式化类型, order:排序字段(默认同code),sort:排序方式(asc|desc)
actions:width,操作列宽度, align:对齐方式
action:code:label:操作名称, type:操作类型, url:链接地址,click:点击事件(优先级高于url),target:打开方式默认为_self,tip:操作提示
type:edit 编辑, destroy 删除-->

<yz:radio name="radio1" options="1,2,3" texts="a,b,c" checked="2"></yz:radio>
<br/>

<php>
$texts = ['a','b','c'];
</php>
<yz:radio name="radio2" options="1,2,3" texts="$texts" checked="2"></yz:radio>
<br/>

<php>
$options = [1,2,3];
$texts = ['a','b','c'];
$val = 3;
</php>
<yz:radio name="radio3" options="$options" texts="$texts" checked="$val"></yz:radio>
<br/>

<php>
$options = [
		['name' => 'a','val'  => 1],
		['name' => 'b','val'  => 2],
		['name' => 'c','val'  => 3],
	];
$check = 1;
</php>
<yz:radio name="radio4" options="$options" textfield="name" valuefield="val" checked="$check"></yz:radio>
<br/>

<php>
$options = ['a','b','c'];
$check = 1;
</php>
<yz:radio name="radio5" options="$options" checked="$check"></yz:radio>
<br/>

=====================================================
<br/>

<yz:checkbox name="checkbox1" options="1,2,3" texts="a,b,c" checked="2"></yz:checkbox>
<br/>

<php>
$texts = ['a','b','c'];
</php>
<yz:checkbox name="checkbox2" options="1,2,3" texts="$texts" checked="2"></yz:checkbox>
<br/>

<php>
$options = [1,2,3];
$texts = ['a','b','c'];
$val = 3;
</php>
<yz:checkbox name="checkbox3" options="$options" texts="$texts" checked="$val"></yz:checkbox>
<br/>

<php>
$options = [
		['name' => 'a','val'  => 1],
		['name' => 'b','val'  => 2],
		['name' => 'c','val'  => 3],
	];
$check = 1;
</php>
<yz:checkbox name="checkbox4" options="$options" textfield="name" valuefield="val" checked="$check"></yz:checkbox>
<br/>

<php>
$options = ['a','b','c'];
$check = 1;
</php>
<yz:checkbox name="checkbox5" options="$options" checked="$check"></yz:checkbox>
<br/>

=====================================================
<br/>

<yz:select name="select1" options="1,2,3" texts="a,b,c" selected="2"></yz:select>
<br/>

<php>
$texts = ['a','b','c'];
</php>
<yz:select name="select2" options="1,2,3" texts="$texts" selected="2"></yz:select>
<br/>

<php>
$options = [1,2,3];
$texts = ['a','b','c'];
$val = 3;
</php>
<yz:select name="select3" options="$options" texts="$texts" selected="$val"></yz:select>
<br/>

<php>
$options = [
		['name' => 'a','val'  => 1],
		['name' => 'b','val'  => 2],
		['name' => 'c','val'  => 3],
	];
$check = [1,3];
</php>
<yz:select name="select4" options="$options" textfield="name" valuefield="val" selected="$check" multiple="1" size="5"></yz:select>
<br/>

<php>
$options = ['a','b','c'];
$check = 1;
</php>
<yz:select name="select5" options="$options" selected="$check"></yz:select>
<br/>

@yizan_end

@stop