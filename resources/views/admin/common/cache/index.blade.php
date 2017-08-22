@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	#loading{width: 100%;padding: 30px 0px;}
	#loadMsg{padding: 10px;font-size: 12px;display: none;}
	#loadingImg{display: none;}
	#loadingImgDiv{text-align: center;height: 15px;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<!-- <yz:btn label="清除所有缓存" click="clearAll()"></yz:btn> -->
		<yz:btn label="清除模板缓存" click="clearLocal()"></yz:btn>
		<yz:btn label="清除服务器缓存" click="clearServer()"></yz:btn>
		<div id="loading">
			<div id="loadingImgDiv">
				<img src="{{ asset('images/loading_e.gif') }}" alt="" id="loadingImg">
			</div>
			<p id="loadMsg"></p>
		</div>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
var loadingImg = $("#loadingImg");
var msg = $("#loadMsg");
	function clearAll () {
		var l = clearLocal();
		if(l) {
			var s = clearServer();
			if(s) {
				loadingImg.hide();
				msg.append('<p>清除完毕！</p>');
			}
		}
		
	}

	function clearServer () {
		msg.show();
		loadingImg.show();
		msg.append('<p>正在清除服务器缓存,请稍等...</p>');
		$.post("{{u('Cache/clear')}}",function(res){
			loadingImg.hide();
			if(res==1){
				msg.append('<p>服务器缓存 清除完毕！</p>');
				return true;
			}
			else{
				msg.append('<p>服务器缓存 清除失败！</p>');
				return false;
			}
		});
	}

	function clearLocal () {
		msg.show();
		loadingImg.show();
		msg.append('<p>正在清除模板缓存,请稍等...</p>');
		$.post("{{u('Cache/local')}}",function(res){
			loadingImg.hide();
			if(res==1){
				msg.append('<p>模板缓存 清除完毕！</p>');
				return true;
			}
			else{
				msg.append('<p>模板缓存 清除失败！</p>');
				return false;
			}
		})
	}
</script>
@stop
