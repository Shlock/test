@extends('admin._layouts.base')
@section('css')
<style type="text/css"> 
.f-bhtt{ border: 1px solid #ccc; background: #f9f9f9; line-height: 43px;}
.x-lrul{width: 50px; height: 28px; padding: 1px; border-radius: 20px; background: #4cd964; position: absolute;top: 8px;left: 88px;}
.x-lrul li{width: 22px; height: 28px; float: left; cursor: pointer;}
.x-lrul li span{position: absolute;top: 6px;margin-left: 7px;display: none;}
.x-lrul .on{width: 28px; height: 28px; background: #fff; border-radius: 100%;}

</style>
@stop
@section('right_content')
	<div>
		<div class="m-zjgltbg">
			<div class="p10">
				<!-- 订单管理 -->
				<div class="g-fwgl" style="position:relative">
					<p class="f-bhtt f14 clearfix" style="border-bottom:0;">
						<span class="ml15 fl">审核设置：
							<input type="radio" class="condig_radio" name="config_box" value="0" @if($posts_config == 0) checked="checked" @endif >关闭
							<input type="radio" class="condig_radio" name="config_box" value="1" @if($posts_config == 1) checked="checked" @endif >开启
							<!-- <ul class="x-lrul">
								<li class="@if($posts_config == 0) on @endif">
									<span class="close" >关</span>
								</li>
								<li class="@if($posts_config == 1) on @endif">
									<span class="open"  >开</span>
								</li>
							</ul> -->
						</span>
					</p>
				</div>
				<div id="checkList" class="">
                    <div class="tabs">
						<div class="tab-navs u-spnav u-orderlstnav">
							<ul class="clearfix"> 
								<li class="tab-nav @if($type == 1) on @endif">
		    						<a href="{{u('PostsCheck/index', ['type'=>1])}}" target="_self">未审核</a>
		    					</li>
		                        <li class="tab-nav @if($type == 2) on @endif">
				    				<a href="{{u('PostsCheck/index',['type'=>2])}}" target="_self">已拒绝</a>
				    			</li>
							</ul>
						</div>
					</div> 
                    <div class="m-tab"> 
	                    @if($type == 1)
                    	<div style="margin-top:5px;margin-bottom:5px">
                    		<input id="check_all" type="checkbox" value="1">全选
                    	</div>
                    	@endif
                      	<table id="checkListTable" class=""> 
	                    	<tbody>
	                    	@foreach($list as $key => $item)
	                    	<php> 
	                    	if(($key+1)%2 == 0){
	                    		$css = 'even';
	                    	} else {
	                    		$css = 'odd';
	                    	}
	                    	</php>
	                    	@if($type == 1)
                            <tr class="tr-5 tr-{{$css}}" key="{{$item['id']}}" >
	                            <td class="" code="sn" width="40">
	                            	<input class="check_item" type="checkbox" value="{{$item['id']}}"> 
	                            </td> 
		    					<td class="" code="">
		    						<table style="border:none; margin:none;padding:0px; ">
		    							<tr>
		    								<td>
		    									@if($item['pid'] == 0) {{$item['title']}} @endif
		    								</td>
		    								<td>
		    									{{$item['plate']['name']}}
		    								</td>
		    								<td>
		    									{{$item['user']['name']}}
		    									<br>
		    									{{$item['createTime']}}
		    								</td>
		    								<td>
		    									<a href="{{u('ForumPosts/detail',['id'=>$item['id']])}}">查看</a>
		    									<a href="{{u('ForumPosts/edit',['id'=>$item['id']])}}">编辑</a>
		    								</td>
		    							</tr>
		    							<tr>
		    								<td colspan="4"> 
		    									{{$item['content']}}
		    								</td>
		    							</tr>
		    							<tr text-align="left">
		    								<td colspan="4" >  
		    									<a data-val="1" data-id="{{$item['id']}}" class="updateStatus" href="javascript:;" >同意</a>
		    									<a data-val="-1" data-id="{{$item['id']}}" class="updateStatus" href="javascript:;" >拒绝</a>
		    								</td>
		    							</tr>
		    						</table>
		    					</td> 
		                    </tr> 
		                    @else
		                    <tr class="tr-5 tr-{{$css}}" key="{{$item['id']}}" > 
		    					<td class="" code="">
		    						<table style="border:none; margin:none;padding:0px; ">
		    							<tr>
		    								<td>
		    									@if($item['pid'] == 0) {{$item['title']}} @endif
		    								</td>
		    								<td>
		    									{{$item['plate']['name']}}
		    								</td>
		    								<td>
		    									{{$item['user']['name']}}
		    									<br>
		    									{{ yztime($item['createTime'])}}
		    								</td>
		    								<td>
		    									<a>查看</a>
		    									<a>编辑</a>
		    								</td>
		    							</tr>
		    							<tr>
		    								<td colspan="4"> 
		    									{{$item['content']}}
		    								</td>
		    							</tr> 
		    						</table>
		    					</td> 
		                    </tr> 	
		                    @endif
		                    @endforeach
                            </tbody>
	                    </table>
	                    @if($type == 1)
	                    <div style="margin-top:5px;margin-bottom:5px">
	                    	<button data-val="1" class="btn all_do">全部通过</button>
	                    	<button data-val="-1" class="btn all_do">全部拒绝</button>
	                    </div>
	                    @endif
	                </div>
        			@include('admin._layouts.pager')
                </div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
    	$(function(){
    		$(".condig_radio").click(function(){
    			var obj = new Object();
    			obj.code = 'posts_check';
    			obj.val = $(this).val();
    			$.post("{{u('ForumPosts/postsConfig')}}", obj, function(){},'json');
    		});
    		$(".updateStatus").click(function(){
    			var obj = new Object();
    			obj.status = $(this).data('val');
    			obj.id = $(this).data('id');
    			obj.field = 'is_check';
    			$.post("{{u('ForumPosts/updateStatus')}}", obj, function(res){
    				if(res.code > 0){
    					$.ShowAlert('处理失败');
    				} else {
    					$.ShowAlert('成功');
    					window.location.reload();
    				}
    			},'json');
    		}) 
    		$("#check_all").click(function(){
    			var bln = $(this).is(':checked');
    			$(".check_item").each(function(){
    				if(bln){
						$(this).parent().addClass("checked");
					}else{
						$(this).parent().removeClass("checked");
					}
					this.checked = bln;
    			});
    		})
    		$(".all_do").click(function(){
    			var obj = new Object();
    			obj.status = $(this).data('val');
    			var id = new Array();
    			$(".check_item").each(function(){
    				if(this.checked){
    					id.push($(this).val());
    				}
    			});
    			obj.id = id;
    			obj.field = 'is_check';
    			$.post("{{u('ForumPosts/updateStatus')}}", obj, function(res){
    				if(res.code > 0){
    					$.ShowAlert('处理失败');
    				} else {
    					$.ShowAlert('成功');
    					window.location.reload();
    				}
    			},'json');
    		})
    	});
    </script>
@stop