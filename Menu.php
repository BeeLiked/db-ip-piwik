<?php
namespace Piwik\Plugins\BeeLikedDBIP;

use Piwik\Menu\MenuAdmin;
use Piwik\Piwik;

class Menu extends \Piwik\Plugin\Menu
{

    public function configureAdminMenu(MenuAdmin $menu)
    {
    		if (Piwik::isUserHasSomeAdminAccess()){
    			$menu->addSystemItem(
    				'DB-IP',
    				array('module' => 'BeeLikedDBIP', 'action' => 'config'),
    				$orderId = 35);
          }
    }
}
