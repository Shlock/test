<?php namespace YiZan\Utils;

use Config;

class Time {
	public static function getMicrotime(){
		return array_sum(explode(' ',microtime()));
	}

	public static function getTime(){
		return time() - date('Z');
	}

	public static function getNowDay(){
		return mktime(0, 0, 0, date('m'), date('d'), date('Y')) - date('Z');
	}

	public static function getNowHour(){
		return mktime(date('H'), 0, 0, date('m'), date('d'), date('Y')) - date('Z');
	}

	public static function getNowMinute(){
		return mktime(date('H'), date('i'), 0, date('m'), date('d'), date('Y')) - date('Z');
	}

	public static function getNextMonth(){
		return mktime(0, 0, 0, date('m') + 1, 1, date('Y')) - date('Z');
	}

	//获取本周第一天时间戳
	public static function getWeekFirstDay() {
		return mktime(0, 0, 0, date('m'),date('d')-date('w')+1,date('Y')) - date('Z');
	}

	//获取本周最后一天时间戳
	public static function getWeekLastDay() {
		return mktime(23, 59, 59, date('m'),date('d')-date('w')+7,date('Y')) - date('Z');
	}

	//获取本月第一天时间戳
	public static function getMonthFirstDay() {
		return mktime(0, 0, 0, date('m'),1,date('Y')) - date('Z');
	}

	//获取本月最后一天时间戳
	public static function getMonthLastDay() {
		return mktime(23, 59, 59, date('m'),date('t'),date('Y')) - date('Z');
	}

	public static function toDate($time = NULL, $format = 'Y-m-d H:i:s'){
		if(empty($time)){
			return '';
		}
		if (!is_numeric($time)) {
			return $time;
		}
		$time += date('Z');
		$format = str_replace('#',':',$format);
		return date($format, $time);
	}

	public static function toTime($str){
		$str = trim($str);
		if(empty($str)){
			return 0;
		}
		return strtotime($str) - date('Z');
	}

	public static function toDayTime($time){
		if(0 > $time){
			return 0;
		}
		$str = self::toDate($time,'Y-m-d');
		return strtotime($str) - date('Z');
	}

	public static function toHourTime($time){
		if(0 > $time){
			return 0;
		}
		$str = self::toDate($time,'Y-m-d H:00');
		return strtotime($str) - date('Z');
	}

	public static function toMinuteTime($time){
		if(0 > $time){
			return 0;
		}
		$str = self::toDate($time,'Y-m-d H:i');
		return strtotime($str) - date('Z');
	}

	/**
	 * 获取指定时间与当前时间的时间间隔
	 * @param   integer      $time
	 * @return  string
	 */
	public static function getBeforeTimelag($time){
		if($time == 0)
			return "";

		static $today_time = NULL,
				$before_lang = NULL,
				$beforeday_lang = NULL,
				$today_lang = NULL,
				$yesterday_lang = NULL,
				$hours_lang = NULL,
				$minutes_lang = NULL,
				$months_lang = NULL,
				$date_lang = NULL,
				$sdate = 86400;

		if($today_time === NULL)
		{
			$today_time = self::getTime();
			$before_lang = "前";
			$beforeday_lang = "前天";
			$today_lang = "今天";
			$yesterday_lang = "昨天";
			$hours_lang = "小时";
			$minutes_lang = "分钟";
			$months_lang = "月";
			$date_lang = "日";
		}

		$now_day = self::toTime(self::toDate($today_time,"Y-m-d")); //今天零点时间
		$pub_day = self::toTime(self::toDate($time,"Y-m-d")); //发布期零点时间

		$timelag = $now_day - $pub_day;

		$year_time = self::toDate($time,'Y');
		$today_year = self::toDate($today_time,'Y');

		if($year_time < $today_year)
			return self::toDate($time,'Y:m:d H:i');

		$timelag_str = self::toDate($time,' H:i');

		$day_time = 0;
		if($timelag / $sdate >= 1)
		{
			$day_time = floor($timelag / $sdate);
			$timelag = $timelag % $sdate;
		}

		switch($day_time)
		{
			case '0':
				$timelag_str = $timelag_str;
			break;

			default:
				$timelag_str = $day_time."天前";
			break;
		}
		return $timelag_str;
	}

