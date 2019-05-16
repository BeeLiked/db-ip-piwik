<?php
namespace Piwik\Plugins\BeeLikedDBIP;

use Piwik\Common;
use Piwik\Db;
#use Piwik\Plugins\SitesManager\API as APISitesManager;
use Piwik\Plugins\UserCountry\LocationProvider;
#use Piwik\Plugins\BeeLikedDBIP\API as DBIPAPI;

class BeeLikedDBIP extends \Piwik\Plugin
{
	/**
     * @see Piwik\Plugin::registerEvents
     */
    public function registerEvents()
    {
        return array(
            'Tracker.setTrackerCacheGeneral' => 'setTrackerCacheGeneral',
        );
    }

	public function setTrackerCacheGeneral(&$cache)
    {
        $cache['currentLocationProviderId'] = LocationProvider::getCurrentProviderId();
    }
}
