<?php
namespace DreadLabs\Dbmigrate\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Thomas Juhnke (tommy@van-tomas.de)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * ExtensionManagement.php
 *
 * Provides extension management helpers for usage in ext_localconf/ext_tables.
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class ExtensionManagement implements \TYPO3\CMS\Core\SingletonInterface {

	protected static $shippedConfiguration = 'Configuration/Global/config.php';

	protected static $instanceConfiguration = 'dbmigrate_config.php';

	protected static $ajaxControllers = array(
#		'tx_dbmigrate::is_logging_disabled' => 'EXT:dbmigrate/Classes/Controller/Toolbar.php:Tx_Dbmigrate_Controller_Toolbar->isLoggingDisabled',
	);

	public static function loadConfiguration() {
		$defaultConfiguration = ExtensionManagementUtility::extPath('dbmigrate', self::$shippedConfiguration);
		include_once($defaultConfiguration);

		$instanceConfiguration = PATH_typo3conf . self::$instanceConfiguration;

		if (TRUE === @file_exists($instanceConfiguration)) {
			include_once($instanceConfiguration);
		}
	}

	public static function addAjaxControllers() {
		foreach (self::$ajaxControllers as $ajaxId => $controllerReference) {
			$GLOBALS['TYPO3_CONF_VARS']['BE']['AJAX'][$ajaxId] = $controllerReference;
		}
	}

	public static function addToolbarItem($_EXTKEY) {
		$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] = ExtensionManagementUtility::extPath($_EXTKEY, 'Classes/Backend/Toolbar.php');
	}

	public static function addTCEMainHooks($_EXTKEY) {
		$hookClass = ExtensionManagementUtility::extPath($_EXTKEY, 'Classes/Database/TceMainTransactionHandler.php:DreadLabs\\Dbmigrate\\Database\\TceMainTransactionHandler');

		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = $hookClass;
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = $hookClass;
	}

	public static function addQueryProcessors() {
		$preProcessor = 'EXT:dbmigrate/Classes/Database/QueryPreProcessor.php:DreadLabs\\Dbmigrate\\Database\\QueryPreProcessor';
		$postProcessor = 'EXT:dbmigrate/Classes/Database/QueryPostProcessor.php:DreadLabs\\Dbmigrate\\Database\\QueryPostProcessor';

		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'][] = $preProcessor;
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'][] = $postProcessor;
	}
}
?>