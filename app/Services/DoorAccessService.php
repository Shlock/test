<?php namespace YiZan\Services;


use YiZan\Utils\Http; 
use YiZan\Utils\Time; 
use Config;
class DoorAccessService extends BaseService{

    public static $isAccessInterface = false;

 	/**
 	 * 设备接口注册
 	 * @param string  $departid 				小区编号
 	 * @param string  $pid 						设备编号
 	 * @param string  $installLockName 			设备名称
 	 * @param string  $installAddress 			设备安装地址
 	 * @param string  $installGps 				设备GPS
 	 * @param string  $installWork 				设备安装人
 	 * @param string  $installTelete 			设备安装人电话
 	 * @param string  $installComm 				设备安装备注
 	 * @return array $result 					安装结果
 	 */
	public static function deviceActivation($departid, $pid, $installLockName, $installAddress, $installGps, $installWork, $installTelete, $installComm){
		$data = [
			'app_key' => Config::get('app.lock_sdk_api.app_key'),
			'agt_num' => Config::get('app.lock_sdk_api.agt_num'),
			'pid' => $pid,
			'departid' => $departid, 
			'install_lock_name' => $installLockName,
			'install_address' => $installAddress,
			'install_gps' => $installGps,
			'install_work' => $installWork,
			'install_telete' => $installTelete,
			'intall_comm' => $installComm,
		];

		$result = Http::post(Config::get('app.lock_sdk_api.install_lock_url_test'), $data);
		//file_put_contents('C:\wamp\www\o2o\branches\community\storage\logs\app1.log', print_r($result, true) . '---' . print_r($data, true), FILE_APPEND);
		return json_decode($result, true);
	} 

 	/**
 	 * 钥匙凭证申请
 	 * @param string  $pid 						门禁编号
 	 * @param string  $userId 					用户手机号
 	 * @param string  $validity 				设备名称
 	 * @param string  $aucno 					车牌号
 	 * @param string  $auname 					用户姓名
 	 * @param string  $auno 					身份证号
 	 * @param string  $autel 					备用手机号
 	 * @param string  $aucommon 				其他说明
 	 * @return array $result 					
 	 */
	public static function keyApply($pid, $userId, $validity, $aucno, $auname, $auno, $autel, $aucommon){
		$data = [
			'app_key' => Config::get('app.lock_sdk_api.app_key'),
			'agt_num' => Config::get('app.lock_sdk_api.agt_num'),
			'pid' => $pid,
			'user_id' => $userId,
			'validity' => Time::toDate($validity, 'Ymd'),
			'aucno' => $aucno,
			'auname' => $auname,
			'auno' => $auno,
			'autel' => $autel,
			'aucommon' => $aucommon,
		];

		$result = Http::post(Config::get('app.lock_sdk_api.qry_keys_url_test'), $data);
		//file_put_contents('C:\wamp\www\o2o\branches\community\storage\logs\app2.log', print_r($result, true) . '---' . print_r($data, true), FILE_APPEND);
		return json_decode($result, true);
	}

 	/**
 	 * 添加小区
 	 * @param string  $depart_name 				小区名称 
 	 * @param string  $city_code 				所在地区 
 	 * @param string  $depart_tel 				联系电话 
 	 * @param string  $depart_mail 				电子邮箱 
 	 * @param string  $street 					街道/村/门牌号 
 	 * @param string  $address 					通讯地址 
 	 * @param string  $common 					说明 
 	 * @return array $result 					
 	 */
	public static function addDistrict($depart_name, $city_code, $depart_tel, $depart_mail, $street, $address, $common){
		$data = [
			'app_key' => Config::get('app.lock_sdk_api.app_key'),
			'agt_num' => Config::get('app.lock_sdk_api.agt_num'),
			'depart_name' => $depart_name, 
			'city_code' => $city_code, 
			'depart_tel' => $depart_tel, 
			'depart_mail' => $depart_mail, 
			'street' => $street, 
			'address' => $address, 
			'common' => $common, 
		];
		// echo Config::get('app.lock_sdk_api.add_community_url');
		$result = Http::post(Config::get('app.lock_sdk_api.add_community_url'), $data); 
		// print_r($result);
		//file_put_contents('C:\wamp\www\o2o\branches\community\storage\logs\app3.log', print_r($result, true) . '---' . print_r($data, true), FILE_APPEND);
		return json_decode($result, true);
	}

 	/**
 	 * 查询小区
 	 * @param string  $departId 				组织机构编号 
 	 * @return array $result 					
 	 */
	public static function getDistrict($departId){
		$data = [
			'app_key' => Config::get('app.lock_sdk_api.app_key'),
			'agt_num' => Config::get('app.lock_sdk_api.agt_num'),
			'departId' => $departId, 
		];

		$result = Http::post(Config::get('app.lock_sdk_api.get_community_url'), $data);
		//file_put_contents('C:\wamp\www\o2o\branches\community\storage\logs\app4.log', print_r($result, true) . '---' . print_r($data, true), FILE_APPEND);
		return json_decode($result, true);
	}

}