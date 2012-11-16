<?php
/**
 * @package modules.relaiscolis.lib.services
 */
class relaiscolis_ModuleService extends ModuleBaseService
{
	/**
	 * Singleton
	 * @var relaiscolis_ModuleService
	 */
	private static $instance = null;
	
	/**
	 * @return relaiscolis_ModuleService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

}