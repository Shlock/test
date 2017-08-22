<?php
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\WeixinService;
class UseractiveController extends BaseController{

    /**
     * 获取微信JS信息配置
     */
    public function getweixin() {
        $payment = WeixinService::getweixin($this->request('url'));

        $this->outputData($payment);
    }

    /**
     *
     */
    public function share(){

        $userId = $this->userId;
        $host = $_SERVER['HTTP_HOST'];
        $host = str_replace('api', 'wap', $host);
    	$shareUrl = "http://{$host}/User/share?sharedByUserId={$userId}";
    	$qrcodeUrl = "http://www.wwei.cn/Uploads/qrcodetemp/2016/05/21/574007ef17d99.png";
    	$encodeUrl = urlencode($shareUrl);
    	$requestQrcodeUrl="http://api.wwei.cn/wwei.html?data={$encodeUrl}&version=1.0&apikey=20141110217674";

    	$content = file_get_contents($requestQrcodeUrl);
    	$response = json_decode($content);

    	if($response->data->qr_filepath){
    		$qrcodeUrl = $response->data->qr_filepath;
    	}

    	$data = array(
    		'shareUrl' => $shareUrl,
    		'qrcodeUrl' => $qrcodeUrl
    	);
    	$this->outputData($data);
    }


}
