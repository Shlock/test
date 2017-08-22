@extends('seller._layouts.base')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.tagsinput.css') }}">
<style type="text/css">
	#cateSave{display: none;}
	.page_2,.page_3{display: none;}
	.m-spboxlst li{margin-bottom: 0px;}
	#tags_goods-form-item .f-boxr {width:550px;}
</style> 
@stop 
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 添加服务 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">添加服务</span>
					</p>
				</div>
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser pt20">
					@yizan_begin
	                    <yz:form id="yz_form" action="save"> 

							<dl class="m-ddl">
								<dt>服务信息设置</dt>
								<dd class="clearfix" style="padding:15px;"> 
							<yz:fitem name="name" label="服务标题"></yz:fitem> 
							<yz:fitem label="服务分类">
								<yz:select name="cateId" options="$cate" textfield="name" valuefield="id" selected="$data['cate']['id']"></yz:select>
							</yz:fitem>
							<yz:fitem name="price" label="价格"></yz:fitem> 
							<yz:fitem name="duration" label="服务时长"></yz:fitem> 
							<yz:fitem label="单位"> 
								<php> $unit = (int)$data['unit'] </php>
								<yz:radio name="unit" options="0,1" texts="分钟,小时" checked="$unit"></yz:radio>
							</yz:fitem>
							<yz:fitem label="服务图片">
								<yz:imageList name="images." images="$data['images']"></yz:imageList>
							</yz:fitem>
                                    <yz:fitem name="brief" label="服务描述">
                                        <yz:Editor name="brief" value="{{ $data['brief'] }}"></yz:Editor>
                                    </yz:fitem>
							<yz:fitem label="选择员工" pcss="send-user-type send-user-group hidden">
							    <div class="input-group">
							    	<table border="0" style="width:70%;margin-left:104px;">
						                 <tbody>
						                 	<tr>
							                    <td rowspan="2">
							                        <select id="user_1" name="staffIds" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:260px;">
							                        @foreach($data['staffIds'] as $item)
													<option value="{{$item['id']}}" >{{$item['name']}}</option>
							                        @endforeach
							                        </select>
							                    </td>
							                    <td width="60" align="center" rowspan="2">
							                        <button type="button" class="btn btn-gray" onclick="$.optionMove('user_2', 'user_1', 1);">
							                            <span class="fa fa-2x fa-angle-double-left"> </span>
							                        </button>
							                        <br><br>
							                        <button type="button" class="btn btn-gray" onclick="$.optionMove('user_2', 'user_1');">
							                            <span class="fa fa-2x fa-angle-left"> </span>
							                        </button>
							                        <br><br>
							                        <button type="button" class="btn btn-gray" onclick="$.optionMove('user_1', 'user_2');">
							                            <span class="fa fa-2x fa-angle-right"> </span>
							                        </button>
							                        <br><br>
							                        <button type="button" class="btn btn-gray" onclick="$.optionMove('user_1', 'user_2', 1);">
							                            <span class="fa fa-2x fa-angle-double-right"> </span>
							                        </button>
							                        <input type="hidden" name="staffIds" id="users">
							                    </td>
							                    <td width="230" style="padding:0; height:35px;">
							                        <input type="text" class="u-ipttext" placeholder="搜索员工" id="fansName" style="width:140px;hieght:30px;">
							                        <a href="javascript:;" id="fansNameBtn" class="btn btn-gray btn-success input-image-select">
							                        <i class="fa fa-search"></i></a>
							                    </td>
							                </tr>
							                <tr>
							                    <td>
							                       <select id="user_2" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:220px;"> 
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
										var u_id = new Array(); 
						                $.post("{{ u('Seller/search') }}",{"name":$("#fansName").val()},function(result){  
						                    if(!result || result.length < 1){ 
						                        $('#user_2').html("<option value='0' disabled='true'>未搜索到员工</option>");
						                    } else {
						                        var html = '';
												$("#user_1 option").each(function(){
													u_id.push(this.value);
												})
						                        $.each(result, function(index,e){
													console.log(u_id.indexOf(result[index].id));
						                        	if (u_id.indexOf(result[index].id) == -1){
														html += " <option class='uid" + e.id + "' value=" + e.id + ">" + e.name + "</option>";													
													}
						                        });
						                        $('#user_2').html(html);
						                    }
						                },'json');
						            }); 
						            $('#fansNameBtn').click();

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
							<!-- <yz:fitem label="人员佣金方式"> 
								<php> $deductType = (int)$data['deductType'] </php>
								<yz:radio name="deductType" options="1,2" texts="固定佣金,百分比" checked="$deductType"></yz:radio>
							</yz:fitem>
							<yz:fitem name="deductVal" label="人员佣金值"></yz:fitem>  -->
							<yz:fitem label="服务状态"> 
								<php> $status = (int)$data['status'] </php>
								<yz:radio name="status" options="0,1" texts="下架,上架" checked="$status"></yz:radio>
							</yz:fitem>
							<yz:fitem name="sort" label="排序"></yz:fitem> 
								</dd> 
							</dl>
							@if($data['id'] > 0)
							<dl class="m-ddl">
								<dt>营业信息设置</dt>
								<dd class="clearfix" style="padding:15px;"> 
									@include('seller.common.service.showtime') 
						            @include('seller.common.service.sztime') 
								</dd> 
							</dl>
							@endif
						</yz:form>
	                @yizan_end
				</div>
			</div>
		</div>
	</div>
