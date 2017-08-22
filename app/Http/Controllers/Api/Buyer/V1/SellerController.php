<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\SellerService;
use YiZan\Services\SellerCateService;
use DB, View;
/**
 * 服务人员
 */
class SellerController extends BaseController {
	/**
	 * 列表
	 */
	public function lists() 
    {
		$data = SellerService::getSellerList(
                (int)$this->request('id'),
				max((int)$this->request('page'), 1),
				(int)$this->request('sort'),
				$this->request('keyword'),
                $this->userId,
                $this->request('mapPoint')
			);
        
		$this->outputData($data);
	}

	/**
	 * 详细
	 */
	public function detail() 
    {
        $data = SellerService::getSellerDetail((int)$this->request('id'), $this->userId);
		if (!$data) 
        {
			$this->outputCode(30001);
		}
        
		$this->outputData($data);
	}

	public function catelists() {
		$list = SellerCateService::getCatesAll((int)$this->request('id'), (int)$this->request('type'));
        $this->outputData($list);
	}

	public function getcate() {
        $data = SellerCateService::get((int)$this->request('id'));
        $this->outputData($data);
    }

    public function hotlists() 
    {
		$data = SellerService::getSellerList(
				(int)$this->request('id', 0),
				max((int)$this->request('page'), 1),
				(int)$this->request('sort', 1),
				$this->request('keyword'),
                $this->userId);
 
        $data = array_slice($data, 0, 5);
     
		$this->outputData($data);
	}

	public function check(){
		$data = SellerService::checkUser($this->userId);
		$this->output($data);
	}

	public function reg(){ 
		$result = SellerService::createSeller(
				$this->userId,
				(int)$this->request('sellerType'), 
				$this->request('logo'),
				$this->request('name'),
				$this->request('cateIds'),
				$this->request('address'),
                $this->request('addressDetail'),
                (int)$this->request('provinceId'),
                (int)$this->request('cityId'),
                (int)$this->request('areaId'),
                $this->request('mapPointStr'),
                $this->request('mapPosStr'),
				$this->request('mobile'), 
				$this->request('pwd'), 
				$this->request('idcardSn'),
				$this->request('idcardPositiveImg'),
				$this->request('idcardNegativeImg'),
				$this->request('businessLicenceImg'), 
				$this->request('introduction'),
				$this->request('serviceFee'), 
				$this->request('deliveryFee'),
                $this->request('contacts'),
                $this->request('serviceTel')
			);
		// if ($result['code'] == 0) {
		// 	$seller = $result['data'];
		// 	$this->createToken($seller->id, $seller->pwd);
		// 	$seller = $seller->toArray();
		// 	$result['data'] = ['seller' => $seller];
	 //    	$result['token'] = $this->token;
	 //    	$result['sellerId'] = $seller['id'];
		// 	$this->output($result);
		// }
		$this->output($result);
	}


    public function mappos() {
    	$data = array(
            'mapPoint' => strval($this->request('mapPoint')),
            'mapPos' => $this->request('mapPos'),
            'mapPosStr' => $this->request('mapPos'),
            'mapPointStr' => strval($this->request('mapPoint')),
        );
    	// var_dump($data);
    	// exit;
    	View::share('data', $data);
        return View::make('api.seller.reg');
    }

}