<?php namespace YiZan\Services;
use YiZan\Models\Vehicle;
use YiZan\Utils\Time;
use DB, Validator;
/**
 * 车辆信息
 */
class VehicleService extends BaseService 
{ 
    /**
     * Summary of getList
     * @param mixed $userId 
     * @param mixed $page 
     * @param mixed $pageSize 
     * @return mixed
     */
    public static function getList($userId, $page, $pageSize = 20) 
    {
        return Vehicle::where('user_id', $userId)
            ->orderBy('status', "DESC")
            ->orderBy('create_time', "DESC")
            ->with('brand','series')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
    }
     /**
     * 添加车辆信息
     * @param int $userId 会员ID
     * @return array 
     */
    public static function insert($userId,$plateNumber,$appellation,$image,$brandId,$carColor,$seriesId)
    {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
        
        $rules = array(
            'userId'        => ['required'],
            'plateNumber'   => ['required'],
            'appellation'   => ['required'],
            'image'         => ['required'],
            'brandId'       => ['required'],
            'seriesId'       => ['required'],
            'carColor'      => ['required']
        );

        $messages = array
        (
            'userId.required'           => 77000,    //  用户Id不能为空
            'plateNumber.required'      => 77001,    //  车牌号不能为空
            'appellation.required'      => 77002,    //  车辆称呼不能为空
            'image.required'            => 77003,    //  车辆图片不能为空
            'brandId.required'          => 77004,    //  车型不能为空
            'seriesId.required'         => 77011,    //  车系不能为空
            'carColor.required'         => 77005     //  车辆颜色不能为空
        );

        $validator = Validator::make(
            [
                'userId'        => $userId,
                'plateNumber'   => $plateNumber,
                'appellation'   => $appellation,
                'image'         => $image,
                'brandId'       => $brandId,
                'seriesId'      => $seriesId,
                'carColor'      => $carColor
            ], $rules, $messages
        );
        if ($validator->fails()) {                  //验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        if (count($image) > 0) {
           $image = self::moveUserImage($userId,$image);
            if (!$image) {
                $result['code'] = 50004;
                return $result;
            }
        } else {
            $image = '';
        }
        $vehicle = new Vehicle;
        $vehicle->user_id    	  = $userId;
        $vehicle->plate_number    = $plateNumber;
        $vehicle->appellation     = $appellation;
        $vehicle->image           = $image;
        $vehicle->brand_id        = $brandId;
        $vehicle->series_id       = $seriesId;        
        $vehicle->car_color       = $carColor;   
        $vehicle->create_time     = UTC_TIME;     
        $vehicle->status = 0; 

	    DB::beginTransaction();
    	try {    		
        	$vehicle->save();
	    	DB::commit();
	        $result['data'] = Vehicle::where('user_id',$userId)->with('brand','series')->find($vehicle['id']);
    	} catch (Exception $e) {
    		DB::rollback();
    		$result['code'] = 77009;
    	}
        return $result;
    }
     /**
     * 更新车辆信息
     * @param int $userId 会员ID
     * @param int $ids 车信息编号
     * @return array 
     */
    public static function update($userId,$id,$plateNumber,$appellation,$image,$brandId,$carColor,$seriesId)
    {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
        $vehicle = Vehicle::where('user_id',$userId)->find($id);
        if(empty($vehicle)){
            $result['code'] = 77006;   //  车辆信息不存在
            return $result;
        }

        $rules = array(
            'userId'        => ['required'],
            'id'            => ['required'],
            'plateNumber'   => ['required'],
            'appellation'   => ['required'],
            'image'         => ['required'],
            'brandId'       => ['required'],
            'seriesId'      => ['required'],
            'carColor'      => ['required']
        );

        $messages = array
        (
            'userId.required'           => 77000,    //  用户Id不能为空
            'id.required'               => 77006,    //  用户Id不能为空
            'plateNumber.required'      => 77001,    //  车牌号不能为空
            'appellation.required'      => 77002,    //  车辆称呼不能为空
            'image.required'            => 77003,    //  车辆图片不能为空
            'brandId.required'          => 77004,    //  车型不能为空
            'seriesId.required'         => 77011,    //  车型不能为空
            'carColor.required'         => 77005     //  车辆颜色不能为空
        );

        $validator = Validator::make(
            [
                'userId'        => $userId,
                'id'            => $id,
                'plateNumber'   => $plateNumber,
                'appellation'   => $appellation,
                'image'         => $image,
                'brandId'       => $brandId,
                'seriesId'       => $seriesId,
                'carColor'      => $carColor
            ], $rules, $messages
        );
        if ($validator->fails()) {                  //验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        $image = self::moveUserImage($userId,$image);
        if (!$image) {
            $result['code'] = 50004;
            return $result;
        }
        DB::beginTransaction();
    	try {    		
        	Vehicle::where("user_id", $userId)->where("id", $id)->update(
	            [
	                "plate_number"      => $plateNumber,
	                "appellation"       => $appellation,
	                "image"             => $image,
	                "brand_id"          => $brandId,
	                "series_id"         => $seriesId,
	                "car_color"         => $carColor
	            ]
	        );
	    	DB::commit();	    	
	        $result['data'] = Vehicle::where('user_id',$userId)->with('brand','series')->find($id);
    	} catch (Exception $e) {
    		DB::rollback();
    		$result['code'] = 77009;
    	}
        return $result;
    }
    /**
     * 删除车辆信息
     * @param int $userId 会员ID
     * @param int $ids 车信息编号
     * @return array 
     */
    public static function delete($userId, $ids) 
    {
    	$result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
        if( !is_array( $ids ) ){
            $ids = [  '0' => $ids ];
        }        
        if( !empty($ids))
        {        
          $result = Vehicle::where("user_id", $userId)->whereIn("id", $ids)->delete();
        }else{
        	$result['code'] = 77010;
        }
        return $result;
    }
    /**
     * 获取车辆消息
     * @param int $userId 会员ID
     * @param int $ids 车辆信息ID
     * @return array 
     */
    public static function getdatas($userId, $id)
    {
        return Vehicle::where('user_id', $userId)
            ->where('id', $id)
            ->with('brand','series')
            ->first();
    }
    /**
     * 获取车辆消息
     * @param int $userId 会员ID
     * @param int $ids 车辆信息ID
     * @return array 
     */
    public static function getstatus($userId)
    {
        $data = Vehicle::where('user_id', $userId)
            ->where('status','1')
            ->with('brand','series')
            ->first();
        if(empty($data)){
            $data = Vehicle::where('user_id', $userId)
                ->orderBy('status', "DESC")
                ->with('brand','series')
                ->first();
            if(!empty($data)){
                Vehicle::where('user_id', $userId)->where('id', $data['id'])->update(["status"=> 1]);
                $data['status'] =1;
            }
        }
        return  $data;
    }
    /**
     * 默认车辆信息
     * @param int $userId 会员ID
     * @param int $ids 车辆信息ID
     * @return array 
     */
    public static function setstatus($userId,$id)
	{	 
		$result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
    	DB::beginTransaction();
    	try{
    		Vehicle::where('user_id', $userId)->update(["status"=> 0]);
    		$result = Vehicle::where('user_id', $userId)->where('id', $id)->update(["status"=> 1]);
	    	DB::commit();
	    } catch (Exception $e) {
	    	throw $e;
    		DB::rollback();
            $result['code'] = 77007;   //  设置默认失败	    	
    	}
    	return $result;
    }
}