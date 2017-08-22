@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.ts{color: #999;margin-left: 5px;vertical-align:middle;}
</style>
@stop
@section('right_content')
<?php 

	$cleaningtype = [
		['type'=>'offset','name'=>'抵用券'],
		['type'=>'money','name'=>'优惠券']
	];

	$timetype = [
		['id'=>1,'name'=>'使用有效期'],
		['id'=>2,'name'=>'固定有效期'],
		['id'=>3,'name'=>'永久']
	];
 ?>
	@yizan_begin
		<yz:form id="yz_form" action="saveCarTicket">
			<yz:fitem name="name" label="优惠券名称" append="1">
				<p class="ts">注：设置优惠券面值、适用范围、优惠券类型后优惠券名称自动生成</p>
			</yz:fitem>

			<yz:fitem label="优惠券类型">
				<php> $data['type'] = isset($data['type']) ? $data['type'] : 'money'; </php>
				<yz:radio name="type" options="$cleaningtype" valuefield="type" css="send-type" textfield="name" checked="$data['type']"></yz:radio>
				<p class="ts">注：服务抵用券即使用后服务售价为0,服务优惠券即减免相应面值的服务费用</p>
			</yz:fitem>

			<yz:fitem name="data" label="优惠券面值" append="1">
				<span class="ts">元</span>
			</yz:fitem>

			{{--<yz:fitem name="sellPrice" label="优惠券出售金额" append="1">--}}
				{{--<span class="ts">元</span>--}}
			{{--</yz:fitem>--}}
			<yz:fitem label="有效时间设定">
				<php> $data['timetype'] = isset($data['perpetual']) ? $data['perpetual'] : 1; </php>
				<yz:radio name="timetype" options="$timetype" valuefield="id" css="send-type" textfield="name" checked="$data['perpetual']"></yz:radio>
			</yz:fitem>
			<yz:fitem pid="timetype1" label="使用有效期" css="hidden">
				<div>
					购买之日起<input type="text" name="expireDay" class="u-ipttext ml5 mr5" style="width:100px;" value="{{$data['expireDay']}}">日有效
				</div>
			</yz:fitem>
			<yz:fitem pid="timetype2" pstyle="display:none" name="beginTime" label="固定有效期开始" type="date"></yz:fitem>
			<yz:fitem pid="timetype3" pstyle="display:none" name="endTime" label="结束" type="date"></yz:fitem>
			<yz:fitem pid="timetype4" label="使用有效期" css="hidden">
				<div>
					购买之日起永久有效
				</div>
			</yz:fitem>
			<!--<yz:fitem label="是否叠加使用">
			    <php> $data['overlay'] = isset($data['overlay']) ? $data['overlay'] : 1; </php>
				<yz:radio name="overlay" options="0,1" texts="不可叠加使用,可叠加使用" checked="$data['overlay']"></yz:radio>
			</yz:fitem>-->
			<yz:fitem label="状态">
				<php> $data['status'] = isset($data['status']) ? $data['status'] : 1; </php>
				<yz:radio name="status" options="1,0" texts="启用,禁用" checked="$data['status']"></yz:radio>
			</yz:fitem>
			<yz:fitem name="sort" label="排序" val="100"></yz:fitem>
		</yz:form>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
    jQuery.fn.removeSelected = function() {
        this.val("");
    };

	$(function(){
        @if($data['type'] == 'offset')
        $("#data-form-item").hide();
        @endif

        //一级分类出现二级分类
        $("select[name='cate']").change(function(){
            var cateId = $(this).val();
            $("#cateId2").removeSelected();
            if(cateId == 0){
                $("#cateId2").hide();
                return false;
            }
            var id = "{{$args['id']}}";
            $.post("{{ u('Promotion/getcateSecond') }}",{"cateId":cateId},function(res){
                if(res.code > 0){
                    $.ShowAlert(res.msg);
                }else{
                    var html = "";
                    html += '<select name="cateId2" class="sle">';
                    html += '<option value="0">全部</option>';

                    $.each(res.data,function(){
                        html += '<option value="'+this.id+'">'+this.name+'</option>';
                    })

                    html += '</select>';
                    $("#cateId2").html(html);
                }
            });
            $("#cateId2").show();
        })

		//$("#name").attr({ readonly: 'true' });
		function makeName()
		{
			var data = $("#data").val();
            var type = $("input:radio[name='type'][checked]").val();
			if (data != '' && type == "money") {
			    if( /\d(\.\d{1,2})?/g.test(data) ){
                    data = data +"元";
			    }else{
                    data = "";
                    $("#data").val(data);
			    }
			}
            var cate = $("select[name='cateId2'] option:selected").text();
            if(cate == '全部' || cate == ''){
                cate = $("select[name='cate'] option:selected").text();
            }
            if(type == "offset"){
                var name =  "抵用券";
                $("#data-form-item").hide();
            }else{
                var name = data + "优惠券";
                $("#data-form-item").show();
            }
			$("#name").val(name);
		}

		//makeName();

		$("#sellPrice").blur(function(){
			if( /\D/g.test($(this).val()) ){
				$(this).val("");
			}
		});
		$("#data").blur(makeName);
		$("#cate").change(makeName);

        $("#cateId2").change(function(){
            var cateId = $(this).val();
            if(cateId != 0){
                makeName();
            }
        });

        $("input:radio[name='type']").click(makeName);
		 
		var timetype = $("input:radio[name='timetype'][checked]").val();
		if(timetype == 1){
			$('#timetype2').hide();
			$('#timetype3').hide();
			$('#timetype1').show();
			$('#timetype4').hide();
		}else if(timetype == 2){
			$('#timetype1').hide();
			$('#timetype2').show();
			$('#timetype3').show();	
			$('#timetype4').hide();	
		}else{
			$('#timetype2').hide();
			$('#timetype3').hide();
			$('#timetype1').hide();
			$('#timetype4').show();
		}
		$("input:radio[name='timetype']").change(function(){
				if( $(this).val() == 1 ) {
					$('#timetype2').hide();
					$('#timetype3').hide();
					$('#timetype4').hide();
					$('#timetype1').show();
				}else if($(this).val() == 2){
					$('#timetype1').hide();
					$('#timetype2').show();
					$('#timetype4').hide();
					$('#timetype3').show();
				}else{
					$('#timetype2').hide();
					$('#timetype3').hide();
					$('#timetype1').hide();
					$('#timetype4').show();
				}
			});
	})
</script>
@stop
