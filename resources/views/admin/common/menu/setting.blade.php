@extends('admin._layouts.base')
@section('css')
<style type="text/css">
.form-tip{padding: 10px 0px;margin-bottom: 10px;border: 1px solid #eee;}
.form-tip div.form-title{padding:5px 0px 5px 43px;font-size: 18px;margin-top: -10px;margin-bottom: 10px;font-weight: bold;background-color: #F4F6F9;}
.y-qghd{border:1px solid #ededed;background:#f9f9f9;padding:10px 1em;margin-bottom:10px;}
.y-start{position: relative;border:1px solid #ededed;background:#fff;padding:10px 10px;}
.y-qghdbtn{position: absolute;top:9px;left:1em;border-radius:3px;overflow: hidden;}
.y-qghdbtn a{float:left;border:1px solid #ededed;border-width:1px 1px 1px 0;width:35px;line-height:20px;font-size:12px;display:inline-block;text-align: center;}
.y-qghdbtn a:first-child{border-width:1px 0 1px 1px;}
.y-qghdbtn a:hover{color:#313233;}
.y-qghdbtn a.on{border:1px solid #00BB00;background:#00BB00;color:#fff;}
.y-start .y-qghdmain{color:#979797;margin-left:90px;line-height:20px;}
</style>
@stop
@section('right_content')
	@yizan_begin
	   <div class="y-qghd">
	       <h3 class="f14">抢购活动</h3>
	       <div class="y-start mt10">
	           <div class="y-qghdbtn">
	               <a @if($data['status'] == 1) class="on" @endif href="javascript:;" data-type="open">开启</a>
	               <a @if($data['status'] == 0) class="on" @endif href="javascript:;" data-type="close">关闭</a>
	           </div>
	           <div class="f12 y-qghdmain">关闭抢购活动时，可添加显示抢购服务，当开启抢购活动时将不允许在添加任何服务</div>
	       </div>
	   </div>
	   <div @if($data['status'] == 0) style="display:none;"@endif>
		<yz:list>
			<tabs>
				<navs>
					<nav label="抢购活动">
						<attrs>
							<url>{{ u('ShoppingSpree/index') }}</url>
							<css> @if(ACTION_NAME == 'index') on @endif </css>
						</attrs>
					</nav>
					<nav label="抢购设置">
						<attrs>
							<url>{{ u('ShoppingSpree/setting') }}</url>
							<css> @if(ACTION_NAME == 'setting') on @endif </css>
						</attrs>
					</nav>
				</navs>
			</tabs>
		</yz:list>
		
		<yz:form id="yz_form" action="save">
    		<div class="form-tip">
				<yz:fitem name="name" label="活动名称" ></yz:fitem>
				<yz:fitem name="starttime" label="抢购开始时间" type="date"></yz:fitem>
				<yz:fitem name="endtime" label="抢购结束时间" type="date"></yz:fitem>
				<yz:fitem name="image" label="抢购宣传图" val="{{$image}}" type="image" tip="活动宣传图,尺寸590*260"></yz:fitem>
    		</div>
    	</yz:form>
		</div>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
$(document).on('click','.y-qghd a',function(){
	var type = $(this).data('type');
	var url = '{{ u("ShoppingSpree/setStaus") }}';
	$.post(url,{'type':type},function(res){
		if(res.code == 0){
			window.location.reload();
		}else{
			$.ShowAlert(res.msg);
		}
	},"json");
});
</script>
@stop
