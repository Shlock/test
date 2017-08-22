@yizan_include('admin.common.personnel.index')

@yizan_section('header')
<header label="人员信息" width="110"></header>
<header label="机构信息" width="150"></header>
<header label="机构证书"></header>
@yizan_stop

@yizan_section('list_item')
<td style="text-align:left">
	<p>{{ $list_item['seller']['name'] }}</p>
	<p>{{ $list_item['seller']['mobile'] }}</p>
</td>
<td style="text-align:left">
	<p>{{ $list_item['companyName'] }}</p>
	<p>{{ $list_item['businessLicenceSn'] }}</p>
</td>
<td style="text-align:left">
	<a href="{{ $list_item['businessLicenceImg'] }}" target="_blank"><img src="{{ formatImage($list_item['businessLicenceImg'],0,40,0) }}" alt="" height="40"></a> 
</td>
@yizan_stop