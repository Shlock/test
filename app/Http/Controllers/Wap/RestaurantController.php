<?php 
namespace YiZan\Http\Controllers\Wap;

use Input, Response;
/**
 * 资源上传
 */
class RestaurantController extends AuthController {
	/**
     * 收藏餐厅
     * type=1收藏 type=2取消收藏
     */
    public function collect() {
        $args = Input::all();
        if($args['type'] == 1) {
        	$result = $this->requestApi('collect.restaurant.create',$args);
        }
        else{
        	$result = $this->requestApi('collect.restaurant.delete',$args);
        }
       return Response::json($result);
    }


}
