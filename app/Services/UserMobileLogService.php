<?php namespace YiZan\Services;

use YiZan\Models\UserMobileLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserMobileLogService extends BaseService
{
    public static function record($uid, $lat, $lng, $device_no, $device_type, $os_version, $ip, $time, $type)
    {
        $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $time);
        $timestamp = $datetime->getTimestamp();
        $requests = [
            'uid' => $uid,
            'lat' => $lat,
            'lng' => $lng,
            'device_no' => $device_no,
            'device_type' => $device_type,
            'os_version' => $os_version,
            'ip' => $ip,
            'time' => $timestamp,
            'type' => $type
        ];
        $validator = Validator::make($requests, self::$rules);
        if ($validator->fails()) {
            return ['code' => 1, 'data' => null, 'msg' => 'data invalid'];
        }
        $ret = UserMobileLog::create($requests);
        if ($ret) {
            return ['code' => 0, 'data' => null, 'msg' => 'success'];
        } else {
            return ['code' => 1, 'data' => null,
                'msg' => 'craete mobile log record failed, please contact to server administrator.'];
        }
    }

    private static $rules = [
        'uid' => 'required|digits_between:1,11',
        'lat' => 'required',
        'lng' => 'required',
        'device_no' => 'required',
        'device_type' => 'required|alpha_num',
        'os_version' => 'required',
        'ip' => 'required|ip',
        'time' => 'required|numeric',
    ];
}

