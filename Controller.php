<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @version $Id: Controller.php 4336 2011-04-06 01:52:11Z matt $
 *
 * @category Piwik_Plugins
 * @package Piwik_BeeLikedDBIP
 */

namespace Piwik\Plugins\BeeLikedDBIP;

use DBIP\Client;
use DBIP\Client_Exception;
use Piwik\Common;
use Piwik\Nonce;
use Piwik\Notification\Manager as NotificationManager;
use Piwik\Piwik;
use Piwik\Plugins\Marketplace\Api\Exception;
use Piwik\Site;
use Piwik\Plugins\LanguagesManager\LanguagesManager;
use Piwik\View;
#use Piwik\Plugins\SitesManager\API as APISitesManager;
use Piwik\Plugins\BeeLikedDBIP\API as APIDBIP;
use Piwik\Menu\MenuAdmin;
use Piwik\Menu\MenuTop;


class Controller extends \Piwik\Plugin\Controller
{
	public function config($siteId = 0, $errors = array()) {
		Piwik::checkUserHasSuperUserAccess();

		if ($siteId == 0) {
			$siteId = Common::getRequestVar('idSite');
		}

		$saved = (empty($errors) && Common::getRequestVar('submit', '')) ? : false;

		$apiKey = (Common::getRequestVar('apiKey', '')) ? trim(Common::getRequestVar('apiKey', '')) : APIDBIP::getAPIKey();

		$view = new View('@BeeLikedDBIP/config');
		$view->language = LanguagesManager::getLanguageCodeForCurrentUser();

		$this->setBasicVariablesView($view);
		$view->defaultReportSiteName = Site::getNameFor($siteId);
		$view->assign('idSite', $siteId);
		$view->assign('saved', $saved);
		$view->assign('errors', $errors);

		$view->assign('apiKey', $apiKey == 'free' ? '' : $apiKey);
        
        try {
            require_once PIWIK_INCLUDE_PATH . '/plugins/BeeLikedDBIP/vendor/dbip-client.class.php';
            $dbIpClient = new Client($apiKey);
            $result = $dbIpClient->Get_Key_Info();
            $view->assign('infoQueriesLeft', $result->queriesLeft);
            if ($apiKey !== 'free') {
                $view->assign('infoQueriesPerDay', $result->queriesPerDay);
                $view->assign('infoStatus', $result->status);
                $view->assign('infoType', 'commercial');
            }
            else {
                $view->assign('infoType', 'free');
            }
            
        } catch (Client_Exception $ex)
        {
            $view->assign('infoError', $ex->getMessage());
            $view->assign('infoType', 'error');
        }

		$view->nonce = Nonce::getNonce('BeeLikedDBIP.saveConfig');
		$view->adminMenu = MenuAdmin::getInstance()->getMenu();
		$view->topMenu = MenuTop::getInstance()->getMenu();
		$view->notifications = NotificationManager::getAllNotificationsToDisplay();
		$view->phpVersion = phpversion();
		$view->phpIsNewEnough = version_compare($view->phpVersion, '5.3.0', '>=');

		echo $view->render();
	}

	public function saveConfig() {
		try{
			Piwik::checkUserHasSuperUserAccess();
			$siteID = Common::getRequestVar('siteID', 0);
			if ($siteID == 0) {
				$siteID = Common::getRequestVar('idSite');
			}

	  		$errors = [];

			$apiKey = trim(Common::getRequestVar('apiKey', ''));

			if (!APIDBIP::getAPIKeyInfo($apiKey)) {
				$errors[] = Piwik::translate('BeeLikedDBIP_PleaseEnterAValidAPIKey');
			}

			if (empty($errors)) {
                APIDBIP::setAPIKey($apiKey);
			}

			$this->config($siteID, $errors);

		} catch(Exception $e ) {
			echo $e;
		}
	}
}
