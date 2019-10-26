<?php
namespace Piwik\Plugins\BeeLikedDBIP\LocationProvider;

use DBIP\Client_Exception;
use Piwik\Piwik;
use Piwik\Option;
use Piwik\Http;
use Piwik\Plugins\BeeLikedDBIP\API;
use Piwik\Plugins\Marketplace\Api\Exception;
use Piwik\Plugins\UserCountry\LocationProvider;

class BeeLikedDBIP extends LocationProvider
{
	const ID = 'beelikeddbip';
	const TITLE = 'BeeLikedDBIP';

	public function __construct()
	{
	}

	/**
	 * Returns information about this location provider.
	 *
	 * @return array
	 */
	public function getInfo()
	{
    $description = Piwik::translate('BeeLikedDBIP_LocationProviderInfoDescription', array('<b>DB-IP</b>', '<br><br>', '<b>', '</b>', Option::get('BeeLikedDBIP.APIKey'), '<br><br>', Piwik::translate('BeeLikedDBIP_Disclaimer')));
	   
	  if (Option::get('BeeLikedDBIP.APIKey')) {
      $extraMessage = Piwik::translate('BeeLikedDBIP_LocationProviderInfoExtraMessageCommercialPlan', array(''));
    } else {
      $extraMessage = Piwik::translate('BeeLikedDBIP_LocationProviderInfoExtraMessageFreePlan', array(''));
    }
	   
		return array(
			'id'			=> self::ID,
			'title'			=> Piwik::translate('BeeLikedDBIP_LocationProviderInfoTitle'),
			'order'			=> 5,
			'description'   => $description,
			'extra_message'	=> $extraMessage,
		);
	}

	/**
	 * Get a visitor's location based on their IP address.
	 *
	 * @param array $info Must have an 'ip' field.
	 * @return array
	 */
	public function getLocation($info)
	{
		$ip = $this->getIpFromInfo($info);
		$result = $this->getDbIpAddressInfo($ip);
  	$this->completeLocationResult($result);
		return $result;
	}
 
	public function getDbIpAddressInfo($ip)
    {
        $result = API::getIPAddressInfo($ip);

        $location = array(
            self::CONTINENT_CODE_KEY => $result->continentCode,
            self::CONTINENT_NAME_KEY => $result->continentName,
            self::COUNTRY_CODE_KEY => $result->countryCode,
            self::COUNTRY_NAME_KEY => $result->countryName,
            self::REGION_NAME_KEY => $result->stateProv,
            self::CITY_NAME_KEY => $result->city,
        );

        if (isset($result->isp)) {
          $location[self::ISP_KEY] = $result->isp;
        }
        if (isset($result->organization)) {
          $location[self::ORG_KEY] = $result->organization;
        }
        if (isset($result->zipCode)) {
          $location[self::POSTAL_CODE_KEY] = $result->zipCode;
        }
        if (isset($result->latitude)) {
          $location[self::LATITUDE_KEY] = $result->latitude;
        }
        if (isset($result->longitude)) {
          $location[self::LONGITUDE_KEY] = $result->longitude;
        }
        if (isset($result->phonePrefix)) {
          if (is_array($result->phonePrefix)) {
            $location[self::AREA_CODE_KEY] = count($result->phonePrefix) > 0 ? $result->phonePrefix[0] : null;
          } else {
            $location[self::AREA_CODE_KEY] = $result->phonePrefix;
          }
        }
        
        return $location;
    }
	
	/**
	 * Returns an array describing the types of location information this provider will
	 * return.
	 *
	 * @return array
	 */
	public function getSupportedLocationInfo()
	{
		$result = array();

		// Country & continent information always available
		$result[self::COUNTRY_CODE_KEY] = true;
    $result[self::COUNTRY_NAME_KEY] = true;
		$result[self::CONTINENT_CODE_KEY] = true;
    $result[self::CONTINENT_NAME_KEY] = true;

    $response = $this->getDbIpAddressInfo('8.8.8.8');
		
		if (isset($response->stateProv)) {
			$result[self::REGION_NAME_KEY] = true;
		}

		if (isset($response->city)) {
			$result[self::CITY_NAME_KEY] = true;
		}

		if (isset($response->latitude)) {
			$result[self::LATITUDE_KEY] = true;
			$result[self::LONGITUDE_KEY] = true;
		}

		if (isset($response->isp)) {
			$result[self::ISP_KEY] = true;
		}
		
    if (isset($response->organization)) {
      $result[self::ORG_KEY] = true;
    }
    
    if (isset($response->zipCode)) {
      $result[self::POSTAL_CODE_KEY] = true;
    }
    
    if (isset($response->phonePrefix)) {
      $result[self::AREA_CODE_KEY] = true;
    }

		return $result;
	}

	/**
	 * Returns true if this location provider is available.
	 *
	 * @return bool
	 */
	public function isAvailable()
	{
		return true;
	}

	/**
	 * Returns true if this provider has been setup correctly, the error message if
	 * otherwise.
	 *
	 * @return bool|string
	 */
	public function isWorking()
	{
        try {
            $response = $this->getDbIpAddressInfo('8.8.8.8');
        }
        catch (Client_Exception $ex) {
            return 'The DB-IP API Key is invalid.';
        }
        
		if (!isset($response[self::COUNTRY_CODE_KEY])) {
			return 'The DB-IP database file is corrupted.';
		}

		return true;
	}
}
