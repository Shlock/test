@extends('admin._layouts.base')
@section('css') 
@stop
@section('right_content')
	@yizan_begin 
		<yz:form id="yz_form" action="save"> 
			<yz:fitem name="name" label="小区名称"></yz:fitem> 
			<yz:fitem name="provinceId" label="所在地区">
				<yz:region pname="provinceId" pval="$data['province']['id']" cname="cityId" cval="$data['city']['id']" aname="areaId" aval="$data['area']['id']"></yz:region>
			</yz:fitem> 
			<yz:fitem name="address" label="详细地址">
				<yz:map name="address" pointVal="$data['mapPointStr']" addressVal="$data['address']" ></yz:map>
			</yz:fitem>
			<yz:fitem name="houseNum" label="户数"></yz:fitem> 
			<yz:fitem name="areaNum" label="面积" tip="平方米"></yz:fitem>   
			<php>
				$houseData = Lang::get('api.house_type');
				$houseTypes = [];
				foreach($houseData as $key => $value){
					$arr = [];
					$arr['id'] = $key;
					$arr['name'] = $value;
					$houseTypes[] = $arr; 
				}
			</php>
			<yz:fitem label="房产类型">
				<yz:select name="houseType" options="$houseTypes" textfield="name" valuefield="id" selected="$data['houseType']"></yz:select>
			</yz:fitem>
			<yz:fitem name="departTel" label="联系电话"></yz:fitem> 
			<yz:fitem name="departMail" label="电子邮箱"></yz:fitem> 
			<yz:fitem name="departStreet" label="街道/村/门牌号"></yz:fitem>  
			<yz:fitem name="departCommon" label="说明"></yz:fitem> 
		</yz:form> 
	@yizan_end
	<script type="text/javascript"> 
	</script>
@stop