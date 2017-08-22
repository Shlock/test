@extends('wap.community._layouts.base')
@section('js')
    <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script>
    <link rel="stylesheet" href="/static/smartUI/css/smart.css">
    <script type="text/javascript" src="/static/smartUI/vendor/zepto.js"></script>
    <script type="text/javascript" src="/static/smartUI/js/smart.js"></script>
@stop
@section('show_top')
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>我的推广数据</h1>
        <a href="{{u('UserCenter/index')}}" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
@stop

@section('content')
    <script type="text/javascript">
        var jq=$.noConflict();
    </script>
    <link rel="stylesheet" href="/static/smartUI/css/smart.css">
    <script type="text/javascript" src="/static/smartUI/vendor/zepto.js"></script>
    <script type="text/javascript" src="/static/smartUI/js/smart.js"></script>
    <div role="main" class="">
        <div class="x-wdscmain">
            
        </div>
        <style type="text/css">
            .desc1{
                width: 5em !important;
            }
            .desc2{
                width: 18em !important;
            }
            
        </style>
        <div class="x-wdsmian">
        <div style="width:100%;">

               
                
            </div>
                <div class="sample" style="background:white;margin-top:20px;margin-bottom:20px">
                <div class="sample-item">
                  <div class="sample-content">
                    <ul class="smart-list-default smart-list-default-left">
                      <li class="smart-list-default-item">
                        <div class="smart-list-default-title desc1">开始时间</div>
                        <div class="smart-list-default-content">
                             <input type="text" style="background:white;line-height:20px;" placeholder="开始日期" id="begin_date" class="form-text" readonly="readonly" value="{{$begin}}">
                        </div>
                      </li>
                      <li class="smart-list-default-item">
                        <div class="smart-list-default-title desc1">结束时间</div>
                        <div class="smart-list-default-content"><input type="text" style="background:white;line-height:20px;" placeholder="结束日期" id="end_date" class="form-text" readonly="readonly" value="{{$end}}"></div>
                      </li>
                      </li>
                    </ul>
                  </div>
                </div>
            </div>
            <button id="confirm">确认</button>

         <div class="sample" style="background:white;margin-top:20px">
                <div class="sample-item">
                  <div class="sample-content">
                    <ul class="smart-list-default smart-list-default-left">
                      <li class="smart-list-default-item ">
                        <div class="smart-list-default-title desc2">推广注册数（扫码注册用户数）：</div>
                        <div class="smart-list-default-content">{{$extensionUserNum}}人</div>
                      </li>
                      <li class="smart-list-default-item">
                        <div class="smart-list-default-title desc2">有效用户数（在指定地理范围内）：</div>
                        <div class="smart-list-default-content">{{$validUserNum}}人</div>
                      </li>
                      <li class="smart-list-default-item">
                        <div class="smart-list-default-title desc2">活跃用户数（注册之后有登录app）：</div>
                        <div class="smart-list-default-content">{{$activeUserNum}}人</div>
                      </li>
                      <li class="smart-list-default-item">
                        <div class="smart-list-default-title desc2">微信活跃用户数（有微信登录记录）：</div>
                        <div class="smart-list-default-content">{{$weixinActiveUserNum}}人</div>
                      </li>
                      <li class="smart-list-default-item">
                        <div class="smart-list-default-title desc2">购买用户数（有产生购买记录的用户）：</div>
                        <div class="smart-list-default-content">{{$buyUserNum}}人</div>
                      </li>
                    </ul>
                  </div>
                </div>
            </div>
        </div>
        <div class="y-con">
            <ul>
                
            </ul>
        </div>
    </div>
     <div class="x-bgtk x-sctk none">
         <div class="x-bgtk1">
             <div class="x-tkbgi">
                 <div class="tips"></div>
             </div>
         </div>
     </div>
<script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry"></script>
 <script type="text/javascript">
 var clientLatLng = null;
     jq(function() {

        var Datepicker1 = $("#begin_date").datepicker({
                initData: '2012-12-23',
                min: '2012-6-6',
                max: '2017-8-8',
                type: 'date',
                onChange: function (date) {
                    console.log('the date is change', date)
                }
            })
        var Datepicker2 = $("#end_date").datepicker({
                initData: '2012-12-23',
                min: '2012-6-6',
                max: '2017-8-8',
                type: 'date',
                onChange: function (date) {
                    console.log('the date is change', date)
                }
            })

     });
     jq(document).on("touchend","#confirm",function(){
        var begin = jq('#begin_date').val();
        var end = jq('#end_date').val();
        location.href="/UserCenter/extension?begin="+begin+"&end="+end;
     });
     jq(document).on("click","#confirm",function(){
        var begin = jq('#begin_date').val();
        var end = jq('#end_date').val();
        location.href="/UserCenter/extension?begin="+begin+"&end="+end;
     });


 </script>
@stop

