@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
<?php
	$access_id = Config::get('app.image_config.oss.access_id');
	$access_key = Config::get('app.image_config.oss.access_key');
	$bucket = Config::get('app.image_config.oss.bucket');

	$url='http://'.$bucket.'.oss-cn-hangzhou.aliyuncs.com';
	$policy = '{"expiration": "2016-01-01T12:00:00.000Z","conditions":[{"bucket": "'.$bucket.'" },["content-length-range", 0, 1024000]]}';
	$policy = base64_encode($policy);
	$signature = base64_encode(hash_hmac('sha1', $policy, $access_key, true));
	$success_action_redirect = "http://admin.vso2o.com/a.php";//回调路径
	$savePath = "upload/".date('Y').'/'.date('m').'/${filename}';//保存路径
?>
	<form action="{{$url}}" method="post" enctype="multipart/form-data" target="upload">
        <label for="file">选择文件:</label>
        <input type="hidden" name="OSSAccessKeyId" id="OSSAccessKeyId"  value="{{ $access_id }}" />
        <input type="hidden" name="policy" id="policy"  value="{{ $policy }}" />
        <input type="hidden" name="signature" id="signature"  value="{{ $signature }}" />
        <input type="hidden" name="key" id="key"  value="{{$savePath}}" />
        <input type="hidden" name="success_action_redirect" id="success_action_redirect"  value="{{ $success_action_redirect }}" />
        <input type="file" name="file" id="file" />
        <br/>
        <input type="submit" name="submit" value="确定" id="submit" />
    </form>
    <iframe src="" frameborder="0" id="upload" onload="" border="0" width="1" height="1">
    </iframe>

    <script type="text/javascript">
    	$(function(){
    		$('#submit').submit(function(){
    			$('#upload').one("load",function(){
    				try{
		    			if (obj.contentWindow) {
			    			alert(obj.contentWindow.document.body.innerHTML);
			    		}else if(obj.contentDocument) {
			    			alert(obj.contentDocument.document.body.innerHTML);
			    		} else {
			    			alert('Error');
			    		}
		    		} catch(e) {
		    			alert('Error');
		    		}
    			});
    		});
    	});
    </script>

	@yizan_begin
		
	@yizan_end	    
@stop

@section('js')
@stop


