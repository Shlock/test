<?php namespace YiZan\Models;

class SystemConfig extends Base {
	/**
	 * 系统配置信息集合
	 * @var array
	 */
	private static $_configs = null;

    /**
     * 根据配置代码获取相关配置信息
     * @param  string $code 配置代码
     * @return string       配置信息
     */
	public static function getConfig($code) 
    {
        if (self::$_configs === null) {
        	self::$_configs = [];

            $configs = self::all();
            foreach ($configs as $config) {
			    self::$_configs[$config->code] = $config->val;
			}
        }
        return self::$_configs[$code];
	}
    /**
     * 重置配置信息
     */
    public static function resetConfig()
    {
        self::$_configs = null;
    }
}
