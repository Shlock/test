<?php 
namespace YiZan\Http\Controllers\Resource;

use Intervention\Image\Facades\Image;
use Config, Request, Response, File, Redirect, Exception;

class ImageController extends BaseController {
	public function index() {
		$img = Request::input('img');
		$whecos = Request::input('wheco');

		$width = 0;
		$height = 0;
		$is_cat = strpos($whecos, '_1e_1c_') !== false;
		
		$whecos = explode('_', str_replace('_1e_1c', '', $whecos));
		foreach ($whecos as $wheco) {
			$type = substr($wheco, -1, 1);
			$value = substr($wheco, 0, -1);
			switch ($type) {
				case 'w':
					$width = (int)$value;
					break;
				
				case 'h':
					$height = (int)$value;
					break;
			}
		}

		$to_img = Request::path();
		try {
			$img = Image::make($img);
		} catch (Exception $e) {
	    	exit;
	    }
		
		if ($is_cat) {//剪切时
			$old_width = $img->width();
			$old_height = $img->height();
			if ($width / $old_width < $height / $old_height) {//以高度
				$new_height = $height;
				$new_width = intval($height / $old_height * $old_width);
			} else {//以宽度
				$new_width = $width;
				$new_height = intval($width / $old_width * $old_height);
			}

			$img->resize($new_width, $new_height);
			$top 	= intval(($new_height - $height) / 2);
			$left	= intval(($new_width - $width) / 2);

			$img->crop($width, $height, $left, $top);
		} else {
			if ($width > 0) {
				$img->resize($width, null, function ($constraint) {
				    $constraint->aspectRatio();
				});
			}elseif ($height > 0) {
				$img->resize(null, $height, function ($constraint) {
				    $constraint->aspectRatio();
				});
			} else {
				exit;
			}
		}
		
		$mime = $img->mime();
		$format = 'jpg';
		switch ($mime) {
			case 'image/png':
				$format = 'png';
			break;
			
			case 'image/gif':
				$format = 'gif';
			break;
		}
		$data = $img->encode($format);
		$saved = @file_put_contents($to_img, $data);
		if ($saved === false) {
			exit;
		}
		return $img->response($format);
	}

	public function upload() {
		$type = Request::input('type');
		define('SERVER_IMAGE_UPLOAD_URL', Config::get('app.image_config.server.upload_url'));
        define('SERVER_IMAGE_TOKEN', Config::get('app.image_config.server.token'));
        define('SERVER_IMAGE_URL', Config::get('app.image_config.server.url'));
        define('SERVER_IMAGE_SAVE_PATH', Config::get('app.image_config.server.save_path'));

        $save_path	= trim(Request::input('key'));
		if (strpos($save_path, SERVER_IMAGE_SAVE_PATH) !== 0) {
			return $this->getOutput(false, '非法提交', '', $type);
		}

        $is_canvas = (int)Request::input('iscanvas');
		if ($is_canvas == 0) {
			if (!isset($_FILES['file'])) {
				return $this->getOutput(false, '非法提交', '', $type);
			}

			if ($_FILES['file']['size'] > Config::get('app.image_config.server.max_size')) {
				return $this->getOutput(false, '非法提交', '', $type);
			}
		} else {
			$image = Request::input('file');
			if (strpos($image, 'data:image/png;base64,') !== 0) {
				return $this->getOutput(false, '非法提交', '', $type);
			}
			/*if (strlen($image) - 22 > Config::get('app.image_config.server.max_size')) {
				return $this->getOutput(false, '非法提交', '', $type);
			}*/
		}

		$data = [
            'save_path' => [
                'name'  => 'key',
                'path'  => $save_path,
            ],
            'file_name' => 'file',
            'action'    => SERVER_IMAGE_UPLOAD_URL,
            'wap_action'    => SERVER_IMAGE_UPLOAD_URL,
            'image_url' => SERVER_IMAGE_URL.str_replace(SERVER_IMAGE_SAVE_PATH, '', $save_path) 
        ];
        
        $token = md5(http_build_query($data).'&'.SERVER_IMAGE_TOKEN.'&'.Request::ip());
        if ($token != Request::input('token')) {
        	return $this->getOutput(false, '非法提交', '', $type);
        }
        
        $image_url = $data['image_url'];

		$save_path = base_path().'/'.$save_path;
		$dirs = pathinfo($save_path);
		File::makeDirectory($dirs['dirname'], 0755, true);

		try {
			if ($is_canvas == 0) {
				$img = Image::make($_FILES['file']['tmp_name']);
			} else {
				$img = Image::make(Request::input('file'));
			}
			$mime = $img->mime();
			switch ($mime) {
				case 'image/png':
					$data = $img->encode('png');
				break;
				
				case 'image/gif':
					$data = $img->encode('gif');
				break;
				
				default:
					$data = $img->encode('jpg');
				break;
			}
			
            $this->_removeFiles($image_url);
			$saved = @file_put_contents($save_path, $data);
			if ($saved === false) {
				throw new Exception("Can't write image data to path ({$save_path})");
			}

			if ($type == 'app') {
				return $this->getOutput(true, '', $image_url, $type);
			}
			return Redirect::to(Request::input('success_action_redirect'));
		} catch (Exception $e) {
	    	return $this->getOutput(false, $e->getMessage(), '', $type);
	    }
	    exit;
	}

	private function getOutput($status, $message, $data, $type) {
		if ($type == 'app') {
			$result = ['code' => ($status ? 0 : 99999), 'msg' => $message, 'data' => $data];
		} else {
			$result = ['status' => $status, 'message' => $message];
		}
		return Response::json($result);
	}

	public function remove() {
		$this->_removeFiles(Request::input('files'));
	}
	
	private function _removeFiles($files) {
	    define('SERVER_IMAGE_URL', Config::get('app.image_config.server.url'));
	    define('SERVER_IMAGE_SAVE_PATH', Config::get('app.image_config.server.save_path'));

	    if (!is_array($files)) {
	        $files = array($files);
	    }
	    foreach ($files as $file) {
	        if (strpos($file, SERVER_IMAGE_URL) === 0) {
	            $file = str_replace(SERVER_IMAGE_URL , base_path().'/'.SERVER_IMAGE_SAVE_PATH, $file);
	            $paths = pathinfo($file);
	    
	            $path = $paths['dirname'];
	            $dir = dir($path);
	            while (false !== ($entry = $dir->read())) {
	                if ($entry != '.' && $entry != '..') {
	                    if(is_file($path.'/'.$entry)) {
	                        if (strpos($entry, $paths['basename']) === 0) {
	                            @unlink($path.'/'.$entry);
	                        }
	                    }
	                }
	            }
	        }
	    }
	}
}