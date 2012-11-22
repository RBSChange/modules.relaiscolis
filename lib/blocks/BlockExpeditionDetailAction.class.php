<?php
/**
 * relaiscolis_BlockExpeditionDetailAction
 * @package modules.relaiscolis.lib.blocks
 */
class relaiscolis_BlockExpeditionDetailAction extends shipping_BlockExpeditionDetailAction
{
	/**
	 * Initialize $this->param
	 */
	protected function init()
	{
		$shippingAdress = $this->expedition->getAddress();
		$shippingMode = $this->expedition->getShippingMode();
		
		$this->param['relayCode'] = $shippingAdress->getLabel();
		$this->param['countryCode'] = $shippingAdress->getCountryCode();
		$this->param['lang'] = strtoupper($this->getContext()->getLang());
		
		$webserviceUrl = Framework::getConfigurationValue('modules/relaiscolis/webserviceUrl');
		
		$clientOptions = array('encoding' => 'utf-8', 'trace' => true);
		$soapClient = new SoapClient($webserviceUrl . '?wsdl', $clientOptions);
		
		$authheader = '
			<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
			<wsse:UsernameToken>
			<wsse:Username>' . htmlspecialchars($shippingMode->getUsername()) . '</wsse:Username>
			<wsse:Password>' . htmlspecialchars($shippingMode->getPassword()) . '</wsse:Password>
			</wsse:UsernameToken>
			</wsse:Security>';
		
		$authvars = new SoapVar($authheader, XSD_ANYXML);
		$header = new SoapHeader("http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd", "Security", $authvars);
		$soapClient->__setSOAPHeaders(array($header));
		$this->param['soapClient'] = $soapClient;
	}
	
	/**
	 * @return shipping_Relay
	 */
	protected function getRelayDetail()
	{
		$relay = null;
		
		$soapClient = $this->param['soapClient'];
		$params = array('xETTCode' => $this->param['relayCode']);
		
		return $relay;
	}
	
	/**
	 * @param string $trackingNumber
	 * @return array
	 */
	protected function getTrackingDetail($trackingNumber)
	{
		$result = array();
		
		$ls = LocaleService::getInstance();
		$soapClient = $this->param['soapClient'];
		
		$params = array('parcelNumber' => $trackingNumber);
		$resultSoap = $soapClient->trackingByConsignment($params);
		
		$result['steps'] = array();
		
		$events = $resultSoap->Parcel->events;
		
		if ($events->requestDate)
		{
			$dateEvent = date_Calendar::getInstance($events->requestDate);
			$step = array();
			$step['label'] = $ls->transFO('m.relaiscolis.general.request', array('ucf'));
			$step['date'] = date_Formatter::format($dateEvent, 'd/m/Y');
			$step['hour'] = date_Formatter::format($dateEvent, 'H:i');
			$step['place'] = '';
			$result['steps'][] = $step;
		}
		
		if ($events->processDate)
		{
			$dateEvent = date_Calendar::getInstance($events->processDate);
			$step = array();
			$step['label'] = $ls->transFO('m.relaiscolis.general.process', array('ucf'));
			$step['date'] = date_Formatter::format($dateEvent, 'd/m/Y');
			$step['hour'] = date_Formatter::format($dateEvent, 'H:i');
			$step['place'] = $events->processCenter;
			$result['steps'][] = $step;
		}
		
		if ($events->arrivalDate)
		{
			$dateEvent = date_Calendar::getInstance($events->arrivalDate);
			$step = array();
			$step['label'] = $ls->transFO('m.relaiscolis.general.arrival', array('ucf'));
			$step['date'] = date_Formatter::format($dateEvent, 'd/m/Y');
			$step['hour'] = date_Formatter::format($dateEvent, 'H:i');
			$step['place'] = $events->arrivalCenter;
			$result['steps'][] = $step;
		}
		
		if ($events->deliveryDepartureDate)
		{
			$dateEvent = date_Calendar::getInstance($events->deliveryDepartureDate);
			$step = array();
			$step['label'] = $ls->transFO('m.relaiscolis.general.delivery-departure', array('ucf'));
			$step['date'] = date_Formatter::format($dateEvent, 'd/m/Y');
			$step['hour'] = date_Formatter::format($dateEvent, 'H:i');
			$step['place'] = $events->deliveryDepartureCenter;
			$result['steps'][] = $step;
		}
		
		if ($events->deliveryDate)
		{
			$dateEvent = date_Calendar::getInstance($events->deliveryDate);
			$step = array();
			$step['label'] = $ls->transFO('m.relaiscolis.general.delivery', array('ucf'));
			$step['date'] = date_Formatter::format($dateEvent, 'd/m/Y');
			$step['hour'] = date_Formatter::format($dateEvent, 'H:i');
			$step['place'] = '';
			$result['steps'][] = $step;
		}
		
		return $result;
	}
}