@stop
@section('js')
@include('seller._layouts.alert')
<script src="{{ asset('js/jquery.tagsinput.min.js') }}"></script>  
<script type="text/javascript">
$(function() {
	// 添加时间
    $(".m-sjglbcbtn").click(function(){
        $('.msg').text("");
        var week = new Array();
        var hour = new Array();
        var hr = new Array();
        $(".m-zhouct label input:checked").each(function(){
            week.push($(this).val());   
            $('.msg').append( $(this).parents("label").text() );
        });
        $(".m-sjdct ul li").each(function(){
            if($(this).hasClass("on")){
                hour.push($(this).find('span').data("hours"));
                hr.push($(this).text());    
            }
        });
        if(week==''){
            $.ShowAlert('你还没有选择星期几');
            return false;
        }
        if(hour==''){
            $.ShowAlert('你还没有选择预约时间');
            return false;
        }
        var goodsId = $("input[name=id]").val();
        obj = new Object();
        obj.weeks = week;
        obj.hours = hour;
        obj.goodsId = goodsId;
        var msg = $('.msg').text();
        if($(this).text() == "更新"){
                obj.id = $(".data-id").text();
                $.post(szurl,obj,function(result){  
                    if(result.status == true){
                        $(".u-timshow .u-czct span").each(function(){
                            if($(this).data('mid') == obj.id){
                                htmls = '<div class="u-timshow por"><div class="updatetime"><p>'+hr+'</p><p class="gray">'+msg+'</p><p class="grays" style="display:none;">'+week+', </p></div><div class="u-czct"><span data-id="'+obj.id+'" class="mr15 f-edit f-edit'+obj.id+'"><a href="javascript:;" class="fa fa-edit f14"></a>编辑</span><span data-id="'+obj.id+'" data-mid="'+obj.id+'" data-css="m-timlst'+obj.id+'" class="f-delet"><a href="javascript:;" class="fa fa-trash f14 dels" ></a>删除</span></div></div>';
                                $(this).parents(".m-timlst").html(htmls);
                            }
                            $('.m-bjtimbox').slideUp();
                        });
                    $(".m-timebtn").removeClass("none");
                    alod ();
                    }else{
                        $.ShowAlert(result.msg);
                    }
                },'json'); 
            }else{
                $.post(adddatatime,obj,function(result){  
                    if(result.status == true){
                        $(".m-sjdct ul li").each(function(){
                            $(this).removeClass("on");
                        });
                        $(".m-zhouct label input:checked").each(function(){
                            $(this).removeAttr("checked");
                            $(this).attr("disabled","true");
                        });
                        $.ShowAlert(result.msg);
                        $.post(gettimes,{id:obj.goodsId},function(res){
                            var htmls = "";
                            if(res.code == 0){                  
                                $.each(res.data,function(i,v){
                                    htmls = '<div class="m-timlst m-timlst'+v.id+'"><div class="u-timshow por"><div class="updatetime"><p>'+v.times+'</p><p class="gray">'+v.weeks+'</p><p class="grays" style="display:none;">'+week+', </p></div><div class="u-czct"><span data-id="'+v.id+'" class="mr15 f-edit f-edit'+v.id+'"><a href="javascript:;" class="fa fa-edit f14"></a>编辑</span><span data-id="'+v.id+'" data-mid="'+v.id+'" data-css="m-timlst'+v.id+'" class="f-delet"><a href="javascript:;" class="fa fa-trash f14 dels" ></a>删除</span></div></div></div>';
                                });
                            }
                            $('.g-tmlstzct').append(htmls);
                            alod ();
                        });
                        $('.m-bjtimbox').slideUp();
                        $(".m-timebtn").removeClass("none");
                    }else{
                        $.ShowAlert(result.msg);
                    }
                },'json'); 
            }
    });
})
	
</script>

@stop
