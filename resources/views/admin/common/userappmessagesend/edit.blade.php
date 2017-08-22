@extends('admin._layouts.base')
@section('right_content')
<style type="text/css">
	
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
.hidden{display: none;}
</style>
@yizan_begin
	<yz:form id="yz_form" action="send">   
		<yz:fitem name="title" label="标题"></yz:fitem> 
		<yz:fitem name="content" label="内容" type="textarea"></yz:fitem>   
		<yz:fitem name="userType" label="推送会员类型">
			<yz:Radio name="userType"  css="userType" options="0,1" texts="所有人,指定会员" checked="0"></yz:Radio>
		</yz:fitem>
		<yz:fitem label="选择会员" pcss="send-user-type send-user-group hidden">
		    <div class="input-group">
		    	<table border="0">
	                 <tbody>
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
		                        <input type="hidden" name="users" id="users">
		                    </td>
		                    <td width="230" style="padding:0; height:35px;">
		                        <input type="text" class="form-control" placeholder="搜索手机号" id="fansName" style="width:120px;">
		                        <a href="javascript:;" id="fansNameBtn" class="btn btn-success input-image-select">
		                        <i class="fa fa-search"></i> 搜索</a>
		                    </td>
		                </tr>
		                <tr>
		                    <td>
		                       <select id="user_2" class="form-control" multiple="multiple" style="min-width:200px; height:220px;"> 
                            	</select>
		                    </td>
		                </tr>
	            	</tbody>
            	</table>
            	<div class="blank3"></div>
            </div> 
		</yz:fitem> 
		
		<yz:fitem name="sendType" label="推送类型" tip="html页面，推送参数为url">
			<yz:Radio name="sendType"  css="sendType" options="1,2" texts="普通消息,html页面" checked="1"></yz:Radio>
		</yz:fitem>
		<yz:fitem name="args" label="推送参数" ></yz:fitem> 
		<fitem type="script">
		<script type="text/javascript">
			jQuery(function($){
			    $("#yz_form").submit(function(){
			        var ids = new Array(); 
			        $("#user_1 option").each(function(){
			            ids.push(this.value);
			        })
			        $("#users").val(ids);
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
		
				$('input[name="userType"]').change(function(){ 
					var type = $("input[name='userType']:checked").val(); 
					if(type==0){
						$('.send-user-group').addClass("hidden");
					}else{
						$('.send-user-group').removeClass("hidden");
					}
				});

	            $('#fansNameBtn').click(function(){
	                $.post("{{ u('User/search') }}",{"name":$("#fansName").val()},function(result){  
	                    if(!result || result.length < 1){ 
	                        $('#user_2').html("<option value='0' disabled='true'>未搜索到会员用户</option>");
	                    } else {
	                        var html = '';
	                        $.each(result, function(index,e){ 
	                            html += " <option value=" + e.id + ">" + e.name + "</option>";
	                        });
	                        $('#user_2').html(html);
	                    }
	                },'json');
	            });  

	            $("input[name=sendType]").change(function() {
	            	/* Act on the event */
	            	var type = $("input[name='sendType']:checked").val(); 
					if(type==1){
						$('#args-form-item').addClass("hidden");
					}else{
						$('#args-form-item').removeClass("hidden");
					}
	            });
	            $('#args-form-item').addClass("hidden");
			});
		</script>	
		</fitem> 
	</yz:form>
@foreach ($errors->all() as $error)
    <p class="error">{{ $error }}</p>
 @endforeach 
@yizan_end

@stop