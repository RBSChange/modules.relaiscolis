<?php
/**
 * relaiscolis_SelectRelayAction
 * @package modules.relaiscolis.actions
 */
class relaiscolis_SelectRelayAction extends shipping_SelectRelayAction
{
	protected function getMode($modeId)
	{
		return relaiscolis_persistentdocument_relaiscolismode::getInstanceById($modeId);
	}

}