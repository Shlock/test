@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
    @yizan_begin

    <yz:list>
        <div class="u-ssct clearfix" style="position:relative;">

                <div class="search-row clearfix">
                    <div id="beginTime-form-item" class="u-fitem clearfix ">
		            <span class="f-tt">服务人员: </span>
                    <div class="f-boxr">
                        {{ $data['staff']['name'] }}
                        <a class="btn mr5" style="position:absolute;right:0px;top:5px;" href="{{ u('Staffleave/detail',['id'=>Input::get('id')]) }}">返回</a>
                    </div>
                    </div>
                </div>
                <div class="search-row clearfix">
                    <div id="beginTime-form-item" class="u-fitem clearfix ">
                        <span class="f-tt">日程： </span>
                        <div class="f-boxr">
                            {{$data['beginTime']}} — {{$data['endTime']}}
                        </div>
                    </div>
                </div>
            @if((int)Input::get('oid') > 0)
                <div class="search-row clearfix">
                    <div id="beginTime-form-item" class="u-fitem clearfix ">
                        <span class="f-tt">服务事项： </span>
                        <div class="f-boxr">
                            {{$order['goodsName']}}
                        </div>
                    </div>
                </div>
            @endif
            <div class="search-row clearfix">
                <div id="beginTime-form-item" class="u-fitem clearfix ">
                    <span class="f-tt"><h3>更换服务人员：</h3> </span>
                    <div class="f-boxr">
                        <a class="btn mr5" style="position:absolute;right:0px;bottom:5px;" href="javascript:designate(0,2);">随机指派</a>
                    </div>
                </div>
            </div>

        </div>

        <table  relmodule="Staffleave">
            <columns>
                <column code="id" label="服务人员编号" align="left"></column>
                <column code="name" label="空闲人员" align="left"></column>
                <actions width="30">
                    <p>
                        <action label="更改" css="blu">
                            <attrs>
                                <url>javascript:designate('{{ $list_item['id'] }}', '1');</url>
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
    <script type="text/javascript">
            function designate(staff_id, type) {
                @if((int)Input::get('oid') > 0)
                var oids = "{{Input::get('oid')}}";
                @else
                var oids = "{{Input::get('oids')}}";
                @endif
                $.post("{{ u('Staffleave/designate') }}",{type:type,oids:oids,staffId:staff_id,id:"{{Input::get('id')}}"},function(res){
                    if(res.code == 0){
                        $.ShowAlert(res.msg)
                        window.location.href = "{!! u('Staffleave/detail',['id'=>Input::get('id')]) !!}";
                    }else{
                        $.ShowAlert(res.msg);
                    }
                },"json");
            }
    </script>
@stop
