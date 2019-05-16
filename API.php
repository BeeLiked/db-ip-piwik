<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @version $Id: API.php 4448 2011-04-14 08:20:49Z matt $
 *
 * @category Piwik_Plugins
 * @package Piwik_BeeLikedDBIP
 */
namespace Piwik\Plugins\BeeLikedDBIP;

use DBIP\Client;
use DBIP\Client_Exception;
use Piwik\DataTable\Row;
use Piwik\Db;
use Piwik\Common;
use Piwik\DataTable;
use Piwik\Plugins\Marketplace\Api\Exception;
use Piwik\Site;
use Piwik\Date;
use Piwik\Piwik;
use Piwik\Option;
use Piwik\Http;

/**
 * @package Piwik_BeeLikedDBIP
 */
class API extends \Piwik\Plugin\API
{
	static private $instance = null;

	static public function getInstance()
	{
		if (self::$instance == null)
		{
			self::$instance = new self;
		}
		return self::$instance;
	}

	static public function getAPIKey()
	{
		return (Option::get('BeeLikedDBIP.APIKey')) ? Option::get('BeeLikedDBIP.APIKey') : 'free';
	}

	static public function setAPIKey($value)
	{
		Option::set('BeeLikedDBIP.APIKey', $value);
	}
 
	static public function setPreferredLanguage($lang)
    {
        $apiKey = self::getAPIKey();
    
        require_once PIWIK_INCLUDE_PATH . '/plugins/BeeLikedDBIP/vendor/dbip-client.class.php';
        $dbIpClient = new Client($apiKey);
        $dbIpClient->Set_Preferred_Language($lang);
    }
	
	static public function getAPIKeyInfo($apiKey = '')
	{
		if (!$apiKey)
			$apiKey = self::getAPIKey();
   
		try
        {
            require_once PIWIK_INCLUDE_PATH.'/plugins/BeeLikedDBIP/vendor/dbip-client.class.php';
            $dbIpClient = new Client($apiKey);
            $response = $dbIpClient->Get_Key_Info();
        } catch (Client_Exception $ex) {
		    
        }

		return $response;
	}
	
	static public function getIPAddressInfo($ip)
    {
        require_once PIWIK_INCLUDE_PATH . '/plugins/BeeLikedDBIP/vendor/dbip-client.class.php';
    
        $dbIpClient = new Client(Option::get('BeeLikedDBIP.APIKey'));
        $result = $dbIpClient->Get_Address_Info($ip);
    
        return $result;
    }
}
