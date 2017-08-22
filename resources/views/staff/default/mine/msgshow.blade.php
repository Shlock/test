@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('contentcss')bcf @stop
@section('content')
 <!-- 下面是正文 -->
<div  class="mymessage bcf">
    <ul>
        <li>
            <div >
                <span class="mymessage-text ">{{$data['content']}}</span>
                <div class="blank0"></div>
                <span class="mymessage-time">{{Time::toDate($data['sendTime'],'y-m-d')}}</span>
                <div class="blank0"></div>
            </div>
        </li>
    </ul>
</div>
<!-- 正文结束 -->
@stop