<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, File, Session, Cache, Redis;

/**
 * 缓存清理
 */
class CacheController extends AuthController {

	public function index() {
		return $this->display();
	}

	public function clear() {
		$data = $this->requestApi('cache.clear'); 
		echo 1;
	}

	public function local() {
		Cache::flush();
		File::deleteDirectory(storage_path('framework/views'), true);
		echo 1;
	}
}
