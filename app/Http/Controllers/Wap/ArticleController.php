<?php 
namespace YiZan\Http\Controllers\Wap;
use Input, View;
/**
 * 文章控制器
 */
class ArticleController extends BaseController { 

	public function detail() {
		$result = $this->requestApi('article.get', Input::all());
		if ($result['code'] == 0) {
			View::share('title',"- ".$result['data']['title']);
			View::share('data',$result['data']);
		}
		return $this->display();	
	}

	public function detailapp() {
		$result = $this->requestApi('article.get', Input::all());
		if ($result['code'] == 0) {
			View::share('data',$result['data']);
		}
		View::share('is_show_top',false);
		return $this->display();	
	}

	/*
	* 社区公告详情
	*/
	public function propertyarticle() {
        $data = $this->requestApi('article.get', ['id'=>Input::get('id')]);
        //print_r($data);
        View::share('data', $data['data']);

        $result = $this->requestApi('article.read', ['id'=>Input::get('id')]);
        return $this->display();
    }
}