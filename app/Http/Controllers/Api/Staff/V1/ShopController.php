<?php 
namespace YiZan\Http\Controllers\Api\Staff;
use YiZan\Services\Staff\SellerService;
use Lang, Validator, View, Input;

class ShopController extends BaseController {
	/**
	 * 获取店铺信息
	 */
	public function info() {
		$result = SellerService::getSellerInfo($this->sellerId);
		$this->outputData($result);
	}

    /**
     * 更新店铺信息
     */
    public function edit() {
        $result = SellerService::update(
            $this->sellerId,
            $this->request('shopdatas')
        );
        $this->outputData($result);
    }

    /**
     * 商家账单
     */
    public function account() {
        $result = SellerService::getSellerAccount(
            $this->sellerId,
            (int)$this->request('type'),
            (int)$this->request('status'),
            max((int)$this->request('page'), 1)
        );
        $this->outputData($result);
    }

    public function sellermap() {
        $option = array(
            'address' => strval($this->request('address')),
            'mapPoint' => strval($this->request('mapPoint')),
            'mapPos' => $this->request('mapPos'),
        );
        // var_dump($option);
        // exit;
        $data = SellerService::setSellerMap(
            $this->sellerId,
            $option
        );
        $args = [
            'token' => $this->token,
            'userId' => $this->userId
        ];
        //print_r($data);
        View::share('args', $args);
        View::share('data', $data);
        return View::make('api.seller.index');
    }

    
}