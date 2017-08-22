@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
    @yizan_begin

    <yz:list>
        @if (!empty($data))
        <div class="u-ssct clearfix" style="position:relative;">

                <div class="search-row clearfix">
                    <div id="beginTime-form-item" class="u-fitem clearfix ">
		            <span class="f-tt">服务人员： </span>
                    <div class="f-boxr">
                        {{ $data['staff']['name'] }}
                        <a class="btn mr5" style="position:absolute;right:0px;top:5px;" href="{{ u('Staffleave/index') }}">返回</a>
                        @if($data['status'] == 0)
                        <a class="btn mr5" style="position:absolute;right:160px;top:5px;" href="javascript:staffleave('{{$data['id']}}',1);">批准请假</a>
                        <a class="btn mr5" style="position:absolute;right:65px;top:5px;" href="javascript:staffleave('{{$data['id']}}',2);">驳回请假</a>
                        @endif
                    </div>
                    </div>
                </div>
                <div class="search-row clearfix">
                    <div id="beginTime-form-item" class="u-fitem clearfix ">
                        <span class="f-tt">请假事由： </span>
                        <div class="f-boxr">
                            {{ $data['remark'] }}
                        </div>
                    </div>
                </div>
                <div class="search-row clearfix">
                    <div id="beginTime-form-item" class="u-fitem clearfix ">
                        <span class="f-tt">请假日期： </span>
                        <div class="f-boxr">
                            {{$data['beginTime']}} 至 {{$data['endTime']}}
                        </div>
                    </div>
                </div>
                <div class="search-row clearfix">
                    <div id="beginTime-form-item" class="u-fitem clearfix ">
                        <span class="f-tt">处理结果： </span>
                        <div class="f-boxr">
                            {{$data['isAgree']}}
                        </div>
                    </div>
                </div>
            @if($data['status'] != 0)
                <div class="search-row clearfix">
                    <div id="beginTime-form-item" class="u-fitem clearfix ">
                        <span class="f-tt">处理时间： </span>
                        <div class="f-boxr">
                            {{$data['disposeTime']}}
                        </div>
                    </div>
                </div>
                <div class="search-row clearfix">
                    <div id="beginTime-form-item" class="u-fitem clearfix ">
                        <span class="f-tt">处理备注： </span>
                        <div class="f-boxr">
                            {{$data['disposeResult']}}
                        </div>
                    </div>
                </div>
            @endif
            <div class="search-row clearfix">
                <div id="beginTime-form-item" class="u-fitem clearfix ">
                    <span class="f-tt"><h3>本次请假影响服务事项：</h3> </span>
                </div>
            </div>
        </div>
        @endif
        <php>
            $nav = '';
            $nav1 = '';
            $nav2 = '';
            switch((int)Input::get(type)){
                case 0 : $nav = 'on'; break;
                case 1 : $nav1 = 'on'; break;
                case 2 : $nav2 = 'on'; break;
            }
        </php>
        <tabs>
            <navs>
                <nav label="所有派发">
                    <attrs>
                        <url>{{ u('Staffleave/detail',['id'=>Input::get('id')]) }}</url>
                       <css>{{$nav}}</css>
                    </attrs>
                </nav>
                <nav label="指定派发">
                    <attrs>
                        <url>{{ u('Staffleave/detail',['id'=>Input::get('id'),'type'=>1]) }}</url>
                        <css>{{$nav1}}</css>
                    </attrs>
                </nav>
                <nav label="随机派发">
                    <attrs>
                        <url>{{ u('Staffleave/detail',['id'=>Input::get('id'),'type'=>2]) }}</url>
                        <css>{{$nav2}}</css>
                    </attrs>
                </nav>
            </navs>
        </tabs>
        <btns>
            <linkbtn label="批量指派"  url="javascript:batchDesignate(this);"></linkbtn>
        </btns>
        <table  checkbox="1" relmodule="Staffleave">
            <columns>
                <column code="Designate" label="服务派发方式" align="center"></column>
                <column code="Schedule" label="日程" align="center"></column>
                <column code="goodsName" label="服务事项" align="center"></column>
                <column code="orderStatusStr" label="服务状态" align="center"></column>
                <actions>
                    <p>
                        <a href="javascript:;" class=" blu" target="_self" title="{{$list_item['mobile']}}">致电客户</a>
                    </p>
                    <p>
                        <action label="更改日期" css="blu">
                            <attrs>
                                <url>{{ u('Staffleave/date', ['id'=>Input::get('id'),'oid'=>$list_item['id']]) }}</url>
                            </attrs>
                        </action>
                    </p>
                    <p>
                        <action label="更改人员" css="blu">
                            <attrs>
                                <url>{{ u('Staffleave/staff', ['id'=>Input::get('id'),'oid'=>$list_item['id']]) }}</url>
                            </attrs>
                        </action>
                    </p>
                </actions>
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop

@section('js')
    <script type="text/tpl" id="staffleave">
        <div style="width:500px; text-align:center;margin:10px 0 ">
            <textarea name='disposeRemark' id='disposeRemark' placeholder='请填写备注' style="width:400px;height:100px;border:1px solid #EEE"></textarea>
        </div>
    </script>
    <script type="text/javascript">
        function staffleave(id, agree) {
            if(agree == 1){
                var title = '批准请假';
            }else{
                var title = '驳回请假';
            }
            var dialog = $.zydialogs.open($("#staffleave").html(), {
                boxid:'SET_GROUP_WEEBOX',
                width:300,
                title:title,
                showClose:true,
                showButton:true,
                showOk:true,
                showCancel:true,
                okBtnName: '确定',
                cancelBtnName: '取消',
                contentType:'content',
                onOk: function(){
                    var query = new Object();
                    query.id = id;
                    query.content = $("#disposeRemark").val();
                    query.status = agree;
                    if(query.content == ""){
                        $.ShowAlert("请输入备注");
                    }else{
                        dialog.setLoading();
                        $.post("{{ u('Staffleave/dispose') }}",query,function(result){
                            dialog.setLoading(false);
                            if(result.code == 0){
                                $.ShowAlert('操作成功');
                                window.location.reload();
                            }else{
                                $.ShowAlert(result.msg);
                                $.zydialogs.close("SET_GROUP_WEEBOX");
                            }
                        },'json');
                    }
                },
                onCancel:function(){
                    $.zydialogs.close("SET_GROUP_WEEBOX");
                }
            });
        }

       function batchDesignate(obj,id) {
           var ids = new Array();
           if (typeof id == "undefined" || id == '') {
               id = 'checkListTable';
           }
           $("#" + id + " input:checked[name='key']").each(function () {
               if (!$(this).hasClass('disabled')) {
                   ids.push(this.value);
               }
           });
           ids = ids.join(',');
           if (ids == '') {
               $.ShowAlert('请选择需要指派的服务事项');
               return false;
           }
           window.location.href = "{!! u('Staffleave/staff',['id'=>Input::get('id'),'oids' => 'ORDERIDS']) !!}".replace('ORDERIDS',ids);
       }
    </script>
@stop
