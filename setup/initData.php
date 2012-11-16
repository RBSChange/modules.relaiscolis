<?php
/**
 * @package modules.relaiscolis.setup
 */
class relaiscolis_Setup extends object_InitDataSetup
{
	public function install()
	{
		$mbs = uixul_ModuleBindingService::getInstance();
		$mbs->addImportInPerspective('shipping', 'relaiscolis', 'shipping.perspective');
		$mbs->addImportInActions('shipping', 'relaiscolis', 'shipping.actions');
		$result = $mbs->addImportform('shipping', 'modules_relaiscolis/relaiscolismode');
		
		if ($result['action'] == 'create')
		{
			uixul_DocumentEditorService::getInstance()->compileEditorsConfig();
		}
		
		f_permission_PermissionService::getInstance()->addImportInRight('shipping', 'relaiscolis', 'shipping.rights');
	}

	/**
	 * @return String[]
	 */
	public function getRequiredPackages()
	{
		// Return an array of packages name if the data you are inserting in
		// this file depend on the data of other packages.
		// Example:
		// return array('modules_website', 'modules_users');
		return array();
	}
}