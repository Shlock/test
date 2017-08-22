@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.hscolor{color: #999;height: 28px;vertical-align: middle;}
	.setcity{margin-left: 110px;}
	.setcity_check{margin-left: 110px;margin-top: -30px;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
			<!-- @if(isset($data['id'])) -->
			<yz:fitem name="name" label="账号" type="text"></yz:fitem>
			<!-- @else -->
			<yz:fitem name="name" label="账号"></yz:fitem>
			<!-- @endif -->
			<yz:fitem label="密码">
				<input type="text" name="pwd" class="u-ipttext">
				<span class="hscolor">{{$data['ts']}}</span>
			</yz:fitem>
			<yz:fitem label="分组">
				<yz:select name="rid" options="$role" valuefield="id" textfield="name" selected="$data['rid']"></yz:select>
			</yz:fitem>
			<yz:fitem label="管理城市">
				<div class="input-group">
			    	<table border="0">
		                 <tbody>
		                 	<tr class="double-selective">
			                	<td>已选择</td>
			                	<td></td>
			                	<td>待选择</td>
			                </tr>
		                 	<tr>
			                    <td rowspan="2">
			                        <select id="user_1" name="user_1" class="form-control" multiple="multiple" style="min-width:200px;*width:200px; height:260px;">
			                        	<!-- @foreach( $data['city1'] as $key => $value ) -->
		                            		<option value="{{$value['id']}}">{{$value['name']}}</option>
		                            	<!-- @endforeach -->
			                        </select>
			                    </td>
			                    <td width="60" align="center" rowspan="2">
			                        <button type="button" class="btn btn-primary btn-sm" onclick="$.optionMove('user_2', 'user_1', 1);">
			                            <span class="fa fa-2x fa-angle-double-left"> </span>
			                        </button>
			                        <br><br>
			                        <button type="button" class="btn btn-info btn-sm" onclick="$.optionMove('user_2', 'user_1');">
			                            <span class="fa fa-2x fa-angle-left"> </span>
			                        </button>
			                        <br><br>
			                        <button type="button" class="btn btn-info btn-sm" onclick="$.optionMove('user_1', 'user_2');">
			                            <span class="fa fa-2x fa-angle-right"> </span>
			                        </button>
			                        <br><br>
			                        <button type="button" class="btn btn-primary btn-sm" onclick="$.optionMove('user_1', 'user_2', 1);">
			                            <span class="fa fa-2x fa-angle-double-right"> </span>
			                        </button>
			                        <input type="hidden" name="cityIds" id="cityIds">
			                    </td>
			                </tr>
			                <tr>
			                    <td>
			                       <select id="user_2" class="form-control" multiple="multiple" style="min-width:200px;*width:200px; height:260px;">
			                       		<!-- @foreach( $data['city2'] as $key => $value ) -->
		                            		<option value="{{$value['id']}}">{{$value['name']}}</option>
		                            	<!-- @endforeach -->
	                            	</select>
			                    </td>
			                </tr>
		            	</tbody>
	            	</table>
	            	<div class="blank3"></div>
	            </div> 
			</yz:fitem>
			<fitem type="script">
			<script type="text/javascript">
				jQuery(function($){
				    $("#yz_form").submit(function(){
				        var ids = new Array();
				        $("#user_1 option").each(function(){
				            ids.push(this.value);
				        })
				        $("#weixin_group_ids").val(ids);
			
				        ids = new Array();
				        $("#user_1 option").each(function(){
				            ids.push(this.value);
				        })
				        $("#cityIds").val(ids);
				    })
				    $.optionMove = function(from, to, isAll){
				        var from = $("#" + from);
				        var to = $("#" + to);
				        var list;
				        if(isAll){
				            list = $('option', from);
				        }else{
				            list = $('option:selected', from);
				        }
				        list.each(function(){
				            if($('option[value="' + this.value + '"]', to).length > 0){
				                $(this).remove();
				            } else {
				                $('option', to).attr('selected',false);
				                to.append(this);
				            }
				        });
				    } 
				});
			</script>	
		</fitem> 
		</yz:form>
	@yizan_end
@stop
@section('js')
@stop

