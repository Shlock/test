<?php 
namespace YiZan\Http\Controllers\Staff;

/**
 * 资源上传
 */
class ResourceController extends AuthController {
	public function callback(){
		$result = ['status' => true];
		return json_encode($result);
	}
}
