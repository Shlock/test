<?php 
namespace YiZan\Http\Controllers\Wap;
use YiZan\Utils\Image;
use Input;

/**
 * 资源上传
 */
class ResourceController extends AuthController {
	public function callback(){
		$result = ['status' => true];
		return json_encode($result);
	}

    public function upload() {
        $image = Input::get('file');
        $path = Input::get('key');

        if (empty($image) ||
            empty($path) ||
            strpos($path, 'images/') !== 0 ||
            strpos($image, 'data:image/png;base64,') !== 0) {
            exit;
        }
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $data = base64_decode($image);
        $result = Image::upload($data, $path);
        if (!$result) {
            return json_encode(['status' => false,'data' => '']);
        } else {
            return json_encode(['status' => true,'data' => $result]);
        }
    }


}
