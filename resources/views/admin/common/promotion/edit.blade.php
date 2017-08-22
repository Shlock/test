@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	#searchSeller{margin-left: 5px;}
	#mobile{width: 100px;}
	.ts{color: #999;margin-left: 5px;vertical-align:middle;}
</style>
@stop
<?php
$type = [
    ['id'=>1,'name'=>'固定有效期'],
    ['id'=>2,'name'=>'至发放之日起算']
];
$useType = [
        ['id'=>1,'name'=>'无限制'],
        ['id'=>2,'name'=>'指定分类'],
        ['id'=>3,'name'=>'指定商家']
];
$seller = [
    ['id'=>0,'name'=>'请输入手机号或名称']
];
if (!empty($data['seller'])) {
    $seller[1] = ['id'=> $data['seller']['id'],'name'=>$data['seller']['name']];
}
 ?>
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
					<yz:fitem name="name" label="优惠券名称" append="1">
                        <span class="ts" style="color:red;">*</span>
                    </yz:fitem>
					<yz:fitem name="money" label="优惠券面额" append="1"  val="{{ $data['money']}}" style="width:100px;">
						<span class="ts">元</span>
                        <span class="ts" style="color:red;">*</span>
					</yz:fitem>
                    <yz:fitem label="有效期">
                        <php>
                            $data['type'] = max((int)$data['type'],1);
                            if ($data['type'] == 1) {
                                $acss = 'none';
                            } else {
                                $bcss = 'none';
                            }
                        </php>
                        <yz:radio name="type" options="$type" valuefield="id" css="type" textfield="name" checked="$data['type']" append="1"></yz:radio>
                        <span class="ts" style="color:red;">*</span>
                    </yz:fitem>
					<yz:fitem name="expireTime" label="起止时间" type="date" pcss="{{ $bcss }}">
                        <input type="text" name="beginTime" id="beginTime" class="datetime u-ipttext" style="width: 150px;" value="{{ yztime($data['beginTime'],'Y-m-d H:i') }}"> -
                        <input type="text" name="endTime" id="endTime" class="datetime u-ipttext" style="width: 150px;" value="{{ yztime($data['endTime'],'Y-m-d H:i') }}">
                    </yz:fitem>

                    <yz:fitem name="expireDay" label="有效天数" pcss="{{$acss}}">
                        <div>
                            发放之日起<input type="text" name="expireDay" class="u-ipttext ml5 mr5" style="width:100px;" value="{{$data['expireDay']}}">日有效
                        </div>
                    </yz:fitem>
                    <yz:fitem name="unableDate" label="不可用日期">
                        @if(count($data['unableDate']) > 0)
                        @foreach($data['unableDate'] as $key=>$val)
                            @if($key == 0)
                                <div class="f-boxr" id="unableDate">
                                    <input type="text" name="unableDate[]"  class="date u-ipttext" value="{{ yztime($val['dateTime'],'Y-m-d') }}">
                                    <span class="ts add-unable-date"><a href="javascript:;">添加</a></span>
                                </div>
                            @else
                                <div class="f-boxr" style="clear:left;">
                                    <input type="text" name="unableDate[]"  class="date u-ipttext" value="{{ yztime($val['dateTime'],'Y-m-d') }}">
                                    <span class="ts del-unable-date"><a href="javascript:;">删除</a></span>
                                </div>
                            @endif
                        @endforeach
                        @else
                            <div class="f-boxr" id="unableDate">
                                <input type="text" name="unableDate[]"  class="date u-ipttext">
                                <span class="ts add-unable-date"><a href="javascript:;">添加</a></span>
                            </div>
                        @endif


                    </yz:fitem>
                    <yz:fitem label="类型">
                        <php>
                            $data['useType'] = max((int)$data['useType'],1);
                            $acss = '';
                            $bcss = '';
                            if ($data['useType'] == 1) {
                                $acss = 'none';
                                $bcss = 'none';
                            } elseif ($data['useType'] == 2) {
                                $acss = 'none';
                            } else {
                                $bcss = 'none';
                            }
                        </php>
                        <yz:radio name="useType" options="$useType" valuefield="id" textfield="name" checked="$data['useType']"></yz:radio>
                        <span class="ts" style="color:red;">*</span>
                    </yz:fitem>
                    <yz:fitem name="seller" label="商家" append="1" attr="maxlength='11'" style="margin-right:5px;width:120px;" pcss="{{ $acss }}">
                        <yz:select name="sellerId" options="$seller" valuefield="id" textfield="name" selected="$data['seller']['id']"></yz:select>
                        <yz:btn label="搜索" id="searchSeller"></yz:btn>
                        <span class="ts ts2"></span>
                    </yz:fitem>
                <yz:fitem label="选择分类" pcss="send-cate-type send-cate-group {{ $bcss }}" name="sellerCate">
                    <div class="input-group">
                        <table border="0">
                            <tbody>
                            <tr>
                                <td rowspan="2">
                                    <select id="cate_1" name="sellerCateIds" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:260px;">
                                        @if(count($data['sellerCates']) > 0)
                                            @foreach($data['sellerCates'] as $item)
                                                <option value="{{$item['cates']['id']}}" >{{$item['cates']['name']}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td width="60" align="center" rowspan="2">
                                    <button type="button" class="btn btn-gray" onclick="$.optionMove('cate_2', 'cate_1', 1);">
                                        <span class="fa fa-2x fa-angle-double-left"> </span>
                                    </button>
                                    <br><br>
                                    <button type="button" class="btn btn-gray" onclick="$.optionMove('cate_2', 'cate_1');">
                                        <span class="fa fa-2x fa-angle-left"> </span>
                                    </button>
                                    <br><br>
                                    <button type="button" class="btn btn-gray" onclick="$.optionMove('cate_1', 'cate_2');">
                                        <span class="fa fa-2x fa-angle-right"> </span>
                                    </button>
                                    <br><br>
                                    <button type="button" class="btn btn-gray" onclick="$.optionMove('cate_1', 'cate_2', 1);">
                                        <span class="fa fa-2x fa-angle-double-right"> </span>
                                    </button>
                                    <input type="hidden" name="sellerCateIds" id="cateIds">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select id="cate_2" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:260px;">
                                        @foreach($cateIds as $key => $val)
                                            @if($val['isHasChild'])
                                                <optgroup label="{{$val['name']}}">
                                                    @foreach($cateIds[$key]['childs'] as $cs)
                                                        <option value="{{$cs['id']}}">{{$cs['name']}}</option>
                                                    @endforeach
                                                </optgroup>
                                            @else
                                                <option value="{{$val['id']}}">{{$val['name']}}</option>
                                            @endif
                                        @endforeach
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
                                $("#cate_1 option").each(function(){
                                    ids.push(this.value);
                                })
                                $("#cateIds").val(ids);
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

                <yz:fitem name="limitMoney" label="消费满" append="1" style="width:100px;" val="{{ (double)$data['limitMoney']}}">
                    <span class="ts">元使用</span>
                </yz:fitem>
                <yz:fitem name="brief" label="描述" type="textarea">
                </yz:fitem>
        </yz:form>
	@yizan_end
@stop

@section('js')
    <script type="text/tpl" id="append-unable-date">
        <div class="f-boxr" style="clear: left">
            <input type="text" name="unableDate[]" class="date u-ipttext">
            <span class="ts del-unable-date"><a href="javascript:;">删除</a></span>
        </div>
    </script>
<script type="text/javascript">
	$(function(){


		$('#searchSeller').click(function(){
			$(".ts2").text('');
			var keywords = $('#seller').val();
			$.post("{{u('Promotion/searchSeller')}}",{"mobileName":keywords},function(res){
				//res = eval(res.data);
				if(res.data.length > 0){
					var html = "";
					$.each(res.data,function(n,value) {
						html += "<option value='"+value.id+"'>"+value.name+"</option>";
					});
					$("#sellerId").html(html);
				}else{
					$("#sellerId").html("<option value='0'>请输入手机号或名称</option>");
					$(".ts2").text('未查询到相关服务人员');
				}


			},"json");
		});

        //有效期选择
		$("input:radio[name='type']").change(function(){
			if( $(this).val() == 1 ) {
				$("#expireTime-form-item").removeClass("none");
                $("#expireDay-form-item").addClass("none");
			}else{
                $("#expireTime-form-item").addClass("none");
                $("#expireDay-form-item").removeClass("none");
			}
		});



        //不可用日期
        $(".add-unable-date").on("click",function(){
            var html = $("#append-unable-date").html();
            $("#unableDate").parent().append(html);
            $(".date").datepicker();
        })

        $(document).on("click",".del-unable-date",function(){
            $(this).parent().remove();
        })


        //使用类型
        $("input[name='useType']").change(function(){
            var val = $(this).val();
            if(val == 1){
                $("#seller-form-item").addClass("none");
                $("#sellerCate-form-item").addClass("none");
            }else if(val == 2){
                $("#seller-form-item").addClass("none");
                $("#sellerCate-form-item").removeClass("none");
            }else{
                $("#seller-form-item").removeClass("none");
                $("#sellerCate-form-item").addClass("none");
            }
        })

	});
</script>
@stop
