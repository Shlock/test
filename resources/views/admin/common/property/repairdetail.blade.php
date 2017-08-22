@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	<div>
    <div class="m-zjgltbg">                 
        <div class="p10">
            <div class="m-tab m-smfw-ser pt20">
                <div class="m-porbar clearfix">
                    <?php $width=(100/count($data['statusNameDate'])).'%'; $_width = ((100/count($data['statusNameDate']))-1).'%';?> 
                    <img src="{{ asset('images/'.$data['statusFlowImage'].'.png') }}" alt=" " class="mt20 pt10 clearfix" width="750">
                    <ul class="m-barlst clearfix tc mt20 pt10" style="width:770px;">
                    @foreach($data['statusNameDate'] as $key => $value)
                        @if($data['statusFlowImage'] == 'statusflow_9' && $key == 0)
                            <?php $color = '#000000'; ?>
                        @elseif($data['statusFlowImage'] == 'statusflow_10' && $key == 1)
                            <?php $color = '#000000'; ?>
                        @elseif($data['statusFlowImage'] == 'statusflow_11' && $key == 2)
                            <?php $color = '#000000'; ?>
                        @else
                            @if($value['date']==0)
                                <?php $color = '#ccc'; ?>
                            @else
                                <?php $color = '#000000'; ?>
                            @endif
                        @endif
                        <li style="width:{{$width}};*width:{{$_width}};color:{{$color}}">
                            <p class="tc">{{$value['name']}}</p>
                            <p class="tc">{{ $value['date'] > 0 ? yztime($value['date']) : '' }}</p>
                        </li>
                    @endforeach
                    </ul>
                </div>
                <?php $images = $data['images'] ? explode(',', $data['images']) : ''; ?>
                @yizan_begin
                    <yz:form nobtn="1">
                        <yz:fitem name="types" label="报修类型" type="text">
                            {{ $data['types']['name'] }}
                        </yz:fitem>
                        <yz:fitem name="build" label="楼栋号" type="text">
                            {{ $data['build']['name'] }}
                        </yz:fitem>
                        <yz:fitem name="roomNum" label="房间号" type="text">
                            {{ $data['room']['roomNum'] }}
                        </yz:fitem>
                        <yz:fitem name="owner" label="业主" type="text">
                            {{ $data['room']['owner'] }}
                        </yz:fitem>
                        <yz:fitem name="mobile" label="联系电话" type="text">
                            {{ $data['room']['mobile'] }}
                        </yz:fitem>
                        <yz:fitem label="问题描述" >
                            <p>{{ $data['content'] }}</p>
                            @if($data['images'])
                            @foreach($images as $image)
                            <a href="{{$image}}" target="_blank"><img src="{{$image}}" width="60%" height="50%"></a>
                            <br/>
                            @endforeach
                            @endif
                        </yz:fitem>
                        <div class="u-antt tc" style=" background: #f9f9f9;">
                            @if($data['status'] == 0)
                            <a href="javascript:;" data-status="1" class="mt15 ml15 on m-sjglbcbtn dispose">处理</a>
                            @elseif($data['status'] == 1)
                            <a href="javascript:;" data-status="2" class="mt15 ml15 on m-sjglbcbtn dispose">完成</a>
                            @endif
                            <a href="javascript:history.back(-1);" class="mt15 ml15 m-quxiaobtn">返回</a>
                        </div>
                    </yz:form>
                @yizan_end
            </div>
        </div>
    </div>
</div>
  <script type="text/javascript">
  $(function() {
        $('.dispose').click(function() {
            var status = $(this).data('status');
            var id = "{{ $data['id'] }}";
            $.post("{{ u('Property/repairsave')  }}",{id:id,status:status},function(result){   
                if(result.code == 0){ 
                    window.location.reload();
                }else{
                    $.ShowAlert(result.msg);
                }
            },'json');
        })
    })
  </script>
@stop 