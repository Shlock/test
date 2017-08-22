@extends('seller._layouts.base')
@section('css')
    <style type="text/css">
        ul,li{list-style: none;margin: 0;padding: 0;}
        div,p{margin: 0; padding: 0;}
        a{text-decoration: none;}
        .clearfix:after{content:'.';clear:both;display:block;height:0;visibility:hidden}
        .x-date{background: #fff; width: 770px; margin: 0 1px;}
        .x-date .tt{margin: 15px 15px 10px 15px;}
        .x-datetab{width: 770px; position:relative; border:solid #ddd; border-width: 1px 1px 0 1px;}
        html > body .x-datetab {width:770px;}
        .x-datetab #two5{border-right: 0;}
        .mr10{margin-right: 10px;}
        .x-datetab .x-datett{float:left; width:109px; height:35px; border: 1px solid #ddd; line-height:20px; padding: 10px 0; font-size:14px; cursor:pointer; text-align:center; background: #fff;}
        .x-datetab .up{background:#f3f6fa;color:#fff; width:100px;}
        .x-datetab .x-datett p{margin: 0;}
        .x-datetab .x-msg{position:absolute; top:55px; left:-1px; width:770px; border: 1px solid #ddd;display:none;}
        .x-datetab .block{display:block;}
        .x-datelst li{padding: 8px 0; border-bottom: 1px solid #ddd; position: relative; line-height: 28px; font-size: 14px;}
        .x-datelst li .free{line-height: 45px;}
        .x-datelst li .datew{width: 120px; text-align: center; display: inline-block;}
        .x-datelst .datedel{padding: 15px; border-bottom: 1px solid #ddd;}
        .x-datelst .ui-btn{float: right; color: #f3f6fa; font-size: 12px;}
        .x-datelst .date1{font-size: 12px; line-height: 18px; margin: 10px 0;}
        .x-datelst .date2{font-size: 12px; line-height: 18px;}
        .x-datelst .dates{width: 120px; background: #fff; position: absolute; top: 44%; left: 0; text-align: center;}
        .x-datelst .last{border-bottom: 0;}
		.x-date{ background:#f3f6fa;}
		.x-datetab{ background:#f3f6fa; border:1px solid #ccc; border-width:1px; border-bottom:0; margin-left:-1px;}
		.x-datetab .x-datett{ background:none; border:0; line-height:35px; border-left:1px solid #ccc;}
		.x-datetab .x-datett:first-child{ border:0;}
		.x-datetab .up{ background:#fff; color:#313233; width:109px;}
		.x-datelst{background:#fff;}
    </style>
@stop
@section('content')
    <div style="width:775px;">
        <div class="x-date">
            <p class="tt">{{$data['name']}}的排期</p>
            <div class="x-datetab clearfix" style="width:100%;">
                @for($i = 1; $i <= $days; $i++)
                <?php $day = Time::toDate(UTC_TIME+($i-1)*86400, 'Y-m-d');?>
                <div class="x-datett" id="two{{$i}}" onclick="setContentTab('two',{{$i}},{{$days}})">
                    {{Time::toDate(UTC_TIME+($i-1)*86400, 'm月d日')}}
                </div>
                <div class="block x-msg" id="con_two_{{$i}}" style="background-color:#ffffff;">
                    <ul class="x-datelst">
                        <li>
                            @if(count($schedule_date[$day]) > 0)
                            @foreach($schedule_date[$day] as $val)
                            <div class="datedel">
                                <p>
                                    <span class="mr10">联系人：{{ $val['name']}}</span>
                                    <span class="mr10">联系电话：{{ $val['mobile']}}</span>
                                    <span class="mr10">配送/服务时间：{{Time::toDate($val['appTime'], 'Y-m-d H:i')}}</span>
                                </p>
                                <p class="date1">地址：{{ $val['address']}}</p>
                                <p class="date2">备注：{{ $val['buyRemark'] ? $val['buyRemark'] : '无备注'}}</p>
                            </div>
                            @endforeach
                            @else
                            <p class="free"><span class="datew"></span>暂无安排</p>
                            @endif
                        </li>
                    </ul>
                </div>
                @endfor
            </div>
        </div>
    </div>
@stop
@section('js')
<script type="text/javascript">
    function setContentTab(name, curr, n) {
        for (i = 1; i <= n; i++) {
            var menu = document.getElementById(name + i);
            var cont = document.getElementById("con_" + name + "_" + i);
            menu.className = i == curr ? "up x-datett" : "x-datett";
            if (i == curr) {
                cont.style.display = "block";
            } else {
                cont.style.display = "none";
            }
        }
    }
    setContentTab('two', 1,{{$days}});
    $(".x-datett").click(function() {
        $.post("{{ u('Staff/get_zodiac_sign') }}",{'time':$(this).val()},function(res){
            $("#constellation2").text(res);
            $("#constellation").val(res);
        });
    })
</script>
@stop
