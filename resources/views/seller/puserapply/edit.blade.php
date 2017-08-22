@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
<div class="p20">
		<div class="m-zjgltbg">
			<div class="p10">		
				<div class="m-quyu1">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">业主身份信息审核</span>
						<a href="{{ u('PuserApply/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
					</p>
					<div class="" style="padding-top:18px;"> 
						@yizan_begin
							<yz:form id="yz_form" action="update" nobtn="1">
								<yz:fitem name="build" label="楼栋号" type="text">
									<p>{{$data['build']['name']}}</p>
								</yz:fitem>
								<yz:fitem name="room" label="房间号" type="text">
									<p>{{$data['room']['roomNum']}}</p>
								</yz:fitem>
								<yz:fitem name="name" label="姓名" type="text">
									<p>{{$data['name']}}</p>
								</yz:fitem>
								<yz:fitem name="mobile" label="联系电话" type="text">
									<p>{{$data['mobile']}}</p>
								</yz:fitem> 
								@if($data['status'] == -1)
								<yz:fitem name="rejcontent" label="拒绝原因" type="text">
									<p>{{$data['content']}}</p>
								</yz:fitem> 
								@endif
								<div class="u-antt tc" style="background: #DFE0E0; height:auto;">
									<p class="f16 clearfix">
										<span class="ml15 fl" style="font-weight:bold;">楼栋对应房间信息</span>
									</p>
		                            <yz:fitem name="build" label="楼栋号" type="text">
										<p>{{$data['build']['name']}}</p>
									</yz:fitem>
									<yz:fitem name="room" label="房间号" type="text">
										<p>{{$data['room']['roomNum']}}</p>
									</yz:fitem>
									<yz:fitem name="build" label="业主" type="text">
										<p>{{$data['room']['owner']}}</p>
									</yz:fitem>
									<yz:fitem name="room" label="联系电话" type="text">
										<p>{{$data['room']['mobile']}}</p>
									</yz:fitem>
									<yz:fitem name="msg" label="核对信息" type="text">
										<p style="color:red;">{{ $checkmsg }}</p>
									</yz:fitem> 
		                        </div>
								@if($data['status'] == 0)
								<div class="u-antt tc" style=" background: #ffffff;">
		                            <a href="javascript:;" data-status="1" class="mt15 ml15 on m-sjglbcbtn dispose">审核通过</a>
		                            <a href="javascript:;" data-status="-1" class="mt15 ml15 on m-sjglbcbtn" onclick="PuserApply('-1','{{ $data['id'] }}' )">拒绝</a>
		                        </div>
								@endif
							</yz:form>		
						@yizan_end 
					</div>
				</div>
			</div>
		</div>
	</div>
<script type="text/tpl" id="PuserApply">
<div style="width:400px;text-align:center;margin:15px 0"> 
	<span class="msgs"style="display: block;text-align:center;margin:5px"></span>
	<textarea name='content' id='content' placeholder='请务必填写拒绝理由。' style="width:380px;height:100px;border:1px solid #EEE"></textarea> 
</div>
</script> 
<script type="text/javascript"> 
//提现处理
function PuserApply(status, id) {
    var dialog = $.zydialogs.open($("#PuserApply").html(), {
        boxid:'SET_GROUP_WEEBOX',
        width:300,
        title:'审核理由',
        showClose:true,
        showButton:true,
        showOk:true,
        showCancel:true,
        okBtnName: "确定",
		cancelBtnName: '取消',
        contentType:'content',
        onOk: function(){
     		dialog.setLoading();
            var query = new Object();
            query.id = id;
        	query.content = $("#content").val(); 
        	query.status = status;
    		if(query.content == ""){
              	$.ShowAlert("拒绝内容不能为空"); 
                //return false;
              	dialog.setLoading(false);
            }
        	$.post("{{ u('PuserApply/update')  }}",query,function(result){  
                dialog.setLoading(false);  
            	if(result.code == 0){ 
               	 	window.location.href="{{ u('PuserApply/index') }}";
            	}else{
                	$.ShowAlert(result.msg);
                	$.zydialogs.close("SET_GROUP_WEEBOX");
                }
            },'json');  
        }, 
		onCancel:function(){
            $.zydialogs.close("SET_GROUP_WEEBOX");
        }  
	});	
}
	$(function() {
        $('.dispose').click(function() {
            var status = $(this).data('status');
            var id = "{{ $data['id'] }}";
            $.post("{{ u('PuserApply/update')  }}",{id:id,status:status},function(result){   
                if(result.code == 0){ 
                	window.location.href="{{ u('PuserApply/index') }}";
                   // window.location.href="{{ u('PropertyFee/create') }}";
                }else{
                    $.ShowAlert(result.msg);
                }
            },'json');
        })
    })
</script>
@stop 


