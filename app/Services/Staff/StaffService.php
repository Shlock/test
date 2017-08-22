<?php namespace YiZan\Services\Staff;
use YiZan\Models\Staff\SellerStaff;
use YiZan\Models\StaffMap;
use YiZan\Models\Seller;
use YiZan\Models\System\User;
use YiZan\Utils\Helper;

use YiZan\Services\UserService;
use YiZan\Services\SellerStaffService;
use YiZan\Services\SellerService;

use DB, Lang,Exception;
class StaffService extends \YiZan\Services\SellerStaffService {

    /**
     * 更改员工信息
     * @param $staffId 员工编号
     * @param $name 用户昵称
     * @param $avatar 头像
     */
    public static function updateInfo($userId, $avatar) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => Lang::get('api_staff.success.update')
        ];

        if ($avatar == '') {
            $result['code'] = 60001;
            return $result;
        }
        $user = User::where('id', $userId)->first();
        $staff = SellerStaffService::getByUserId($userId);
        $seller = SellerService::getById(0, $userId);

        //找不到卖家信息,则返回
        if (!$staff && !$seller) 
        {
            $result['code'] = 10108;
            return $result;
        }
        if ($seller) {
            $role = 1; // 只是商家，不是人员
            $sellerId = $seller->id;
            $staffId = 0;
            $mobile = $seller->mobile;
            $name = $seller->name;
        }
        if ($staff) {
            switch ($staff->type) {
                case 0:
                    $role |= 7; //既是商家又是人员
                    break;
                case 1:
                    $role |= 2; //配送人员
                    break;
                case 2:
                    $role |= 4; // 服务人员
                    break;
                case 3:
                    $role |= 6; //既是配送又是服务人员
                    break;
            }
            $staffId = $staff->id;
            $sellerId = $staff->seller_id;
            $mobile = $staff->mobile;
            $name = $staff->name;
        }
        //头像图片上传
       // var_dump($avatar);
        $logo = $avatar;
        if ($avatar != $user->avatar) {
            $avatar = self::moveUserImage($user->id, $avatar);
          //  var_dump($avatar);
            if (!$avatar) {
                $result['code'] = 10113;
                return $result;
            }
        }

        $data = [
            'avatar' => $avatar,
        ];
 
        if ($role == 2 || $role == 4) {
            if ($logo != $staff->avatar) {
                $logo = self::moveStaffImage($staff->seller_id, $staff->id, $logo);
                if (!$logo) {//转移图片失败
                    $result['code'] = 10202;
                    return $result;
                }
            }
            $staffdata = [
                'avatar' => $logo,
            ];
            if (false === SellerStaff::where('id', $staff->id)->update($staffdata)) {
                $result['code'] = 60004;
                return $result;
            }
        }
        if (false === User::where('id', $userId)->update($data)) {
            $result['code'] = 60004;
            return $result;
        }

        $result['data'] = 
            [
                "id"=> $userId,
                "staffId" => $staffId,
                "sellerId"=> $sellerId,
                "mobile"=>$mobile,
                "name"=> $name,
                "avatar"=>$avatar,
                "role" => $role,
                'bg' => asset('wap/community/client/images/top-bg.png'),
            ];
        
        return $result;
    }

    /**
     *
     * @param $staffId 员工编号
     * @param $address 地址
     * @param $mapPoint 地图坐标
     */
    public static function address($staffId, $address, $mapPoint) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => Lang::get('api_staff.success.update')
        ];
        if ($address == '') {
            $result['code'] = 60005;
            return $result;
        }
        if ($mapPoint == '') {
            $result['code'] = 60006;
            return $result;
        }
        $mapPoint = Helper::foramtMapPoint($mapPoint);
        if (!$mapPoint){
            $result['code'] = 60006;    // 地图定位错误
            return $result;
        }
        //更新员工表
        $format_map_point = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
        $update = SellerStaff::where('id', $staffId)
                            ->update([
                                'address' => $address,
                                'map_point' => $format_map_point,
                                'map_point_str' => $mapPoint
                            ]);
        if ($update === false) {
            $result['code'] = 60004;
            return $result;
        }
        //更新员工坐标表
        $seller_id = SellerStaff::where('id', $staffId)->pluck('seller_id');
        $map = StaffMap::where('staff_id', $staffId)->first();
        if ($map) {
            StaffMap::where('staff_id', $staffId)->update(['map_point' => $format_map_point]);
        } else {
            StaffMap::insert([
                'seller_id' => $seller_id,
                'staff_id' => $staffId,
                'map_point' => $format_map_point
            ]);
        }
        return $result;
    }

    /**
     * 更新服务范围
     * @param $staffId 员工编号
     * @param $mapPos 服务范围地图坐标
     */
    public static function range($staffId, $mapPos) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => Lang::get('api_staff.success.update')
        ];

        if ($mapPos == '') {
            $result['code'] = 60007;
            return $result;
        }
        $mapPos = Helper::foramtMapPos($mapPos);
        if (!$mapPos){
            $result['code'] = 60007;    // 服务范围错误
            return $result;
        }
        //更新员工表
        $format_map_pos = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
        $update = SellerStaff::where('id', $staffId)
            ->update([
                'map_pos' => $format_map_pos,
                'map_pos_str' => $mapPos["str"]
            ]);
        if ($update === false) {
            $result['code'] = 60004;
            return $result;
        }
        //更新员工坐标表
        $seller_id = SellerStaff::where('id', $staffId)->pluck('seller_id');
        $map = StaffMap::where('staff_id', $staffId)->first();
        if ($map) {
            StaffMap::where('staff_id', $staffId)->update(['map_pos' => $format_map_pos]);
        } else {
            StaffMap::insert([
                'seller_id' => $seller_id,
                'staff_id' => $staffId,
                'map_pos' => $format_map_pos
            ]);
        }
        return $result;
    }	    
    /**
     * 检测验证码
     * @param $mobile 手机号码
     * @param $verifyCode 验证码
     */
    public static function verifymobile($staffId,$mobile, $verifyCode) {
         $result = [
            'code'	=> 0,
            'msg' => Lang::get('api_staff.success.verify_error')
        ];
        if(UserService::checkVerifyCode($verifyCode, $mobile) == false)
        {
           $result['code'] = 10121;
           $result['data'] = false;  
        }else {
           $result['code'] = 10122;
           $result['data'] = true;              
        }
        return $result;
    }
    /**
     * 检测验证码
     * @param $mobile 手机号码
     * @param $verifyCode 验证码
     */
    public static function mobile($userId,$oldmobile,$mobile, $verifyCode) {
    
        if(UserService::checkVerifyCode($verifyCode, $mobile) == false){
           $result['code'] = 10104;
           return $result;
        }else{
            $xuser = UserService::getByMobile($mobile);            
            //找到会员时
            if ($xuser)
            {
                $result['code'] = 10118;
                return $result;
            }else{                
                $ouser = UserService::getByMobile($oldmobile);
                if ($ouser->id > 0)
                {
                    DB::beginTransaction();
                    try {                        
                        User::where('mobile',$oldmobile)->where("id",$ouser->id)->update(['mobile'=>$mobile]); 
                        $staff = SellerStaffService::getByUserId($ouser->id);
                        $seller = SellerService::getById(0, $ouser->id);
                        if ($staff) {
                            SellerStaff::where('user_id', $ouser->id)->update(['mobile' => $mobile]);
                        }
                        if ($seller) {
                            Seller::where('user_id', $ouser->id)->update(['mobile' => $mobile]);
                        }
                        DB::commit();
                        $result['code'] = 0;
                        $result['data'] = UserService::getByMobile($mobile);
                    } catch (Exception $e) {
                        DB::rollback();
                        $result['code'] = 60013;
                    }
                }else{
                    $result['code'] = 10119;
                }                
            }
        }
        return $result;
    }
}
