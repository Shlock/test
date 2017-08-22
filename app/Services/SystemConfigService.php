<?php namespace YiZan\Services;

use YiZan\Models\SystemConfig;

class SystemConfigService extends BaseService {
	private static $_configs = null;

	//初始化配置
	public static function configInit() {
		if (self::$_configs === null) {
			self::$_configs = [];

			$configs = SystemConfig::get();
			foreach ($configs as $config) {
	        	self::$_configs['datas'][$config->code] = $config->val;
	        	$groups = explode(',', substr($config->group_code, 1, -1));
	        	foreach ($groups as $group) {
	        		self::$_configs['groups'][$group][$config->code] = $config->val;
	        	}
			}
		}
	}

	/**
	 * 根据配置信息
	 * @return array 配置信息
	 */
	public static function getConfigs() {
		self::configInit();
		return self::$_configs['datas'];
	}

	/**
	 * 根据分组获取相关配置
	 * @param  string $groupCode 分组
	 * @return array             配置信息
	 */
	public static function getConfigByGroup($groupCode) {
		self::configInit();
		return array_merge(self::$_configs['groups'][$groupCode], self::$_configs['groups']['all']);
	}

	public static function getConfigByCode($code) {
        self::configInit();
		return self::$_configs['datas'][$code];
	}

	public static function updateConfig($code, $val) {
		SystemConfig::where('code', $code)->update(array('val' => $val));
	}

	public static function getByCode($code){
		return SystemConfig::where('code', $code)->first();
	}
}