	/**
	 * 获取当前时间与指定时间的时间间隔
	 *
	 * @access  public
	 * @param   integer      $time
	 *
	 * @return  string or array
	 */
	public static function getEndTimelag($time) {
		if ($time == 0) {
			return "";
		}

		static $sdate = 86400,
				$shours = 3600,
				$sminutes = 60;
		
		$today_time = self::getTime();
		$timelag_arr = array('d'=>0,'h'=>0,'m'=>0,'s'=>0);
		$timelag = ($time - $today_time);
		
		if($timelag / $sdate >= 1) {
			$timelag_arr["d"] = floor($timelag / $sdate);
			$timelag = $timelag % $sdate;
		}
		
		if($timelag / $shours >= 1) {
			$timelag_arr["h"] = floor($timelag / $shours);
			$timelag = $timelag % $shours;
		}
		
		if($timelag / $sminutes >= 1) {
			$timelag_arr["m"] = floor($timelag / $sminutes);
			$timelag = $timelag % $sminutes;
		}
		
		if($timelag > 0) {
			$timelag_arr["s"] = $timelag;
		}

		$str = '';
		if ($timelag_arr["d"] > 0) {
			$str .= $timelag_arr["d"].'天';
		}

		if ($timelag_arr["h"] > 0) {
			$str .= $timelag_arr["h"].'小时';
		}

		if ($timelag_arr["m"] > 0) {
			$str .= $timelag_arr["m"].'分';
		}

		if ($timelag_arr["s"] > 0) {
			$str .= $timelag_arr["s"].'秒';
		}
		
		return $str;
	}

	/*
	 * 日期信息返回
	 */
	public static function getWeek($date,$type = "m月d日") {
	 	$info=self::toDate($date[0],$type);
	 	$wday="";
	 	switch($date['wday']){
	 		case 0:
	 		$wday="星期天";
	 		break;
	 		case 1:
	 		$wday="星期一";
	 		break;
	 		case 2:
	 		$wday="星期二";
	 		break;
	 		case 3:
	 		$wday="星期三";
	 		break;
	 		case 4:
	 		$wday="星期四";
	 		break;
	 		case 5:
	 		$wday="星期五";
	 		break;
	 		case 6:
	 		$wday="星期六";
	 		break;
	 	}
	 	$info.=" $wday";
	 	return $info;
	}

	/*
	 * 格式化开始到结束时间的周相关时间
	 */
	public static function getWeekHoursByTime($beginTime, $endTime) {
		$beginDay	= self::toDayTime($beginTime);
		$endDay		= self::toDayTime($endTime);

		$weeks = [];
		if ($beginDay == $endDay) {
			$week = self::toDate($beginTime, 'w');
			$weeks[$week] = [
				'begin' => self::toDate($beginTime, 'H:i'),
				'end'	=> self::toDate($endTime - 1, 'H:i:s')
			];
		} else {
			$day = $beginDay;
			for ($day; $day <= $endDay; $day += 86400) {
				$week = self::toDate($day, 'w');
				//开始时间时
				if ($day == $beginDay) {
					$weeks[$week] = [
						'begin' => self::toDate($beginTime, 'H:i'),
						'end'	=> '23:59:59'
					];
				} elseif ($day == $endDay) {
					$weeks[$week] = [
						'begin' => '00:00',
						'end'	=> self::toDate($endTime - 1, 'H:i:s')
					];
				} else {
					$weeks[$week] = [
						'begin' => '00:00',
						'end'	=> '23:59:59'
					];
				}
			}
		}
		return $weeks;
	}

	/**
	 * 获取自由组合时分秒
	 */
	public static function getHouerMinuteSec($houer=true, $minute=true, $sec=true) {
		$time = [];
		if($houer == true) {
			for ($i=0; $i < 24; $i++) {
				if($i < 10){
					$time['houer'][$i]= '0'.$i;
				}else{
					$time['houer'][$i]= $i;
				}
			}
		}
		if($minute == true) {
			for ($i=0; $i < 60; $i++) { 
				if($i < 10){
					$time['minute'][$i]= '0'.$i;
				}else{
					$time['minute'][$i]= $i;
				}
			}
		}
		if($sec == true) {
			for ($i=0; $i < 60; $i++) { 
				if($i < 10){
					$time['sec'][$i]= '0'.$i;
				}else{
					$time['sec'][$i]= $i;
				}
			}
		}
		return $time;
	}
}
