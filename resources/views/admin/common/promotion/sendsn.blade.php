@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.ts{color: #999;margin-left: 5px;vertical-align:middle;}
	.form-box{font-size:14px; color:#333;}
	.form-box .form-group{margin-bottom:10px;}
	.form-box .form-group-title{color: #333; line-height:28px;}
	.form-box .form-group-title label{display:inline-block;}
	.form-box .form-group-title b{font-weight:normal;}
	.tip-title{padding:0 0 0 10px; color:#c19952; display: inline-block;}
	.block-tip{padding:3px 0 0 0; color:#c19952;}

	.form-group input[type="text"],.form-group input[type="password"]{padding: 6px 8px 4px 8px; line-height:normal; width:380px;color: rgb(133, 133, 133);background-color: rgb(255, 255, 255);border: 1px solid rgb(213, 213, 213);}
	.form-group textarea{border: 1px solid #ddd; padding:8px; height:3em; width:80%;}

	.form-btns{border-top:solid 1px #ccc; padding-top:20px;}
	.preview-form .form-btns{padding:20px 20px 20px 25px;}

	.double-selective{color: #999;text-align: center;line-height: 2;}
</style>
<?php $data['number'] = $data['number'] > 0 ? $data['number'] : 1; ?>
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="send">
            <yz:fitem name="promotionId" type="hidden" val="{{ Input::get('id') }}"></yz:fitem>
			<yz:fitem name="prefix" label="SN码前缀" append="1">
				<span class="ts">字母/数字,最多3位,可为空</span>
			</yz:fitem>

			<yz:fitem name="number" label="每人发放数量" append="1">
				<span class="ts">最大数量为10张</span>
			</yz:fitem>
			<yz:fitem label="选择发放会员" pcss="send-user-type send-user-group hidden">
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
		                        <select id="user_1" name="user_1" class="form-control" multiple="multiple" style="min-width:200px; height:260px;">
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
		                        <input type="hidden" name="userIds" id="user_fans_ids">
		                    </td>
		                    <td width="230" style="padding:0; height:35px;">
		                        <input type="text" class="form-control u-ipttext" placeholder="搜索会员" id="fansName" style="width:120px;">
		                        <a href="javascript:;" id="fansNameBtn" class="btn btn-success input-image-select">
		                        <i class="fa fa-search"></i> 搜索</a>
		                    </td>
		                </tr>
		                <tr>
		                    <td>
		                       <select id="user_2" class="form-control" multiple="multiple" style="min-width:200px; height:225px;">
	                            	<!-- <option value=""></option>    -->
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
				        $("#user_fans_ids").val(ids);
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
			
					$('.sendtype').change(function(){
						var type = $(".sendtype:checked").val();
						$('.send-user-type').addClass("hidden");
						if(type==2){
							$('.send-user-group').removeClass("hidden");
						}else if(type==3){
							$('.send-user-fans').removeClass("hidden");
						}
					});
 
		            $('#fansNameBtn').click(function(){
		                $.post("{{ u('Promotion/searchUser') }}",{"name":$("#fansName").val()},function(result){
		                    if(!result || result.length < 1){
		                        $('#user_2').html("<option value='0' disabled='true'>未搜索到相关会员</option>");
		                    } else {
		                        var html = '';
		                        $.each(result, function(index,e){
		                            html += " <option value=" + e.id + ">" + e.name + "</option>";
		                        });
		                        $('#user_2').html(html);
		                    }
		                },'json');
		            });  
				});
			</script>	
		</fitem> 
		</yz:form>
	@yizan_end
@stop
@section('js')

@stop
