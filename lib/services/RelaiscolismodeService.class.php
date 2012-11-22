<?php
/**
 * relaiscolis_RelaiscolismodeService
 * @package modules.relaiscolis
 */
class relaiscolis_RelaiscolismodeService extends shipping_RelayModeService
{
	/**
	 * @var relaiscolis_RelaiscolismodeService
	 */
	private static $instance;
	
	/**
	 * @return relaiscolis_RelaiscolismodeService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}
	
	/**
	 * @return relaiscolis_persistentdocument_relaiscolismode
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_relaiscolis/relaiscolismode');
	}
	
	/**
	 * Create a query based on 'modules_relaiscolis/relaiscolismode' model.
	 * Return document that are instance of modules_relaiscolis/relaiscolismode,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_relaiscolis/relaiscolismode');
	}
	
	/**
	 * Create a query based on 'modules_relaiscolis/relaiscolismode' model.
	 * Only documents that are strictly instance of modules_relaiscolis/relaiscolismode
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_relaiscolis/relaiscolismode', false);
	}
	
	/**
	 * @param mondialrelay_persistentdocument_mondialrelaymode $mode
	 * @param order_CartInfo $cart
	 * @return string[]|false
	 */
	public function getConfigurationBlockForCart($mode, $cart)
	{
		return array('relaiscolis', 'RelaiscolisModeConfiguration');
	}
	
	/**
	 * 
	 */
	protected function getDetailExpeditionPageTagName()
	{
		return 'contextual_website_website_modules_relaiscolis_relaiscolisexpedition';
	}
	
	/**
	 * Construct a shipping_Relay from soap object
	 * @param object $soapObject
	 */
	public function getRelayFromSoapObject($soapObject)
	{
		$relay = new shipping_Relay();
		
		$relay->setRef($soapObject->xETTCode);
		$relay->setName($soapObject->name);
		$relay->setAddressLine1($soapObject->address1);
		$relay->setZipCode($soapObject->zipCode);
		$relay->setCity($soapObject->city);
		
		$openingHours = array();
		$openingHours[] = $this->extractOpeningHour($soapObject->openingHours->monday);
		$openingHours[] = $this->extractOpeningHour($soapObject->openingHours->tuesday);
		$openingHours[] = $this->extractOpeningHour($soapObject->openingHours->wednesday);
		$openingHours[] = $this->extractOpeningHour($soapObject->openingHours->thursday);
		$openingHours[] = $this->extractOpeningHour($soapObject->openingHours->friday);
		$openingHours[] = $this->extractOpeningHour($soapObject->openingHours->saturday);
		$openingHours[] = $this->extractOpeningHour($soapObject->openingHours->sunday);
		$relay->setOpeningHours($openingHours);
		
		$relay->setLatitude($soapObject->latitude);
		$relay->setLongitude($soapObject->longitude);
		
		return $relay;
	}
	
	/**
	 * Extract opening hours from raw hours data
	 * @param object $hours
	 * @return string
	 */
	protected function extractOpeningHour($hoursSoap)
	{
		$ls = LocaleService::getInstance();
		$result = '';
		
		$am = $hoursSoap->am;
		$pm = $hoursSoap->pm;
		
		$amArray = split('-', $am);
		$pmArray = split('-', $pm);
		
		$hours = array();
		
		if (count($amArray) == 1)
		{
			$hours[0] = '00:00';
			$hours[1] = '00:00';
		}
		else
		{
			$hours[0] = trim($amArray[0]);
			$hours[1] = trim($amArray[1]);
		}
		
		if (count($pmArray) == 1)
		{
			$hours[2] = '00:00';
			$hours[3] = '00:00';
		}
		else
		{
			$hours[2] = trim($pmArray[0]);
			$hours[3] = trim($pmArray[1]);
		}
		
		if ($hours[0] == '00:00' && $hours[2] != '00:00')
		{
			$hours[0] = $hours[2];
			$hours[1] = $hours[3];
		}
		
		if ($hours[0] == '00:00' && $hours[2] == '00:00')
		{
			$result = $ls->transFO('m.shipping.general.closed');
		}
		else
		{
			$result = $ls->transFO('m.shipping.general.opening-hours', array('ucf'), array('hour1' => $hours[0], 
				'hour2' => $hours[1]));
			if ($hours[2] != '00:00')
			{
				$result .= ' ';
				$result .= $ls->transFO('m.shipping.general.and');
				$result .= ' ';
				$result .= $ls->transFO('m.shipping.general.opening-hours', array(), array('hour1' => $hours[2], 'hour2' => $hours[3]));
			}
		}
		
		return $result;
	}
}