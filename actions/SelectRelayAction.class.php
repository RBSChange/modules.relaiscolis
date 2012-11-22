<?php
/**
 * relaiscolis_SelectRelayAction
 * @package modules.relaiscolis.actions
 */
class relaiscolis_SelectRelayAction extends shipping_SelectRelayAction
{
	/**
	 * @param integer $modeId
	 * @return relaiscolis_persistentdocument_relaiscolismode
	 */
	protected function getMode($modeId)
	{
		return relaiscolis_persistentdocument_relaiscolismode::getInstanceById($modeId);
	}
}