<?php namespace YiZan\Utils;

use Illuminate\Validation\Validator;

class ValidatorExtend extends Validator {
	/**
	 * 验证地图定位
	 */
	public function validatePoint($attribute, $value, $parameters) {
        return Helper::foramtMapPoint($value) !== false;
    }

    /**
	 * 大于
	 */
	public function validateGt($attribute, $value, $parameters) {
        return $value > $parameters[0];
    }

    /**
	 * 大于等于
	 */
	public function validateEgt($attribute, $value, $parameters) {
        return $value >= $parameters[0];
    }

    /**
	 * 小于
	 */
	public function validateLt($attribute, $value, $parameters) {
        return $value < $parameters[0];
    }

    /**
	 * 验证字符串长度
	 */
	public function validateLength($attribute, $value, $parameters) {
        return strlen($value) == $parameters[0];
    }

    /**
	 * 验证是否为指定值
	 */
	public function validateEq($attribute, $value, $parameters) {
        return $value == $parameters[0];
    }
	
	/**
	 * 验证手机号码
	 */
	public function validateMobile($attribute, $value, $parameters) {
        $pattern = '/^1\d{10}$/';
		return preg_match($pattern,$value);
    }
}
