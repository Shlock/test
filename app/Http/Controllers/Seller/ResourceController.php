<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Utils\Image;
use View;

/**
 * 资源上传
 */
class ResourceController extends BaseController {
	/**
	 * 获得表单参数
	 */
	public function getformargs() {
		$data = Image::getFormArgs();
		return json_encode($data);
	}

	public function callback(){
		$result = ['status' => true];
		return json_encode($result);
	}
}
