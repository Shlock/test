<?php
namespace YiZan\Utils;

class Helper {
	/**
	 * 根据编号生成路径
	 * @param  int $id 编号
	 * @return string  路径
	 */
	public static function getDirsById($id){
        $id = sprintf("%011d", $id);
        $dir1 = substr($id, 0, 4);
        $dir2 = substr($id, 4, 4);
        $dir3 = substr($id, -3);
        return $dir1.'/'.$dir2.'/'.$dir3;
    }

    public static function getSn() {
    	list($usec, $sec) = explode(" ",microtime());
		$sec = sprintf("%03d", (int)($usec * 1000));
		return Time::toDate(UTC_TIME, 'YmdHis').$sec.mt_rand(1000, 9999);
    }

    /**
     * @param  int $type 类型 1/数字，2/字母，0/字母数组混合
     * @param  int $num 位数 
     * @return [string] $code
     */
    public static function getCode($type,$num){ 
        $numCodeChar    = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0' ];
        $letterCodeChar = [ 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $mixCodeChar    = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $code = '';
        switch ($type) {
            case '1':
                for($i = 0 ;$i < $num ; $i++){ 
                    $code .= $numCodeChar[rand(0,count($numCodeChar)-1)];
                }
                break;
            case '2':
                for($i = 0 ;$i < $num ; $i++){
                    $code .= $letterCodeChar[rand(0,count($letterCodeChar)-1)];
                }
                break;
            
            default:
                for($i = 0 ;$i < $num ; $i++){
                    $code .= $mixCodeChar[rand(0,count($mixCodeChar)-1)];
                }
                break;
        }
        return $code;
    }

    public static function foramtMapPoint($point) {
        if(strpos($point, ',') === false){
            return false;
        }
        $arr = explode(',', $point);
        if(count($arr) != 2){
            return false;
        }
        
        $lng = (float)$arr[1];
        $lat = (float)$arr[0];
        if($lng < 0 || $lng > 180 || $lat < 0 || $lat > 90){
            return false;
        }
        return $lat.','.$lng;
    }

    public static function foramtMapPos($points) {
        $points = explode('|', $points);
        if(count($points) < 3){
            return false;
        }
        $pos = array();
        $str = array();
        foreach ($points as $point) {
            $arr = explode(',', $point);
            if(count($arr) != 2){
                return false;
            }

            $lng = (float)$arr[1];
            $lat = (float)$arr[0];
            if($lng < 0 || $lng > 180 || $lat < 0 || $lat > 90){
                return false;
            }
            $str[] = $lat.','.$lng;
            $pos[] = $lat.' '.$lng;
        }
        $pos[] = current($pos);
        return [
            'pos' => implode(',', $pos),
            'str' => implode('|', $str)
        ];
    }
}