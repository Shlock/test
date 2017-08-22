@extends('seller._layouts.base')
@section('content')
<div class="p20">
        <div class="m-zjgltbg">
            <div class="p10">                       
                <p class="f-bhtt f14 clearfix">
                    <span class="ml15 fl">CSV导入</span>
                    <a href="{{ u('PropertyFee/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
                </p>
                <div class="m-quyu1">
                    <div class="m-inforct" style="padding-top:78px;width:750px;">  
                        @yizan_begin
                        <yz:form action="importsave" file="1" noajax="1">
                            <yz:fitem label="导入CSV文件">
                                <input type="file" name="csvfile" value="">
                            </yz:fitem>
                            <div class="u-fitem clearfix">
                                <p >
                                <span class="ml15 fl mt15" style="margin-left:50px;"><a href="{{ asset('upload/csvexample_1.csv')}}">下载物业费CSV示例</a></span>
                                </p> 
                            </div>
                        </yz:form>              
                        @yizan_end 
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop