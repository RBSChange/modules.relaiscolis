<?php
/**
 * relaiscolis_RelaiscolismodeScriptDocumentElement
 * @package modules.relaiscolis.persistentdocument.import
 */
class relaiscolis_RelaiscolismodeScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return relaiscolis_persistentdocument_relaiscolismode
     */
    protected function initPersistentDocument()
    {
    	return relaiscolis_RelaiscolismodeService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_relaiscolis/relaiscolismode');
	}
}