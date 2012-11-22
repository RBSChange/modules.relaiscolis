<?php
/**
 * relaiscolis_RelaiscolisModeConfigurationAction
 * @package modules.relaiscolis.lib.blocks
 */
class relaiscolis_BlockRelaiscolisModeConfigurationAction extends shipping_BlockRelayModeConfigurationAction
{
	/**
	 * Return the list of shipping_Relay
	 * @return shipping_Relay[]
	 */
	protected function buildRelayList()
	{
		$webserviceUrl = Framework::getConfigurationValue('modules/relaiscolis/webserviceUrl');
		$mode = $this->param['mode'];
		
		$clientOptions = array('encoding' => 'utf-8', 'trace' => true);
		$soapClient = new SoapClient($webserviceUrl . '?wsdl', $clientOptions);
		
		$authheader = '
			<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
			<wsse:UsernameToken>
			<wsse:Username>' . htmlspecialchars($mode->getUsername()) . '</wsse:Username>
			<wsse:Password>' . htmlspecialchars($mode->getPassword()) . '</wsse:Password>
			</wsse:UsernameToken>
			</wsse:Security>';
		
		$authvars = new SoapVar($authheader, XSD_ANYXML);
		$header = new SoapHeader("http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd", "Security", $authvars);
		$soapClient->__setSOAPHeaders(array($header));
		
		$params = array('zipCode' => $this->param['zipcode'], 'city' => $this->param['city']);
		
		$resultSoap = $soapClient->dropOffPoints($params);
		$result = $resultSoap->DropOffPoint;
		
		$relays = array();
		foreach ($result as $item)
		{
			$relay = relaiscolis_RelaiscolismodeService::getInstance()->getRelayFromSoapObject($item);
			$relays[] = $relay;
		}
		return $relays;
	}
}